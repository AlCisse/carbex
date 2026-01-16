import { test, expect } from '@playwright/test';

test.describe('Settings Pages', () => {
    test.beforeEach(async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.waitForTimeout(1000);
        await page.fill('#email', 'test@carbex.fr');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard**', { timeout: 15000 });
    });

    test('Settings main page', async ({ page }) => {
        await page.goto('/settings');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-main.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings - 500 Error');
        } else {
            console.log('✅ /settings - OK');
        }
        
        // Check for raw translation keys
        const rawKeys = body?.match(/carbex\.[a-z_\.]+/gi)?.filter(k => k !== 'carbex.fr');
        if (rawKeys && rawKeys.length > 0) {
            console.log('⚠️ Missing translations:', [...new Set(rawKeys)].slice(0, 5));
        }
    });

    test('Settings profile', async ({ page }) => {
        await page.goto('/settings/profile');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-profile.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/profile - 500 Error');
        } else {
            console.log('✅ /settings/profile - OK');
        }
    });

    test('Settings organization', async ({ page }) => {
        await page.goto('/settings/organization');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-organization.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/organization - 500 Error');
        } else {
            console.log('✅ /settings/organization - OK');
        }
    });

    test('Settings users', async ({ page }) => {
        await page.goto('/settings/users');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-users.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/users - 500 Error');
        } else {
            console.log('✅ /settings/users - OK');
        }
    });

    test('Settings sites', async ({ page }) => {
        await page.goto('/settings/sites');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-sites.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/sites - 500 Error');
        } else {
            console.log('✅ /settings/sites - OK');
        }
    });

    test('Settings API', async ({ page }) => {
        await page.goto('/settings/api');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-api.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/api - 500 Error');
        } else {
            console.log('✅ /settings/api - OK');
        }
    });

    test('Settings notifications', async ({ page }) => {
        await page.goto('/settings/notifications');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-notifications.png', fullPage: true });
        
        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/notifications - 500 Error');
        } else {
            console.log('✅ /settings/notifications - OK');
        }
    });

    test('Settings billing', async ({ page }) => {
        await page.goto('/settings/billing');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-billing.png', fullPage: true });

        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/billing - 500 Error');
        } else {
            console.log('✅ /settings/billing - OK');
        }
    });

    test('Settings team', async ({ page }) => {
        await page.goto('/settings/team');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1500);
        await page.screenshot({ path: 'test-results/settings-team.png', fullPage: true });

        const body = await page.locator('body').textContent();
        if (body?.includes('Internal Server Error')) {
            console.log('❌ /settings/team - 500 Error');
        } else {
            console.log('✅ /settings/team - OK');
        }
    });
});
