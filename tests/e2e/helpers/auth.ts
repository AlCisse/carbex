import { Page, expect } from '@playwright/test';

/**
 * Shared authentication helper for E2E tests
 * Provides consistent login functionality across all test files
 */

export const TEST_USER = {
    email: 'test@linscarbon.fr',
    password: 'password',
};

/**
 * Perform login and wait for dashboard
 * Uses robust waiting strategy that works with Livewire
 */
export async function login(page: Page, options?: { timeout?: number }): Promise<void> {
    const timeout = options?.timeout ?? 45000;

    // Navigate to login page
    await page.goto('/login');
    await page.waitForLoadState('domcontentloaded');

    // Wait for the form to be ready (Livewire hydration)
    await page.waitForSelector('#email', { state: 'visible', timeout: 10000 });
    await page.waitForTimeout(500); // Small delay for Livewire JS

    // Fill credentials
    await page.locator('#email').fill(TEST_USER.email);
    await page.locator('#password').fill(TEST_USER.password);

    // Submit form
    await page.locator('#password').press('Enter');

    // Wait for successful login - sidebar appears on authenticated pages
    await page.waitForSelector('aside', { state: 'visible', timeout });

    // Ensure page is fully loaded
    await page.waitForLoadState('networkidle');
}

/**
 * Check if user is logged in (sidebar visible)
 */
export async function isLoggedIn(page: Page): Promise<boolean> {
    try {
        await page.waitForSelector('aside', { state: 'visible', timeout: 5000 });
        return true;
    } catch {
        return false;
    }
}

/**
 * Navigate to a protected page with login if needed
 */
export async function navigateAuthenticated(page: Page, path: string): Promise<void> {
    // Check if already logged in
    const loggedIn = await isLoggedIn(page).catch(() => false);

    if (!loggedIn) {
        await login(page);
    }

    await page.goto(path);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(500);
}
