<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\UploadedDocument;
use App\Services\AI\AIManager;
use App\Services\AI\DocumentExtractor;
use App\Services\AI\FactorRAGService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

/**
 * Unit tests for DocumentExtractor service - T141
 */
class DocumentExtractorTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validate_extraction_detects_missing_date(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'supplier_name' => 'Test',
            'total_amount' => 100,
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertContains('Date non trouvée', $errors);
    }

    public function test_validate_extraction_detects_invalid_date_format(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'date' => '15/01/2024',
            'total_amount' => 100,
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertContains('Format de date invalide', $errors);
    }

    public function test_validate_extraction_detects_missing_amount(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'date' => '2024-01-15',
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertContains('Montant total non trouvé ou invalide', $errors);
    }

    public function test_validate_extraction_passes_valid_data(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'date' => '2024-01-15',
            'total_amount' => 150.00,
            'supplier_name' => 'Test',
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertEmpty($errors);
    }

    public function test_validate_extraction_accepts_valid_iso_date(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'energy_bill',
            'date' => '2024-12-31',
            'total_amount' => 250.00,
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertNotContains('Format de date invalide', $errors);
        $this->assertNotContains('Date non trouvée', $errors);
    }

    public function test_validate_extraction_accepts_zero_amount(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'date' => '2024-01-15',
            'total_amount' => 0,
        ];

        $errors = $extractor->validateExtraction($data);

        // Zero is a valid numeric amount (credit notes, etc.)
        $this->assertNotContains('Montant total non trouvé ou invalide', $errors);
    }

    public function test_validate_extraction_accepts_negative_amount(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'invoice',
            'date' => '2024-01-15',
            'total_amount' => -50,
        ];

        $errors = $extractor->validateExtraction($data);

        // Negative amounts are valid (credit notes, refunds)
        $this->assertNotContains('Montant total non trouvé ou invalide', $errors);
    }

    public function test_validate_extraction_handles_null_values(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => null,
            'date' => null,
            'total_amount' => null,
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertContains('Date non trouvée', $errors);
        $this->assertContains('Montant total non trouvé ou invalide', $errors);
    }

    public function test_validate_extraction_handles_empty_array(): void
    {
        $extractor = $this->createExtractor();

        $errors = $extractor->validateExtraction([]);

        $this->assertContains('Date non trouvée', $errors);
        $this->assertContains('Montant total non trouvé ou invalide', $errors);
    }

    public function test_validate_extraction_with_fuel_receipt(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'fuel_receipt',
            'date' => '2024-06-15',
            'total_amount' => 85.50,
            'quantity_liters' => 45.5,
            'fuel_type' => 'diesel',
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertEmpty($errors);
    }

    public function test_validate_extraction_with_energy_bill(): void
    {
        $extractor = $this->createExtractor();

        $data = [
            'document_type' => 'energy_bill',
            'date' => '2024-03-01',
            'total_amount' => 320.00,
            'supplier_name' => 'EDF',
            'energy_data' => [
                'consumption_kwh' => 2500,
                'energy_type' => 'electricity',
            ],
        ];

        $errors = $extractor->validateExtraction($data);

        $this->assertEmpty($errors);
    }

    /**
     * Create a DocumentExtractor with mocked dependencies.
     */
    private function createExtractor(): DocumentExtractor
    {
        $aiManager = Mockery::mock(AIManager::class);
        $factorRAG = Mockery::mock(FactorRAGService::class);

        return new DocumentExtractor($aiManager, $factorRAG);
    }
}
