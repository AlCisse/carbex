import { test, expect } from '@playwright/test';

test.describe('Gamification & Dashboard Tests', () => {

    test.beforeEach(async ({ page }) => {
        // Enable console logging for debugging
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));
        page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

        // Login first
        await page.goto('/login');
        await page.waitForLoadState('domcontentloaded');
        await page.waitForLoadState('networkidle');

        // Wait for Livewire to fully initialize (check for wire:id attribute)
        await page.waitForFunction(() => {
            const form = document.querySelector('form[wire\\:submit]');
            return form !== null;
        }, { timeout: 10000 });

        // Wait a bit more for Livewire JS to initialize
        await page.waitForTimeout(1000);

        // Fill email - use direct input manipulation
        const emailInput = page.locator('#email');
        await emailInput.click();
        await emailInput.fill('test@carbex.fr');

        // Trigger input event manually for Livewire
        await emailInput.dispatchEvent('input');
        await page.waitForTimeout(200);

        // Fill password
        const passwordInput = page.locator('#password');
        await passwordInput.click();
        await passwordInput.fill('password');

        // Trigger input event for Livewire
        await passwordInput.dispatchEvent('input');
        await page.waitForTimeout(500);

        // Submit using Enter key on form
        await passwordInput.press('Enter');

        // Wait for any of these conditions
        try {
            await Promise.race([
                page.waitForURL('**/dashboard', { timeout: 15000 }),
                page.waitForSelector('aside', { timeout: 15000 }),
            ]);
        } catch (e) {
            // Take screenshot on failure
            console.log('Login may have failed, checking page state...');
        }

        // Additional wait
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);
    });

    test('should access dashboard after login', async ({ page }) => {
        // Verify dashboard is loaded - check for sidebar
        await expect(page.locator('aside')).toBeVisible({ timeout: 10000 });
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
