import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test.describe('Gamification & Dashboard Tests', () => {

    test.beforeEach(async ({ page }) => {
        // Enable console logging for debugging
        page.on('console', msg => {
            if (msg.type() === 'error') console.log('PAGE ERROR:', msg.text());
        });
        page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

        // Use shared login helper
        await login(page);
    });

    test('should access dashboard after login', async ({ page }) => {
        // Verify dashboard is loaded - check for sidebar
        await expect(page.locator('aside')).toBeVisible();
    });

    test('should display sidebar navigation', async ({ page }) => {
        // Check sidebar elements
        await expect(page.locator('aside').first()).toBeVisible();
        // Use more specific selectors for scope items in sidebar
        await expect(page.locator('aside').getByText('Scope 1', { exact: false }).first()).toBeVisible();
        await expect(page.locator('aside').getByText('Scope 2', { exact: false }).first()).toBeVisible();
        await expect(page.locator('aside').getByText('Scope 3', { exact: false }).first()).toBeVisible();
    });

    test('should navigate to gamification page', async ({ page }) => {
        // Click on Badges link in sidebar
        await page.getByRole('link', { name: /badges/i }).click();
        await page.waitForLoadState('networkidle');

        // Verify gamification page content
        await expect(page.locator('body')).toContainText(/badge/i);
    });

    test('should display gamification page content', async ({ page }) => {
        await page.goto('/gamification');
        await page.waitForLoadState('networkidle');

        // Check for gamification-related content
        await expect(page.locator('body')).toContainText(/badge/i);
    });

    test('should navigate to AI Analysis page', async ({ page }) => {
        await page.getByRole('link', { name: /analyse ia/i }).click();
        await page.waitForLoadState('networkidle');

        // Verify we're on AI analysis page
        await expect(page.locator('body')).toContainText(/recommandation|analyse|ia/i);
    });

    test('should navigate to Suppliers page', async ({ page }) => {
        await page.getByRole('link', { name: /fournisseurs/i }).click();
        await page.waitForLoadState('networkidle');

        // Verify we're on suppliers page
        await expect(page.locator('body')).toContainText(/fournisseur/i);
    });

    test('should navigate to Reports page', async ({ page }) => {
        await page.getByRole('link', { name: /rapports/i }).click();
        await page.waitForLoadState('networkidle');

        // Verify we're on reports page
        await expect(page.locator('body')).toContainText(/rapport|export/i);
    });

    test('should navigate to Transition Plan page', async ({ page }) => {
        await page.getByRole('link', { name: /plan de transition/i }).click();
        await page.waitForLoadState('networkidle');

        // Verify we're on transition plan page
        await expect(page.locator('body')).toContainText(/transition|plan|action/i);
    });
});
