import { test, expect } from '@playwright/test';

test.describe('Scope 2 & 3 Add Source Tests', () => {
    test.beforeEach(async ({ page }) => {
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
    });

    test('Scope 2.1 - Électricité (Add source)', async ({ page }) => {
        await page.goto('/emissions/2/2.1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

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

            // Fill source name
            console.log('Scope 2.1: Filling source name...');
            await page.locator('input[placeholder*="Paris"]').fill('Électricité bureau principal');
            await page.waitForTimeout(500);

            // Open emission factor selector
            console.log('Scope 2.1: Opening factor selector...');
            await page.getByRole('button', { name: 'Select an emission factor' }).click();
            await page.waitForTimeout(2000);

            // Search for electricity factor
            const searchInput = page.locator('input[type="text"]').filter({ hasText: '' }).locator('visible=true').first();
            if (await searchInput.isVisible()) {
                await searchInput.fill('electricity');
                console.log('Scope 2.1: Searched for electricity');
            }
            await page.waitForTimeout(2000);

            // Select first factor
            const factorRow = page.locator('li.cursor-pointer').first();
            if (await factorRow.isVisible()) {
                await factorRow.click();
                console.log('Scope 2.1: Factor selected');
            } else {
                await page.keyboard.press('Escape');
            }
            await page.waitForTimeout(1000);

            // Fill quantity
            const quantityInput = page.locator('input[type="number"]').first();
            if (await quantityInput.isVisible()) {
                await quantityInput.fill('5000');
                console.log('Scope 2.1: Quantity filled');
            }
            await page.waitForTimeout(500);

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope2-2.1-form.png', fullPage: true });

            // Submit form
            console.log('Scope 2.1: Submitting...');
            await page.getByRole('button', { name: 'Add source' }).last().click();
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
        await page.waitForTimeout(1000);

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

            // Fill source name
            console.log('Scope 3.1: Filling source name...');
            await page.locator('input[placeholder*="Paris"]').fill('Transport fournisseur A');
            await page.waitForTimeout(500);

            // Open emission factor selector
            console.log('Scope 3.1: Opening factor selector...');
            await page.getByRole('button', { name: 'Select an emission factor' }).click();
            await page.waitForTimeout(2000);

            // Search for transport factor
            const searchInput = page.locator('input[type="text"]').filter({ hasText: '' }).locator('visible=true').first();
            if (await searchInput.isVisible()) {
                await searchInput.fill('transport');
                console.log('Scope 3.1: Searched for transport');
            }
            await page.waitForTimeout(2000);

            // Select first factor
            const factorRow = page.locator('li.cursor-pointer').first();
            if (await factorRow.isVisible()) {
                await factorRow.click();
                console.log('Scope 3.1: Factor selected');
            } else {
                await page.keyboard.press('Escape');
            }
            await page.waitForTimeout(1000);

            // Fill quantity
            const quantityInput = page.locator('input[type="number"]').first();
            if (await quantityInput.isVisible()) {
                await quantityInput.fill('2500');
                console.log('Scope 3.1: Quantity filled');
            }
            await page.waitForTimeout(500);

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope3-3.1-form.png', fullPage: true });

            // Submit form
            console.log('Scope 3.1: Submitting...');
            await page.getByRole('button', { name: 'Add source' }).last().click();
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
        await page.waitForTimeout(1000);

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

            // Fill source name
            console.log('Scope 3.5: Filling source name...');
            await page.locator('input[placeholder*="Paris"]').fill('Voyage Paris-Lyon');
            await page.waitForTimeout(500);

            // Open emission factor selector
            console.log('Scope 3.5: Opening factor selector...');
            await page.getByRole('button', { name: 'Select an emission factor' }).click();
            await page.waitForTimeout(2000);

            // Search for travel factor
            const searchInput = page.locator('input[type="text"]').filter({ hasText: '' }).locator('visible=true').first();
            if (await searchInput.isVisible()) {
                await searchInput.fill('train');
                console.log('Scope 3.5: Searched for train');
            }
            await page.waitForTimeout(2000);

            // Select first factor
            const factorRow = page.locator('li.cursor-pointer').first();
            if (await factorRow.isVisible()) {
                await factorRow.click();
                console.log('Scope 3.5: Factor selected');
            } else {
                await page.keyboard.press('Escape');
            }
            await page.waitForTimeout(1000);

            // Fill quantity
            const quantityInput = page.locator('input[type="number"]').first();
            if (await quantityInput.isVisible()) {
                await quantityInput.fill('450');
                console.log('Scope 3.5: Quantity filled');
            }
            await page.waitForTimeout(500);

            // Screenshot before submit
            await page.screenshot({ path: 'test-results/scope3-3.5-form.png', fullPage: true });

            // Submit form
            console.log('Scope 3.5: Submitting...');
            await page.getByRole('button', { name: 'Add source' }).last().click();
            await page.waitForTimeout(3000);

            // Screenshot after submit
            await page.screenshot({ path: 'test-results/scope3-3.5-after.png', fullPage: true });

            console.log('✅ Scope 3.5 test completed');
        } else {
            console.log('⚠️ No Add source button found on 3.5');
        }
    });
});
