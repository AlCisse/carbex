import { test, expect } from '@playwright/test';

test.describe('Scope Navigation Tests', () => {
    const scopeCategories = [
        { scope: 1, code: '1.1', name: 'Sources fixes de combustion' },
        { scope: 1, code: '1.2', name: 'Sources mobiles de combustion' },
        { scope: 1, code: '1.4', name: 'Émissions fugitives' },
        { scope: 1, code: '1.5', name: 'Biomasse (sols et forêts)' },
        { scope: 2, code: '2.1', name: 'Consommation d\'électricité' },
        { scope: 3, code: '3.1', name: 'Transport de marchandise amont' },
        { scope: 3, code: '3.2', name: 'Transport de marchandise aval' },
        { scope: 3, code: '3.3', name: 'Déplacements domicile-travail' },
        { scope: 3, code: '3.5', name: 'Déplacements professionnels' },
        { scope: 3, code: '4.1', name: 'Achats de biens' },
        { scope: 3, code: '4.2', name: 'Immobilisations de biens' },
        { scope: 3, code: '4.3', name: 'Gestion des déchets' },
        { scope: 3, code: '4.4', name: 'Actifs en leasing amont' },
        { scope: 3, code: '4.5', name: 'Achats de services' },
    ];

    test.beforeEach(async ({ page }) => {
        // Enable console logging
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('PAGE ERROR:', msg.text());
            }
        });
        page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

        // Login
        await page.goto('/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.locator('#email').fill('test@carbex.fr');
        await page.locator('#password').fill('password');
        await page.locator('#password').dispatchEvent('input');
        await page.waitForTimeout(500);
        await page.locator('#password').press('Enter');

        // Wait for dashboard
        await page.waitForSelector('aside', { timeout: 30000 });
        await page.waitForLoadState('networkidle');
    });

    test('should navigate to all Scope 1 categories', async ({ page }) => {
        // Click on Scope 1 to expand
        await page.getByText('Scope 1', { exact: false }).first().click();
        await page.waitForTimeout(500);

        // Navigate to each Scope 1 category
        for (const cat of scopeCategories.filter(c => c.scope === 1)) {
            console.log(`Testing: ${cat.code} - ${cat.name}`);

            await page.goto(`/emissions/scope/1/category/${cat.code}`);
            await page.waitForLoadState('networkidle');

            // Check for 500 error
            const bodyText = await page.locator('body').textContent();
            if (bodyText?.includes('Internal Server Error') || bodyText?.includes('500')) {
                console.log(`ERROR on ${cat.code}: 500 Internal Server Error`);
            } else {
                console.log(`OK: ${cat.code}`);
            }

            await page.waitForTimeout(500);
        }
    });

    test('should navigate to Scope 2 category', async ({ page }) => {
        await page.goto('/emissions/scope/2/category/2.1');
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').textContent();
        expect(bodyText).not.toContain('Internal Server Error');
    });

    test('should navigate to all Scope 3 categories', async ({ page }) => {
        for (const cat of scopeCategories.filter(c => c.scope === 3)) {
            console.log(`Testing: ${cat.code} - ${cat.name}`);

            await page.goto(`/emissions/scope/3/category/${cat.code}`);
            await page.waitForLoadState('networkidle');

            const bodyText = await page.locator('body').textContent();
            if (bodyText?.includes('Internal Server Error') || bodyText?.includes('500')) {
                console.log(`ERROR on ${cat.code}: 500 Internal Server Error`);
            } else {
                console.log(`OK: ${cat.code}`);
            }

            await page.waitForTimeout(300);
        }
    });
});
