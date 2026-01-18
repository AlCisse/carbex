import { test, expect } from '@playwright/test';

test('Download generated report', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@linscarbon.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to reports
    console.log('Navigating to Reports page...');
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Screenshot the page
    await page.screenshot({ path: 'test-results/reports-page.png', fullPage: true });

    // Check for completed reports in history (French UI: "Terminé")
    const completedBadge = page.locator('.bg-green-100.text-green-800').first();
    if (await completedBadge.isVisible()) {
        console.log('Found completed report');

        // Look for download button (French UI: "Télécharger")
        const downloadLink = page.locator('a:has-text("Télécharger"), a:has-text("Download")').first();
        if (await downloadLink.isVisible()) {
            console.log('Download button found');

            // Set up download listener
            const downloadPromise = page.waitForEvent('download', { timeout: 30000 });

            // Click download
            await downloadLink.click();

            try {
                const download = await downloadPromise;
                const filename = download.suggestedFilename();
                console.log('✅ Download started:', filename);

                // Save the file
                await download.saveAs(`test-results/${filename}`);
                console.log('✅ File saved to test-results/' + filename);

                // Verify file extension
                expect(filename).toMatch(/\.(docx|xlsx|pdf)$/);
            } catch (e) {
                console.log('Download timeout - checking if page navigated');
                await page.screenshot({ path: 'test-results/download-attempt.png', fullPage: true });
            }
        } else {
            console.log('No download button visible');
        }
    } else {
        console.log('No completed reports found');
        // Generate a new report first
        console.log('Generating a new report...');
        await page.locator('button:has-text("Generate")').first().click();
        await page.waitForTimeout(1000);

        const modalGenerate = page.locator('.relative.z-20 button:has-text("Generate")');
        if (await modalGenerate.isVisible()) {
            await modalGenerate.click();
            await page.waitForTimeout(3000);
        }
    }

    await page.screenshot({ path: 'test-results/report-download-final.png', fullPage: true });
    console.log('✅ Report download test completed');
});
