import { test, expect } from '@playwright/test';

test('Test Add source button on 1.1', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to 1.1
    await page.goto('/emissions/1/1.1');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Screenshot before click
    await page.screenshot({ path: 'test-results/before-add-source.png', fullPage: true });

    // Click Add source button
    console.log('Clicking Add source...');
    await page.getByRole('button', { name: /add source/i }).click();

    // Wait for modal/form to appear
    await page.waitForTimeout(2000);

    // Screenshot after click
    await page.screenshot({ path: 'test-results/after-add-source.png', fullPage: true });

    // Check what appeared
    const body = await page.locator('body').textContent();

    if (body?.includes('Internal Server Error') || body?.includes('500')) {
        console.log('❌ ERROR: 500 Internal Server Error');
    } else {
        console.log('✅ Add source clicked successfully');
    }

    // Keep browser open for 5 seconds to see result
    await page.waitForTimeout(5000);
});
