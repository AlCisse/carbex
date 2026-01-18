# ADR-0005: Architecture de Génération de Rapports

## Statut

Accepté

## Contexte

LinsCarbon doit générer des rapports de bilan carbone:
- Formats multiples: PDF, Excel, BEGES XML
- Personnalisation par template
- Génération potentiellement longue (>30s)
- Stockage et téléchargement ultérieur
- Historique des rapports générés

Approches considérées:
1. **Génération synchrone** - Attente pendant la génération
2. **Génération asynchrone** - Job en arrière-plan
3. **Service externe** - API de génération tierce
4. **Pré-génération** - Génération à intervalles réguliers

## Décision

Nous utilisons une **génération asynchrone** via Laravel Jobs avec stockage sur disque.

## Architecture

```
┌─────────────┐     ┌─────────────────┐     ┌─────────────┐
│   Livewire  │────▶│ GenerateReport  │────▶│   Storage   │
│  Component  │     │      Job        │     │    (S3)     │
└─────────────┘     └─────────────────┘     └─────────────┘
                            │
                            ▼
                    ┌───────────────┐
                    │  Report Model │
                    │ (status,path) │
                    └───────────────┘
```

## Workflow

1. **Demande de génération**: L'utilisateur lance la génération depuis l'UI
2. **Création du Report**: Entrée DB avec status `pending`
3. **Dispatch du Job**: `GenerateReportJob` envoyé à la queue
4. **Génération**: Le job génère le fichier
5. **Stockage**: Fichier uploadé sur S3/disk
6. **Mise à jour**: Report passe à `completed`
7. **Notification**: WebSocket/polling pour mettre à jour l'UI

## Modèle Report

```php
Schema::create('reports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('organization_id');
    $table->foreignUuid('assessment_id');
    $table->foreignUuid('generated_by');

    $table->string('type');       // beges, ghg, custom
    $table->string('format');     // pdf, xlsx, xml
    $table->string('status');     // pending, processing, completed, failed

    $table->string('file_path')->nullable();
    $table->unsignedInteger('file_size')->nullable();
    $table->string('file_hash')->nullable();

    $table->json('options')->nullable();
    $table->text('error_message')->nullable();

    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});
```

## Job de Génération

```php
class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes max
    public int $tries = 3;

    public function __construct(
        public Report $report,
        public array $options = []
    ) {}

    public function handle(ReportGenerator $generator): void
    {
        $this->report->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $path = $generator->generate(
                $this->report->assessment,
                $this->report->format,
                $this->options
            );

            $this->report->update([
                'status' => 'completed',
                'file_path' => $path,
                'file_size' => Storage::size($path),
                'file_hash' => md5_file(Storage::path($path)),
                'completed_at' => now(),
            ]);

            event(new ReportGenerated($this->report));

        } catch (Exception $e) {
            $this->report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->report->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
```

## Générateurs par Format

```php
interface ReportGeneratorInterface
{
    public function generate(Assessment $assessment, array $options): string;
    public function supports(string $format): bool;
}

// Implémentations
class PdfReportGenerator implements ReportGeneratorInterface
{
    // Utilise DomPDF ou Browsershot
}

class ExcelReportGenerator implements ReportGeneratorInterface
{
    // Utilise PhpSpreadsheet
}

class BegesXmlGenerator implements ReportGeneratorInterface
{
    // Génère le XML conforme ADEME
}
```

## Templates

Les rapports utilisent des templates Blade pour le contenu:

```
resources/views/reports/
├── pdf/
│   ├── beges.blade.php
│   ├── ghg-protocol.blade.php
│   └── custom.blade.php
├── excel/
│   └── emissions-detail.blade.php
└── partials/
    ├── header.blade.php
    ├── scope-chart.blade.php
    └── footer.blade.php
```

## Sécurité Téléchargement

```php
// Signed URL pour téléchargement (expire en 1h)
public function download(Report $report): StreamedResponse
{
    $this->authorize('download', $report);

    return Storage::download(
        $report->file_path,
        $report->filename,
        ['Content-Type' => $report->mime_type]
    );
}
```

## Conséquences

- Les rapports volumineux ne bloquent pas l'UI
- L'historique des rapports est conservé
- Les fichiers sont sécurisés avec URLs signées
- La queue peut être scalée indépendamment
- Les échecs sont retentés automatiquement

## Références

- [Laravel Queues](https://laravel.com/docs/queues)
- [DomPDF](https://github.com/dompdf/dompdf)
- [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io)
