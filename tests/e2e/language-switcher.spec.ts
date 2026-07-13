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
    await page.waitForTimeout(500);

    // Wait for dropdown to be visible and click English option
    const englishLink = page.locator('a').filter({ hasText: 'English' });
    await englishLink.waitFor({ state: 'visible', timeout: 5000 });
    await englishLink.click();

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
    await page.waitForTimeout(500);

    // Click German option
    const germanLink = page.locator('a').filter({ hasText: 'Deutsch' });
    await germanLink.waitFor({ state: 'visible', timeout: 5000 });
    await germanLink.click();

    // Wait for page to reload
    await page.waitForLoadState('networkidle');

    // Check that page is now in German
    await expect(page.locator('body')).toContainText(/Funktionen|Preise|CO2-Fußabdruck/i);
  });

  test('should switch back to French', async ({ page }) => {
    // First switch to English
    const langButton = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton.click();
    await page.waitForTimeout(500);
    const englishLink = page.locator('a').filter({ hasText: 'English' });
    await englishLink.waitFor({ state: 'visible', timeout: 5000 });
    await englishLink.click();
    await page.waitForLoadState('networkidle');

    // Now switch back to French
    const langButton2 = page.locator('button').filter({ hasText: /🇫🇷|🇬🇧|🇩🇪/ }).first();
    await langButton2.click();
    await page.waitForTimeout(500);
    const frenchLink = page.locator('a').filter({ hasText: 'Français' });
    await frenchLink.waitFor({ state: 'visible', timeout: 5000 });
    await frenchLink.click();
    await page.waitForLoadState('networkidle');

    // Check that page is now in French
    await expect(page.locator('body')).toContainText(/Fonctionnalités|Tarifs|empreinte carbone/i);
  });

  test('should persist language when navigating to login', async ({ page }) => {
    // Switch to English using direct URL
    await page.goto('/language/en');
    await page.waitForLoadState('networkidle');

    // Navigate to login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');

    // Check that login page is in English
    await expect(page.locator('body')).toContainText(/Email|Password|Sign in|Login/i);
  });
});

test.describe('Authenticated pages i18n (DE/FR)', () => {
  // Guards against hardcoded category titles (bug found in the 2026-01 audit):
  // the scope page heading must follow the locale, not ship in a fixed language.
  test('scope page title matches sidebar taxonomy in German', async ({ page }) => {
    const { login } = await import('./helpers/auth');
    await login(page);

    await page.goto('/emissions/3/3.1?lang=de');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('h1, h2').first()).toContainText('Vorgelagerter Gütertransport');
    await expect(page.locator('aside')).toContainText('Vorgelagerter Gütertransport');
  });

  test('scope page title matches sidebar taxonomy in French', async ({ page }) => {
    const { login } = await import('./helpers/auth');
    await login(page);

    await page.goto('/emissions/3/3.1?lang=fr');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('h1, h2').first()).toContainText('Transport de marchandise amont');
    await expect(page.locator('aside')).toContainText('Transport de marchandise amont');
  });
});
