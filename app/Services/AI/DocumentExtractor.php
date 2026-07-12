<?php

namespace App\Services\AI;

use App\Models\EmissionFactor;
use App\Models\UploadedDocument;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * DocumentExtractor
 *
 * Service d'extraction de données à partir de documents (factures, tickets, etc.)
 * Utilise Claude Vision pour l'analyse d'images et de PDFs.
 */
class DocumentExtractor
{
    protected AIManager $aiManager;

    protected FactorRAGService $factorRAG;

    /**
     * Extraction prompt template for invoices.
     */
    protected string $invoicePrompt = <<<'PROMPT'
Tu es un expert en extraction de données de factures pour le calcul d'empreinte carbone.

Analyse ce document et extrais les informations suivantes au format JSON:

{
    "document_type": "invoice|energy_bill|fuel_receipt|transport_invoice|purchase_order|expense_report|other",
    "supplier_name": "Nom du fournisseur",
    "supplier_address": "Adresse complète",
    "invoice_number": "Numéro de facture",
    "date": "YYYY-MM-DD",
    "due_date": "YYYY-MM-DD ou null",
    "total_amount": 123.45,
    "total_ht": 100.00,
    "tva_amount": 23.45,
    "currency": "EUR",
    "line_items": [
        {
            "description": "Description du produit/service",
            "quantity": 100,
            "unit": "kWh|L|kg|m3|km|unit",
            "unit_price": 0.15,
            "amount": 15.00,
            "emission_category": "energy|fuel|transport|goods|services|waste|other"
        }
    ],
    "energy_data": {
        "consumption_kwh": null,
        "consumption_m3": null,
        "period_start": "YYYY-MM-DD",
        "period_end": "YYYY-MM-DD",
        "meter_number": null,
        "energy_type": "electricity|gas|other"
    },
    "suggested_category": "Code catégorie GHG (ex: 1.1, 2.1, 3.1)",
    "suggested_scope": 1|2|3,
    "confidence_notes": "Notes sur la qualité de l'extraction"
}

Instructions importantes:
- Extrais TOUTES les lignes de la facture
- Pour les factures d'énergie, extrais les données de consommation
- Identifie le scope GHG approprié:
  * Scope 1: combustion directe (gaz, fioul, carburant véhicules de l'entreprise)
  * Scope 2: électricité, chaleur/froid achetés
  * Scope 3: achats, transport, déchets, déplacements professionnels
- Si une information n'est pas trouvée, utilise null
- Les montants doivent être des nombres, pas des chaînes
- La date doit être au format ISO (YYYY-MM-DD)
PROMPT;

    /**
     * Extraction prompt for fuel receipts.
     */
    protected string $fuelPrompt = <<<'PROMPT'
Tu es un expert en extraction de données de tickets carburant pour le calcul d'empreinte carbone.

Analyse ce ticket/facture carburant et extrais les informations suivantes au format JSON:

{
    "document_type": "fuel_receipt",
    "station_name": "Nom de la station",
    "station_address": "Adresse",
    "date": "YYYY-MM-DD",
    "time": "HH:MM",
    "fuel_type": "diesel|essence_sp95|essence_sp98|e10|e85|gpl",
    "quantity_liters": 45.5,
    "price_per_liter": 1.85,
    "total_amount": 84.18,
    "vehicle_info": {
        "plate_number": null,
        "mileage_km": null
    },
    "payment_method": "cb|especes|carte_entreprise",
    "suggested_category": "1.1",
    "suggested_scope": 1,
    "suggested_factor": {
        "name": "Nom du facteur d'émission",
        "unit": "L",
        "emission_factor": 2.49
    },
    "confidence_notes": "Notes sur la qualité de l'extraction"
}

Instructions:
- Le type de carburant est crucial pour le calcul des émissions
- Identifie le scope: généralement Scope 1 (combustion directe) pour véhicules de l'entreprise
- Si c'est une facture de remboursement de frais kilométriques, ce sera du Scope 3
PROMPT;

    /**
     * Extraction prompt for transport invoices.
     */
    protected string $transportPrompt = <<<'PROMPT'
Tu es un expert en extraction de données de factures de transport pour le calcul d'empreinte carbone.

Analyse cette facture de transport et extrais les informations suivantes au format JSON:

{
    "document_type": "transport_invoice",
    "carrier_name": "Nom du transporteur",
    "carrier_address": "Adresse",
    "invoice_number": "Numéro de facture",
    "date": "YYYY-MM-DD",
    "transport_type": "road|rail|air|sea|courier",
    "shipments": [
        {
            "origin": "Ville/Pays de départ",
            "destination": "Ville/Pays d'arrivée",
            "distance_km": 500,
            "weight_kg": 100,
            "volume_m3": null,
            "description": "Description des marchandises",
            "amount": 150.00
        }
    ],
    "total_amount": 150.00,
    "total_distance_km": 500,
    "total_weight_kg": 100,
    "suggested_category": "3.4",
    "suggested_scope": 3,
    "suggested_factor": {
        "name": "Transport routier de marchandises",
        "unit": "t.km",
        "emission_factor": 0.083
    },
    "confidence_notes": "Notes sur la qualité de l'extraction"
}

Instructions:
- Identifie le mode de transport (routier, ferroviaire, aérien, maritime)
- Calcule la distance si non indiquée (estimation entre villes)
- Le scope est généralement 3 (transport amont/aval)
- L'unité d'émission est souvent en t.km (tonne-kilomètre)
PROMPT;

    public function __construct(AIManager $aiManager, FactorRAGService $factorRAG)
    {
        $this->aiManager = $aiManager;
        $this->factorRAG = $factorRAG;
    }

    /**
     * Extract data from an uploaded document.
     *
     * @return array{
     *     success: bool,
     *     data: array|null,
     *     confidence: float,
     *     error: string|null,
     *     processing_time_ms: int
     * }
     */
    public function extract(UploadedDocument $document): array
    {
        $startTime = microtime(true);

        try {
            // Get document content
            $content = $this->getDocumentContent($document);

            if (!$content) {
                return $this->errorResult('Impossible de lire le contenu du document', $startTime);
            }

            // Select appropriate prompt based on document type
            $prompt = $this->getPromptForType($document->document_type);

            // Call AI for extraction
            $result = $this->callAIExtraction($content, $prompt, $document);

            if (!$result) {
                return $this->errorResult('Échec de l\'extraction IA', $startTime);
            }

            // Enhance with emission factor suggestions
            $result = $this->enhanceWithFactors($result);

            // Calculate confidence score
            $confidence = $this->calculateConfidence($result);

            return [
                'success' => true,
                'data' => $result,
                'confidence' => $confidence,
                'error' => null,
                'processing_time_ms' => $this->getElapsedMs($startTime),
            ];

        } catch (\Exception $e) {
            Log::error('Document extraction failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResult($e->getMessage(), $startTime);
        }
    }

    /**
     * Get document content for AI processing.
     *
     * @return array{type: string, content: string}|null
     */
    protected function getDocumentContent(UploadedDocument $document): ?array
    {
        if ($document->isImage()) {
            // Return base64 image for Vision API
            $base64 = $document->getFileBase64();

            return $base64 ? [
                'type' => 'image',
                'content' => $base64,
                'mime_type' => $document->mime_type,
            ] : null;
        }

        if ($document->isPdf()) {
            // Try to extract text from PDF
            $text = $this->extractTextFromPdf($document);

            if ($text && strlen($text) > 100) {
                // PDF has enough text content
                return [
                    'type' => 'text',
                    'content' => $text,
                ];
            }

            // PDF is likely scanned, use Vision API
            $base64 = $document->getFileBase64();

            return $base64 ? [
                'type' => 'pdf',
                'content' => $base64,
                'mime_type' => 'application/pdf',
            ] : null;
        }

        if ($document->isExcel() || $document->isCsv()) {
            // Parse spreadsheet
            $data = $this->parseSpreadsheet($document);

            return $data ? [
                'type' => 'text',
                'content' => $data,
            ] : null;
        }

        return null;
    }

    /**
     * Extract text from PDF.
     */
    protected function extractTextFromPdf(UploadedDocument $document): ?string
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseFile($document->getFilePath());

            return $pdf->getText();
        } catch (\Exception $e) {
            Log::warning('PDF text extraction failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Parse spreadsheet content.
     */
    protected function parseSpreadsheet(UploadedDocument $document): ?string
    {
        try {
            $filePath = $document->getFilePath();

            if ($document->isCsv()) {
                $content = file_get_contents($filePath);

                return $content ?: null;
            }

            // For Excel, use PhpSpreadsheet if available
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();

                $output = [];
                foreach ($sheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $output[] = implode("\t", $rowData);
                }

                return implode("\n", $output);
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Spreadsheet parsing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get appropriate prompt for document type.
     */
    protected function getPromptForType(string $documentType): string
    {
        return match ($documentType) {
            UploadedDocument::TYPE_FUEL_RECEIPT => $this->fuelPrompt,
            UploadedDocument::TYPE_TRANSPORT_INVOICE => $this->transportPrompt,
            default => $this->invoicePrompt,
        };
    }

    /**
     * Call AI for extraction.
     */
    protected function callAIExtraction(array $content, string $prompt, UploadedDocument $document): ?array
    {
        if (!$this->aiManager->isAvailable()) {
            throw new \RuntimeException('Aucun fournisseur IA configuré');
        }

        // Build messages based on content type
        if ($content['type'] === 'image' || $content['type'] === 'pdf') {
            // Use Vision API
            $messages = [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $content['mime_type'],
                                'data' => $content['content'],
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                ],
            ];

            $response = $this->aiManager->vision($messages);
        } else {
            // Text-based extraction
            $messages = [
                [
                    'role' => 'user',
                    'content' => "Voici le contenu du document:\n\n{$content['content']}\n\n{$prompt}",
                ],
            ];

            $response = $this->aiManager->json($messages[0]['content']);

            if ($response) {
                return $response;
            }

            return null;
        }

        if (!$response) {
            return null;
        }

        // Parse JSON from response
        return $this->parseJsonResponse($response);
    }

    /**
     * Parse JSON from AI response.
     */
    protected function parseJsonResponse(string $response): ?array
    {
        // Try direct JSON parse
        $data = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        // Try to extract JSON from markdown code block
        if (preg_match('/```(?:json)?\s*(\{[\s\S]*?\})\s*```/', $response, $matches)) {
            $data = json_decode($matches[1], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        // Try to find JSON object in response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            $data = json_decode($matches[0], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        Log::warning('Failed to parse JSON from AI response', [
            'response' => substr($response, 0, 500),
        ]);

        return null;
    }

    /**
     * Enhance extraction with emission factor suggestions.
     */
    protected function enhanceWithFactors(array $data): array
    {
        // If we already have a suggested factor, try to match it
        if (!empty($data['suggested_factor']['name'])) {
            $factors = $this->factorRAG->search($data['suggested_factor']['name'], [
                'scope' => $data['suggested_scope'] ?? null,
            ]);

            if ($factors->isNotEmpty()) {
                $factor = $factors->first();
                $data['matched_factor'] = [
                    'id' => $factor->id,
                    'name' => $factor->translated_name,
                    'factor_kg_co2e' => $factor->factor_kg_co2e,
                    'unit' => $factor->unit,
                    'source' => $factor->source,
                ];
            }
        }

        // Enhance line items with factor suggestions
        if (!empty($data['line_items'])) {
            foreach ($data['line_items'] as $index => $item) {
                if (!empty($item['description'])) {
                    $factors = $this->factorRAG->search($item['description'], [
                        'unit' => $item['unit'] ?? null,
                    ]);

                    if ($factors->isNotEmpty()) {
                        $factor = $factors->first();
                        $data['line_items'][$index]['suggested_factor'] = [
                            'id' => $factor->id,
                            'name' => $factor->translated_name,
                            'factor_kg_co2e' => $factor->factor_kg_co2e,
                            'unit' => $factor->unit,
                        ];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Calculate confidence score based on extraction quality.
     */
    protected function calculateConfidence(array $data): float
    {
        $score = 0.0;
        $weights = [
            'document_type' => 0.1,
            'supplier_name' => 0.15,
            'date' => 0.15,
            'total_amount' => 0.15,
            'line_items' => 0.2,
            'suggested_category' => 0.1,
            'matched_factor' => 0.15,
        ];

        foreach ($weights as $field => $weight) {
            if ($field === 'line_items') {
                if (!empty($data['line_items']) && count($data['line_items']) > 0) {
                    // Check quality of line items
                    $validItems = 0;
                    foreach ($data['line_items'] as $item) {
                        if (!empty($item['description']) && isset($item['amount'])) {
                            $validItems++;
                        }
                    }
                    $score += $weight * ($validItems / count($data['line_items']));
                }
            } elseif (isset($data[$field]) && $data[$field] !== null) {
                $score += $weight;
            }
        }

        return round($score, 2);
    }

    /**
     * Create error result.
     */
    protected function errorResult(string $message, float $startTime): array
    {
        return [
            'success' => false,
            'data' => null,
            'confidence' => 0.0,
            'error' => $message,
            'processing_time_ms' => $this->getElapsedMs($startTime),
        ];
    }

    /**
     * Get elapsed time in milliseconds.
     */
    protected function getElapsedMs(float $startTime): int
    {
        return (int) ((microtime(true) - $startTime) * 1000);
    }

    /**
     * Validate extracted data structure.
     */
    public function validateExtraction(array $data): array
    {
        $errors = [];

        if (empty($data['document_type'])) {
            $errors[] = 'Type de document non détecté';
        }

        if (empty($data['date'])) {
            $errors[] = 'Date non trouvée';
        } elseif (!$this->isValidDate($data['date'])) {
            $errors[] = 'Format de date invalide';
        }

        if (!isset($data['total_amount']) || !is_numeric($data['total_amount'])) {
            $errors[] = 'Montant total non trouvé ou invalide';
        }

        return $errors;
    }

    /**
     * Check if date string is valid.
     */
    protected function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);

        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Convert extracted data to emission record format.
     */
    public function toEmissionRecord(array $data, string $assessmentId): array
    {
        $emissions = [];

        // Process line items
        if (!empty($data['line_items'])) {
            foreach ($data['line_items'] as $item) {
                if (!empty($item['suggested_factor']['id'])) {
                    $factor = EmissionFactor::find($item['suggested_factor']['id']);

                    if ($factor) {
                        $quantity = $item['quantity'] ?? 1;
                        $emissionKg = $quantity * $factor->factor_kg_co2e;

                        $emissions[] = [
                            'assessment_id' => $assessmentId,
                            'name' => $item['description'] ?? 'Ligne extraite',
                            'emission_factor_id' => $factor->id,
                            'quantity' => $quantity,
                            'unit' => $item['unit'] ?? $factor->unit,
                            'emission_kg_co2e' => $emissionKg,
                            'scope' => $factor->scope,
                            'source_type' => 'document_extraction',
                            'source_document_date' => $data['date'] ?? null,
                        ];
                    }
                }
            }
        }

        // If no line items but we have a matched factor
        if (empty($emissions) && !empty($data['matched_factor']['id'])) {
            $factor = EmissionFactor::find($data['matched_factor']['id']);

            if ($factor) {
                $quantity = $data['energy_data']['consumption_kwh']
                    ?? $data['quantity_liters']
                    ?? $data['total_distance_km']
                    ?? 1;

                $emissionKg = $quantity * $factor->factor_kg_co2e;

                $emissions[] = [
                    'assessment_id' => $assessmentId,
                    'name' => $data['supplier_name'] ?? 'Document extrait',
                    'emission_factor_id' => $factor->id,
                    'quantity' => $quantity,
                    'unit' => $factor->unit,
                    'emission_kg_co2e' => $emissionKg,
                    'scope' => $factor->scope,
                    'source_type' => 'document_extraction',
                    'source_document_date' => $data['date'] ?? null,
                ];
            }
        }

        return $emissions;
    }
}
