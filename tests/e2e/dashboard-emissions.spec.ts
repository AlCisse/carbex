import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test('Check emissions display on dashboard', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });

    // Use shared login helper
    await login(page);

    // Go to dashboard
    console.log('Navigating to dashboard...');
    await page.goto('/dashboard');
    await page.waitForLoadState('networkidle');

    // Screenshot
    await page.screenshot({ path: 'test-results/dashboard-emissions.png', fullPage: true });

    // Check for emissions data
    const body = await page.locator('body').textContent();

    // Look for CO2/emissions indicators
    const hasEmissionsData = body?.includes('CO2') ||
                             body?.includes('tCO2') ||
                             body?.includes('kgCO2') ||
                             body?.includes('émissions') ||
                             body?.includes('Emissions');

    // Look for scope indicators
    const hasScopes = body?.includes('Scope 1') ||
                      body?.includes('Scope 2') ||
                      body?.includes('Scope 3');

    // Look for numeric values (emissions totals)
    const hasNumbers = /\d+[\s,.]?\d*\s*(kg|t|tonne)/i.test(body || '');

    console.log('Dashboard analysis:');
    console.log('- Has emissions terminology:', hasEmissionsData);
    console.log('- Has scope references:', hasScopes);
    console.log('- Has emission values:', hasNumbers);

    // Check stat cards
    const statCards = await page.locator('[class*="stat"], [class*="card"], [class*="metric"]').count();
    console.log('- Stat/card elements found:', statCards);

    // Check for chart elements
    const charts = await page.locator('canvas, svg[class*="chart"], [class*="chart"]').count();
    console.log('- Chart elements found:', charts);

    // Get visible numbers that might be emissions
    const numbers = await page.locator('text=/\\d+[\\s,.]?\\d*\\s*(kg|t|tCO2|kgCO2)/i').allTextContents();
    if (numbers.length > 0) {
        console.log('- Emission values found:', numbers.slice(0, 5).join(', '));
    }

    // Verify page loaded correctly
    expect(body).not.toContain('Internal Server Error');

    console.log('✅ Dashboard test completed');
});
