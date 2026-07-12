import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test.describe('CSRD Compliance Pages', () => {
    test.beforeEach(async ({ page }) => {
        page.on('console', msg => {
            if (msg.type() === 'error') console.log('ERROR:', msg.text());
        });

        await login(page);
    });

    test('CSRD Dashboard loads correctly', async ({ page }) => {
        console.log('Testing CSRD Dashboard...');
        await page.goto('/csrd');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/csrd-dashboard.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');
        expect(body).not.toContain('500');

        // Check for CSRD-related content
        const hasCsrdContent = body?.includes('CSRD') ||
                               body?.includes('compliance') ||
                               body?.includes('Compliance') ||
                               body?.includes('ESRS');
        console.log('Has CSRD content:', hasCsrdContent);

        console.log('✅ CSRD Dashboard OK');
    });

    test('ESRS 2 Disclosures page loads', async ({ page }) => {
        console.log('Testing ESRS 2 page...');
        await page.goto('/csrd/esrs2');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/csrd-esrs2.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        console.log('✅ ESRS 2 page OK');
    });

    test('Climate Transition Plan page loads', async ({ page }) => {
        console.log('Testing Transition Plan page...');
        await page.goto('/csrd/transition-plan');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/csrd-transition-plan.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        console.log('✅ Transition Plan page OK');
    });

    test('EU Taxonomy page loads', async ({ page }) => {
        console.log('Testing EU Taxonomy page...');
        await page.goto('/csrd/taxonomy');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/csrd-taxonomy.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        console.log('✅ EU Taxonomy page OK');
    });

    test('Value Chain Due Diligence page loads', async ({ page }) => {
        console.log('Testing Due Diligence page...');
        await page.goto('/csrd/due-diligence');
        await page.waitForLoadState('networkidle');

        await page.screenshot({ path: 'test-results/csrd-due-diligence.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        console.log('✅ Due Diligence page OK');
    });
});
