import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test.describe('Scope 2 & 3 Add Source Tests', () => {
    test.beforeEach(async ({ page }) => {
        page.on('console', msg => {
            if (msg.type() === 'error') console.log('ERROR:', msg.text());
        });
        page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

        // Use shared login helper
        await login(page);
    });

    /**
     * Helper to fill the add source form
     */
    async function fillAddSourceForm(
        page: any,
        sourceName: string,
        searchTerm: string,
        quantity: string,
        scopeLabel: string
    ) {
        // Fill source name
        console.log(`${scopeLabel}: Filling source name...`);
        await page.locator('input[placeholder*="Paris"]').fill(sourceName);
        await page.waitForTimeout(500);

        // Open emission factor selector
        console.log(`${scopeLabel}: Opening factor selector...`);
        await page.getByRole('button', { name: 'Select an emission factor' }).click();
        await page.waitForTimeout(1500);

        // Search for factor
        const searchInput = page.locator('input[type="text"][placeholder*="Search"], input[type="search"]').first();
        if (await searchInput.isVisible()) {
            await searchInput.fill(searchTerm);
            console.log(`${scopeLabel}: Searched for ${searchTerm}`);
        }
        await page.waitForTimeout(2000);

        // Select first factor
        const factorRow = page.locator('li.cursor-pointer').first();
        if (await factorRow.isVisible()) {
            await factorRow.click();
            console.log(`${scopeLabel}: Factor selected`);
        } else {
            await page.keyboard.press('Escape');
        }

        // Wait for modal to close
        await page.waitForTimeout(1500);
        await page.waitForSelector('.fixed.inset-0.bg-gray-500', { state: 'hidden', timeout: 5000 }).catch(() => {});

        // Fill quantity
        const quantityInput = page.locator('input[type="number"]').first();
        if (await quantityInput.isVisible()) {
            await quantityInput.fill(quantity);
            console.log(`${scopeLabel}: Quantity filled`);
        }
        await page.waitForTimeout(500);
    }

    test('Scope 2.1 - Électricité (Add source)', async ({ page }) => {
        await page.goto('/emissions/2/2.1');
        await page.waitForLoadState('networkidle');

        // Screenshot before
        await page.screenshot({ path: 'test-results/scope2-2.1-before.png', fullPage: true });

        // Check page loaded correctly
        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Click Add source button
        console.log('Scope 2.1: Clicking Add source...');
        const addBtn = page.getByRole('button', { name: /add source/i });
        if (await addBtn.isVisible()) {
            await addBtn.click();
            await page.waitForTimeout(1000);

            await fillAddSourceForm(page, 'Électricité bureau principal', 'electricity', '5000', 'Scope 2.1');

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope2-2.1-form.png', fullPage: true });

            // Submit form with force click
            console.log('Scope 2.1: Submitting...');
            await page.getByRole('button', { name: /add source/i }).last().click({ force: true });
            await page.waitForTimeout(3000);

            // Screenshot after submit
            await page.screenshot({ path: 'test-results/scope2-2.1-after.png', fullPage: true });

            console.log('✅ Scope 2.1 test completed');
        } else {
            console.log('⚠️ No Add source button found on 2.1');
        }
    });

    test('Scope 3.1 - Transport amont (Add source)', async ({ page }) => {
        await page.goto('/emissions/3/3.1');
        await page.waitForLoadState('networkidle');

        // Screenshot before
        await page.screenshot({ path: 'test-results/scope3-3.1-before.png', fullPage: true });

        // Check page loaded correctly
        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Click Add source button
        console.log('Scope 3.1: Clicking Add source...');
        const addBtn = page.getByRole('button', { name: /add source/i });
        if (await addBtn.isVisible()) {
            await addBtn.click();
            await page.waitForTimeout(1000);

            await fillAddSourceForm(page, 'Transport fournisseur A', 'transport', '2500', 'Scope 3.1');

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope3-3.1-form.png', fullPage: true });

            // Submit form with force click
            console.log('Scope 3.1: Submitting...');
            await page.getByRole('button', { name: /add source/i }).last().click({ force: true });
            await page.waitForTimeout(3000);

            // Screenshot after submit
            await page.screenshot({ path: 'test-results/scope3-3.1-after.png', fullPage: true });

            console.log('✅ Scope 3.1 test completed');
        } else {
            console.log('⚠️ No Add source button found on 3.1');
        }
    });

    test('Scope 3.5 - Déplacements professionnels (Add source)', async ({ page }) => {
        await page.goto('/emissions/3/3.5');
        await page.waitForLoadState('networkidle');

        // Screenshot before
        await page.screenshot({ path: 'test-results/scope3-3.5-before.png', fullPage: true });

        // Check page loaded correctly
        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Click Add source button
        console.log('Scope 3.5: Clicking Add source...');
        const addBtn = page.getByRole('button', { name: /add source/i });
        if (await addBtn.isVisible()) {
            await addBtn.click();
            await page.waitForTimeout(1000);

            await fillAddSourceForm(page, 'Voyage Paris-Lyon', 'train', '450', 'Scope 3.5');

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope3-3.5-form.png', fullPage: true });

            // Submit form with force click
            console.log('Scope 3.5: Submitting...');
            await page.getByRole('button', { name: /add source/i }).last().click({ force: true });
            await page.waitForTimeout(3000);

            // Screenshot after submit
            await page.screenshot({ path: 'test-results/scope3-3.5-after.png', fullPage: true });

            console.log('✅ Scope 3.5 test completed');
        } else {
            console.log('⚠️ No Add source button found on 3.5');
        }
    });
});
