import { test, expect } from '@playwright/test';

test.describe('Language Switcher', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('should display language selector with current locale', async ({ page }) => {
    // Check that language selector exists - it shows a flag and locale code
    const languageButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ });
    await expect(languageButton.first()).toBeVisible();
  });

  test('should open dropdown when clicked', async ({ page }) => {
    // Find and click language selector button
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();

    // Wait for Alpine.js transition
    await page.waitForTimeout(200);

    // Check dropdown is visible with language options
    await expect(page.locator('text=Français')).toBeVisible();
    await expect(page.locator('text=English')).toBeVisible();
    await expect(page.locator('text=Deutsch')).toBeVisible();
  });

  test('should switch to English', async ({ page }) => {
    // Click language selector
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();
    await page.waitForTimeout(200);

    // Click English option
    await page.locator('a').filter({ hasText: 'English' }).click();

    // Wait for page to reload
    await page.waitForLoadState('networkidle');

    // Check that page is now in English - look for English-specific text
    // The page title or content should now be in English
    await expect(page.locator('body')).toContainText(/Features|Pricing|carbon footprint/i);
  });

  test('should switch to German', async ({ page }) => {
    // Click language selector
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();
    await page.waitForTimeout(200);

    // Click German option
    await page.locator('a').filter({ hasText: 'Deutsch' }).click();

    // Wait for page to reload
    await page.waitForLoadState('networkidle');

    // Check that page is now in German
    await expect(page.locator('body')).toContainText(/Funktionen|Preise|CO2-Fußabdruck/i);
  });

  test('should switch back to French', async ({ page }) => {
    // First switch to English
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();
    await page.waitForTimeout(200);
    await page.locator('a').filter({ hasText: 'English' }).click();
    await page.waitForLoadState('networkidle');

    // Now switch back to French
    const langButton2 = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton2.click();
    await page.waitForTimeout(200);
    await page.locator('a').filter({ hasText: 'Français' }).click();
    await page.waitForLoadState('networkidle');

    // Check that page is now in French
    await expect(page.locator('body')).toContainText(/Fonctionnalités|Tarifs|empreinte carbone/i);
  });

  test('should persist language when navigating to login', async ({ page }) => {
    // Switch to English
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();
    await page.waitForTimeout(200);
    await page.locator('a').filter({ hasText: 'English' }).click();
    await page.waitForLoadState('networkidle');

    // Navigate to login
    await page.goto('/login');

    // Check that login page is in English
    await expect(page.locator('body')).toContainText(/Email|Password|Sign in|Login/i);
  });
});
