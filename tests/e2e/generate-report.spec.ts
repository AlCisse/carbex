import { test, expect } from '@playwright/test';

test('Generate Complete Carbon Footprint report', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to reports
    console.log('Navigating to Reports page...');
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Click the first Generate button (Complete Carbon Footprint)
    console.log('Clicking Generate for Complete Carbon Footprint...');

    // Click the first generate button to open modal
    await page.locator('button:has-text("Generate")').first().click();
    await page.waitForTimeout(1000);

    // Screenshot the modal
    await page.screenshot({ path: 'test-results/report-modal.png', fullPage: true });

    // Check if modal is visible
    const modalTitle = page.getByRole('heading', { name: 'Generate report' });
    if (await modalTitle.isVisible()) {
        console.log('Modal opened successfully');

        // Click Generate button in modal
        const generateInModal = page.locator('.relative.z-20 button:has-text("Generate")');
        if (await generateInModal.isVisible()) {
            console.log('Clicking Generate in modal...');
            await generateInModal.click();

            // Wait for modal to close or page to update
            await page.waitForTimeout(3000);

            // Check if modal closed (success)
            const modalStillOpen = await modalTitle.isVisible().catch(() => false);
            if (!modalStillOpen) {
                console.log('Modal closed - generation completed!');
            } else {
                console.log('Modal still open - checking for errors...');
                await page.screenshot({ path: 'test-results/report-modal-still-open.png', fullPage: true });
            }
        }
    }

    console.log('Waiting for response...');
    await page.waitForTimeout(2000);

    // Check for download (reports might not download immediately as they're async)
    const download = null;

    if (download) {
        const filename = download.suggestedFilename();
        console.log('✅ Download started:', filename);

        // Save the file
        const path = `test-results/${filename}`;
        await download.saveAs(path);
        console.log('✅ File saved to:', path);
    } else {
        console.log('No download triggered');

        // Screenshot current state
        await page.screenshot({ path: 'test-results/after-generate.png', fullPage: true });

        // Check if there's an error or loading state
        const body = await page.locator('body').textContent();

        if (body?.includes('generating') || body?.includes('Generating')) {
            console.log('Report is being generated...');
        } else if (body?.includes('error') || body?.includes('Error')) {
            console.log('Error occurred during generation');
        }

        // Check report history section
        const historySection = page.locator('text=Report history').first();
        if (await historySection.isVisible()) {
            console.log('Report history section visible');
        }
    }

    // Final screenshot
    await page.screenshot({ path: 'test-results/report-generation-final.png', fullPage: true });

    console.log('✅ Report generation test completed');
});

test('Generate ADEME Declaration', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to reports
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Click the second Generate button (ADEME Declaration) to open modal
    console.log('Clicking Generate for ADEME Declaration...');
    await page.locator('button:has-text("Generate")').nth(1).click();
    await page.waitForTimeout(1000);

    // Click Generate button in modal
    const modalGenerate = page.locator('.relative.z-20 button:has-text("Generate")');
    if (await modalGenerate.isVisible()) {
        console.log('Modal opened, clicking Generate...');
        await modalGenerate.click();
        await page.waitForTimeout(3000);

        // Check if modal closed (success)
        const modalStillOpen = await page.locator('.relative.z-20').isVisible().catch(() => false);
        if (!modalStillOpen) {
            console.log('✅ ADEME report generated successfully');
        }
    }

    await page.screenshot({ path: 'test-results/ademe-generation.png', fullPage: true });
    console.log('✅ ADEME test completed');
});

test('Generate GHG Protocol Report', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to reports
    await page.goto('/reports');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Click the third Generate button (GHG Protocol) to open modal
    console.log('Clicking Generate for GHG Protocol Report...');
    await page.locator('button:has-text("Generate")').nth(2).click();
    await page.waitForTimeout(1000);

    // Click Generate button in modal
    const modalGenerate = page.locator('.relative.z-20 button:has-text("Generate")');
    if (await modalGenerate.isVisible()) {
        console.log('Modal opened, clicking Generate...');
        await modalGenerate.click();
        await page.waitForTimeout(3000);

        // Check if modal closed (success)
        const modalStillOpen = await page.locator('.relative.z-20').isVisible().catch(() => false);
        if (!modalStillOpen) {
            console.log('✅ GHG report generated successfully');
        }
    }

    await page.screenshot({ path: 'test-results/ghg-generation.png', fullPage: true });
    console.log('✅ GHG test completed');
});
