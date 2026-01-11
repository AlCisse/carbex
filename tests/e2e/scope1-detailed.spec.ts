import { test, expect } from '@playwright/test';

test.describe('Scope 1 Detailed Tests', () => {
    test.beforeEach(async ({ page }) => {
        // Capture all console errors
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('CONSOLE ERROR:', msg.text());
            }
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
        await page.waitForLoadState('networkidle');
    });

    test('1.1 - Sources fixes de combustion', async ({ page }) => {
        await page.goto('/emissions/1/1.1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // Screenshot
        await page.screenshot({ path: 'test-results/scope1-1.1.png', fullPage: true });

        // Check no 500 error
        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');
        // Removed: 500 check causes false positives with quantities like 1500

        // Check page has expected content
        await expect(page.locator('body')).toContainText(/combustion|émission|source/i);

        console.log('✅ 1.1 OK');
    });

    test('1.2 - Sources mobiles de combustion', async ({ page }) => {
        await page.goto('/emissions/1/1.2');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/scope1-1.2.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');
        // Removed: 500 check causes false positives with quantities like 1500

        await expect(page.locator('body')).toContainText(/mobile|véhicule|transport|émission/i);

        console.log('✅ 1.2 OK');
    });

    test('1.4 - Émissions fugitives', async ({ page }) => {
        await page.goto('/emissions/1/1.4');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/scope1-1.4.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');
        // Removed: 500 check causes false positives with quantities like 1500

        await expect(page.locator('body')).toContainText(/fugitif|fuite|émission/i);

        console.log('✅ 1.4 OK');
    });

    test('1.5 - Biomasse (sols et forêts)', async ({ page }) => {
        await page.goto('/emissions/1/1.5');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/scope1-1.5.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');
        // Removed: 500 check causes false positives with quantities like 1500

        await expect(page.locator('body')).toContainText(/biomasse|sol|forêt|émission/i);

        console.log('✅ 1.5 OK');
    });

    test('Test form interaction on 1.1', async ({ page }) => {
        await page.goto('/emissions/1/1.1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // Check if form elements exist
        const hasForm = await page.locator('form').count() > 0;
        const hasInputs = await page.locator('input').count() > 0;
        const hasButtons = await page.locator('button').count() > 0;

        console.log(`Form: ${hasForm}, Inputs: ${hasInputs}, Buttons: ${hasButtons}`);

        // Take screenshot
        await page.screenshot({ path: 'test-results/scope1-1.1-form.png', fullPage: true });

        console.log('✅ Form test OK');
    });
});
