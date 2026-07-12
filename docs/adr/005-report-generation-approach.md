# ADR-005: Report Generation Approach

## Status

Accepted

## Date

2024-03-15

## Context

LinsCarbon generates various carbon footprint reports:
- Annual BEGES reports (French regulatory)
- CSRD/ESG reports (EU regulatory)
- Custom carbon footprint summaries
- Supplier data collection reports

Reports can be large and take significant time to generate, involving:
- Aggregating emissions data
- Generating charts and visualizations
- Creating PDF documents
- Multi-language support

### Requirements

- Generate PDF reports up to 100+ pages
- Support multiple languages
- Allow scheduled report generation
- Provide download links (not email attachments)
- Maintain generation history

### Options Considered

1. **Synchronous PDF Generation**
   - Generate on request, return file
   - Pros: Simple, immediate
   - Cons: Timeout issues, poor UX for large reports

2. **Async with Queue Workers**
   - Queue report generation, notify when complete
   - Pros: Scalable, no timeouts
   - Cons: User waits, needs status checking

3. **Pre-generated + Incremental**
   - Generate reports on schedule, serve cached
   - Pros: Instant delivery
   - Cons: Data may be stale, storage costs

4. **External Service (e.g., DocRaptor)**
   - Use PDF generation API
   - Pros: Offload processing, professional output
   - Cons: Vendor dependency, cost per document

## Decision

We chose **Option 2: Async Queue-Based Generation** with Laravel queues and DomPDF/Browsershot for PDF rendering.

### Architecture

```
┌─────────────────────────────────────────────────────┐
│                    User Request                      │
└──────────────────────┬──────────────────────────────┘
                       │
                       ▼
              ┌─────────────────┐
              │ Report Request  │
              │   Controller    │
              └────────┬────────┘
                       │
                       ▼
              ┌─────────────────┐
              │  Create Report  │──────▶ DB (status: pending)
              │    Record       │
              └────────┬────────┘
                       │
                       ▼
              ┌─────────────────┐
              │   Dispatch      │
              │ GenerateReport  │──────▶ Queue
              │      Job        │
              └─────────────────┘
                       │
         ┌─────────────┴─────────────┐
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│ Queue Worker 1  │         │ Queue Worker 2  │
└────────┬────────┘         └────────┬────────┘
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│ ReportGenerator │         │ ReportGenerator │
│    Service      │         │    Service      │
└────────┬────────┘         └────────┬────────┘
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│   Blade View    │         │   Blade View    │
│   + DomPDF      │         │   + DomPDF      │
└────────┬────────┘         └────────┬────────┘
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│   S3 Storage    │         │   S3 Storage    │
│   (PDF file)    │         │   (PDF file)    │
└────────┬────────┘         └────────┬────────┘
         │                           │
         └─────────────┬─────────────┘
                       ▼
              ┌─────────────────┐
              │ Update Report   │──────▶ DB (status: completed)
              │    Record       │
              └────────┬────────┘
                       │
                       ▼
              ┌─────────────────┐
              │ Send Notification│──────▶ User
              │ (Email + In-App) │
              └─────────────────┘
```

### Implementation

1. **Report Request Flow**
   ```php
   class ReportController
   {
       public function generate(ReportRequest $request)
       {
           $report = Report::create([
               'organization_id' => auth()->user()->organization_id,
               'type' => $request->type,
               'status' => 'pending',
               'period_start' => $request->period_start,
               'period_end' => $request->period_end,
           ]);

           GenerateReport::dispatch($report);

           return response()->json([
               'success' => true,
               'data' => ['report_id' => $report->id],
               'message' => 'Report generation started',
           ]);
       }
   }
   ```

2. **Generation Job**
   ```php
   class GenerateReport implements ShouldQueue
   {
       public function handle(ReportGenerator $generator)
       {
           try {
               $report = $this->report;
               $report->update(['status' => 'processing']);

               $path = $generator->generate($report);

               $report->update([
                   'status' => 'completed',
                   'file_path' => $path,
                   'completed_at' => now(),
               ]);

               event(new ReportGenerated($report));

           } catch (\Exception $e) {
               $report->update([
                   'status' => 'failed',
                   'error_message' => $e->getMessage(),
               ]);

               event(new ReportFailed($report, $e->getMessage()));
           }
       }
   }
   ```

3. **PDF Generation**
   ```php
   class ReportGenerator
   {
       public function generate(Report $report): string
       {
           // Gather data
           $data = $this->gatherReportData($report);

           // Render Blade view
           $html = view("pdf.reports.{$report->type}", $data)->render();

           // Generate PDF
           $pdf = Pdf::loadHTML($html)
               ->setPaper('a4')
               ->setOption('enable-local-file-access', true);

           // Store to S3
           $path = "reports/{$report->organization_id}/{$report->id}.pdf";
           Storage::disk('s3')->put($path, $pdf->output());

           return $path;
       }
   }
   ```

### Blade Template Structure

```
resources/views/pdf/reports/
├── summary.blade.php           # Main summary report
├── beges.blade.php             # French BEGES format
├── csrd.blade.php              # EU CSRD format
├── partials/
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── methodology.blade.php
│   ├── scope-breakdown.blade.php
│   └── charts.blade.php
└── layouts/
    └── report.blade.php        # Base layout
```

## Consequences

### Positive

- No timeout issues for large reports
- Horizontal scaling via queue workers
- Progress tracking and status updates
- Failed job handling and retries
- Non-blocking user experience

### Negative

- Not instant (user must wait)
- More complex architecture
- Need to manage job failures
- Storage costs for PDF files

### Performance Optimizations

1. **Chunked Data Loading**: Load emissions in batches
2. **Pre-computed Aggregates**: Use cached summaries
3. **Separate Queue**: `reports` queue with dedicated workers
4. **Memory Management**: Release memory between sections

```php
// Dedicated queue for reports
'reports' => [
    'connection' => 'redis',
    'queue' => 'reports',
    'memory' => 512,
    'timeout' => 600, // 10 minutes max
],
```

## Related

- [ADR-002](002-emission-calculation-engine.md) - Data source for reports
- PDF template design guidelines
- Report storage and retention policy
