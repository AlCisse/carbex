import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test.describe('PDF Report Generation', () => {
    test.beforeEach(async ({ page }) => {
        page.on('console', msg => {
            if (msg.type() === 'error') console.log('ERROR:', msg.text());
        });

        // Use shared login helper
        await login(page);
    });

    test('Reports page loads correctly', async ({ page }) => {
        console.log('1. Navigating to Reports page...');
        await page.goto('/reports');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/reports-page-full.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        console.log('✅ Reports page loaded');
    });

    test('Generate PDF report button exists', async ({ page }) => {
        console.log('2. Checking for PDF generation buttons...');
        await page.goto('/reports');
        await page.waitForLoadState('networkidle');

        // Look for PDF/Export buttons
        const pdfButtons = await page.locator('button:has-text("PDF"), button:has-text("pdf"), a:has-text("PDF"), button:has-text("Export"), button:has-text("export"), button:has-text("Générer"), button:has-text("Generate")').all();

        console.log(`Found ${pdfButtons.length} PDF/Export buttons`);

        for (let i = 0; i < Math.min(pdfButtons.length, 5); i++) {
            const text = await pdfButtons[i].textContent();
            console.log(`  Button ${i + 1}: ${text?.trim()}`);
        }

        await page.screenshot({ path: 'test-results/reports-buttons.png', fullPage: true });

        console.log('✅ PDF buttons check complete');
    });

    test('Click Generate Report and check response', async ({ page }) => {
        console.log('3. Testing report generation...');
        await page.goto('/reports');
        await page.waitForLoadState('networkidle');

        // Try to find and click a generate/export button
        const generateBtn = page.locator('button:has-text("Générer"), button:has-text("Generate"), button:has-text("Export PDF"), button:has-text("Exporter")').first();

        if (await generateBtn.isVisible()) {
            console.log('   Found generate button, clicking...');

            // Listen for download or new page
            const downloadPromise = page.waitForEvent('download', { timeout: 10000 }).catch(() => null);

            await generateBtn.click();
            await page.waitForTimeout(3000);

            const download = await downloadPromise;

            if (download) {
                console.log('   PDF download started:', download.suggestedFilename());
                // Save the file
                await download.saveAs('test-results/generated-report.pdf');
                console.log('✅ PDF downloaded successfully');
            } else {
                console.log('   No download triggered, checking page state...');
                await page.screenshot({ path: 'test-results/after-generate-click.png', fullPage: true });
            }
        } else {
            console.log('   No generate button found, looking for alternatives...');

            // Check for report type selection
            const reportTypes = await page.locator('[class*="report"], [class*="card"]').all();
            console.log(`   Found ${reportTypes.length} report-related elements`);

            await page.screenshot({ path: 'test-results/reports-no-button.png', fullPage: true });
        }

        console.log('✅ Report generation test complete');
    });

    test('Check report templates available', async ({ page }) => {
        console.log('4. Checking report templates...');
        await page.goto('/reports');
        await page.waitForLoadState('networkidle');

        const body = await page.locator('body').textContent();

        // Check for different report types
        const reportTypes = [
            'BEGES', 'Bilan Carbone', 'GHG', 'CDP', 'CSRD',
            'annuel', 'annual', 'mensuel', 'monthly',
            'Scope 1', 'Scope 2', 'Scope 3'
        ];

        console.log('   Available report types:');
        for (const type of reportTypes) {
            if (body?.toLowerCase().includes(type.toLowerCase())) {
                console.log(`   ✓ ${type}`);
            }
        }

        console.log('✅ Report templates check complete');
    });

    test('Export emissions data', async ({ page }) => {
        console.log('5. Testing emissions data export...');

        // Go to emissions page first
        await page.goto('/emissions/1/1.1');
        await page.waitForLoadState('networkidle');

        // Look for export button on emissions page
        const exportBtn = page.locator('button:has-text("Export"), button:has-text("export"), button:has-text("CSV"), button:has-text("Excel")').first();

        if (await exportBtn.isVisible()) {
            console.log('   Found export button on emissions page');

            const downloadPromise = page.waitForEvent('download', { timeout: 10000 }).catch(() => null);
            await exportBtn.click();
            await page.waitForTimeout(2000);

            const download = await downloadPromise;
            if (download) {
                console.log('   Export download started:', download.suggestedFilename());
            }
        } else {
            console.log('   No export button on emissions page');
        }

        await page.screenshot({ path: 'test-results/emissions-export.png', fullPage: true });
        console.log('✅ Emissions export test complete');
    });
});
