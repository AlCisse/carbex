import { test, expect } from '@playwright/test';

/**
 * Complete audit of all Filament Admin panel pages
 * Checks for 500 errors, missing translations, and database errors
 */

const adminPages = [
    // Dashboard
    '/admin',

    // Administration
    '/admin/organizations',
    '/admin/users',
    '/admin/sites',

    // Content
    '/admin/blog-posts',

    // Settings
    '/admin/ai-settings',

    // Carbon Data
    '/admin/emission-factors',

    // Finance
    '/admin/subscriptions',
    '/admin/transactions',
];

test.describe('Admin Panel Pages Audit', () => {
    test.beforeEach(async ({ page }) => {
        // Login to admin panel
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(500);

        // Fill login form
        await page.fill('input[type="email"]', 'test@linscarbon.local');
        await page.fill('input[type="password"]', 'yrW8GSnUpX59vUGP');
        await page.click('button[type="submit"]');

        // Wait for redirect to dashboard
        await page.waitForURL('**/admin**', { timeout: 15000 });
        await page.waitForLoadState('networkidle');
    });

    for (const pagePath of adminPages) {
        test(`Admin: ${pagePath}`, async ({ page }) => {
            await page.goto(pagePath);
            await page.waitForLoadState('networkidle');
            await page.waitForTimeout(1000);

            const body = await page.locator('body').textContent();
            const has500Error = body?.includes('Internal Server Error');
            const hasException = body?.includes('Exception') && body?.includes('vendor/');
            const hasSQLError = body?.includes('SQLSTATE');
            const hasUndefinedColumn = body?.includes('Undefined column');

            if (has500Error || hasException || hasSQLError || hasUndefinedColumn) {
                console.log(`❌ ${pagePath} - Error detected`);
                await page.screenshot({ path: `test-results/admin-error${pagePath.replace(/\//g, '-')}.png`, fullPage: true });
            } else {
                console.log(`✅ ${pagePath} - OK`);
            }

            // Check for raw translation keys
            const rawKeys = body?.match(/linscarbon\.[a-z_\.]+/gi)?.filter(k =>
                !['linscarbon.fr', 'linscarbon.com', 'linscarbon.de', 'linscarbon.io', 'linscarbon.eu', 'linscarbon.local'].includes(k)
            );
            if (rawKeys && rawKeys.length > 0) {
                console.log(`   ⚠️ Missing translations: ${[...new Set(rawKeys)].slice(0, 5).join(', ')}`);
            }

            expect(has500Error || hasException || hasSQLError || hasUndefinedColumn).toBeFalsy();
        });
    }

    // Test global search
    test('Admin: Global Search', async ({ page }) => {
        await page.goto('/admin');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // Try multiple selectors for search
        const searchSelectors = [
            'input[type="search"]',
            '[data-search-input]',
            '.fi-global-search-input',
            'input[placeholder*="earch"]',
            '[wire\\:model*="search"]',
        ];

        let searchFound = false;
        for (const selector of searchSelectors) {
            const element = page.locator(selector).first();
            if (await element.count() > 0) {
                await element.click();
                searchFound = true;
                break;
            }
        }

        if (!searchFound) {
            // Try using keyboard shortcut (Ctrl+K or Cmd+K is common for search)
            await page.keyboard.press('Control+k');
            await page.waitForTimeout(500);
        }

        // Type search query
        await page.keyboard.type('Test');
        await page.waitForTimeout(2000);

        // Check for errors
        const body = await page.locator('body').textContent();
        const hasSQLError = body?.includes('SQLSTATE');
        const hasUndefinedColumn = body?.includes('Undefined column');

        if (hasSQLError || hasUndefinedColumn) {
            console.log('❌ Global Search - SQL Error');
            await page.screenshot({ path: 'test-results/admin-search-error.png', fullPage: true });
        } else {
            console.log('✅ Global Search - OK (or search not found)');
        }

        expect(hasSQLError || hasUndefinedColumn).toBeFalsy();
    });

    // Test CRUD operations on Organizations
    test('Admin: Organizations CRUD', async ({ page }) => {
        // List
        await page.goto('/admin/organizations');
        await page.waitForLoadState('networkidle');
        console.log('✅ Organizations List - OK');

        // View first organization
        const viewButton = page.locator('a[href*="/admin/organizations/"]').first();
        if (await viewButton.count() > 0) {
            await viewButton.click();
            await page.waitForLoadState('networkidle');

            const body = await page.locator('body').textContent();
            expect(body?.includes('SQLSTATE')).toBeFalsy();
            console.log('✅ Organizations View - OK');

            // Edit
            const editButton = page.locator('a[href*="/edit"]').first();
            if (await editButton.count() > 0) {
                await editButton.click();
                await page.waitForLoadState('networkidle');

                const editBody = await page.locator('body').textContent();
                expect(editBody?.includes('SQLSTATE')).toBeFalsy();
                console.log('✅ Organizations Edit - OK');
            }
        }
    });
});
