import { test, expect } from '@playwright/test';

test('test emissions/1/1.1 page', async ({ page }) => {
    page.on('console', msg => console.log('LOG:', msg.text()));
    page.on('pageerror', err => console.log('ERROR:', err.message));

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to emissions/1/1.1
    console.log('Navigating to /emissions/1/1.1...');
    await page.goto('/emissions/1/1.1');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Take screenshot
    await page.screenshot({ path: 'test-results/emissions-1-1.1.png' });

    // Check for errors
    const body = await page.locator('body').textContent();
    if (body?.includes('Internal Server Error') || body?.includes('500')) {
        console.log('ERROR: 500 Internal Server Error detected');
        console.log(body?.substring(0, 1000));
    } else if (body?.includes('404') || body?.includes('Not Found')) {
        console.log('ERROR: 404 Not Found');
    } else {
        console.log('Page loaded successfully');
    }

    // Verify no 500 error
    expect(body).not.toContain('Internal Server Error');
});
