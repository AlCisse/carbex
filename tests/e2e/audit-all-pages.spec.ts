import { test, expect } from '@playwright/test';

/**
 * Complete audit of all application pages
 * Checks for 500 errors and missing translations
 */

const publicPages = [
    '/',
    '/tarifs',
    '/pour-qui',
    '/contact',
    '/blog',
    '/cgv',
    '/cgu',
    '/mentions-legales',
    '/nos-engagements',
    '/login',
    '/register',
    '/forgot-password',
];

const authenticatedPages = [
    // Dashboard
    '/dashboard',

    // Settings
    '/settings',
    '/settings/profile',
    '/settings/team',
    '/settings/sites',
    '/settings/billing',

    // Banking & Transactions
    '/banking',
    '/banking/connect',
    '/transactions',
    '/transactions/review',
    '/transactions/import',

    // Documents
    '/documents',

    // Emissions
    '/emissions',
    '/emissions/scope/1',
    '/emissions/scope/2',
    '/emissions/scope/3',
    '/emissions/1/1.1',
    '/emissions/1/1.2',
    '/emissions/1/1.4',
    '/emissions/1/1.5',
    '/emissions/2/2.1',
    '/emissions/2/2.2',
    '/emissions/3/3.1',
    '/emissions/3/3.2',
    '/emissions/3/3.3',
    '/emissions/3/3.5',
    '/emissions/3/4.1',
    '/emissions/3/4.2',
    '/emissions/3/4.3',
    '/emissions/3/4.4',
    '/emissions/3/4.5',
    '/emissions/activities',

    // AI & Analysis
    '/ai-analysis',

    // Suppliers
    '/suppliers',

    // Gamification
    '/gamification',

    // Transition Plan
    '/transition-plan',
    '/transition-plan/actions',
    '/trajectory',

    // Assessments
    '/assessments',

    // Billing
    '/billing',

    // Reports
    '/reports',
];

test.describe('Public Pages Audit', () => {
    for (const page of publicPages) {
        test(`Public: ${page}`, async ({ page: browserPage }) => {
            await browserPage.goto(page);
            await browserPage.waitForLoadState('networkidle');
            await browserPage.waitForTimeout(500);

            const body = await browserPage.locator('body').textContent();
            const has500Error = body?.includes('Internal Server Error');
            const hasException = body?.includes('Exception') && body?.includes('vendor/');

            if (has500Error || hasException) {
                console.log(`❌ ${page} - 500 Error`);
                await browserPage.screenshot({ path: `test-results/audit-error${page.replace(/\//g, '-')}.png`, fullPage: true });
            } else {
                console.log(`✅ ${page} - OK`);
            }

            // Check for raw translation keys
            const rawKeys = body?.match(/linscarbon\.[a-z_\.]+/gi)?.filter(k => !['linscarbon.fr', 'linscarbon.com', 'linscarbon.de', 'linscarbon.io', 'linscarbon.eu'].includes(k));
            if (rawKeys && rawKeys.length > 0) {
                console.log(`   ⚠️ Missing translations: ${[...new Set(rawKeys)].slice(0, 3).join(', ')}`);
            }

            expect(has500Error || hasException).toBeFalsy();
        });
    }
});

test.describe('Authenticated Pages Audit', () => {
    test.beforeEach(async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.waitForTimeout(500);
        await page.fill('#email', 'test@linscarbon.fr');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard**', { timeout: 15000 });
    });

    for (const pagePath of authenticatedPages) {
        test(`Auth: ${pagePath}`, async ({ page }) => {
            await page.goto(pagePath);
            await page.waitForLoadState('networkidle');
            await page.waitForTimeout(1000);

            const body = await page.locator('body').textContent();
            const has500Error = body?.includes('Internal Server Error');
            const hasException = body?.includes('Exception') && body?.includes('vendor/');
            const hasSQLError = body?.includes('SQLSTATE');

            if (has500Error || hasException || hasSQLError) {
                console.log(`❌ ${pagePath} - 500 Error`);
                await page.screenshot({ path: `test-results/audit-error${pagePath.replace(/\//g, '-')}.png`, fullPage: true });
            } else {
                console.log(`✅ ${pagePath} - OK`);
            }

            // Check for raw translation keys
            const rawKeys = body?.match(/linscarbon\.[a-z_\.]+/gi)?.filter(k => !['linscarbon.fr', 'linscarbon.com', 'linscarbon.de', 'linscarbon.io', 'linscarbon.eu'].includes(k));
            if (rawKeys && rawKeys.length > 0) {
                console.log(`   ⚠️ Missing translations: ${[...new Set(rawKeys)].slice(0, 3).join(', ')}`);
            }

            expect(has500Error || hasException || hasSQLError).toBeFalsy();
        });
    }
});
