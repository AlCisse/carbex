import { test, expect } from '@playwright/test';

test.describe('Home Page', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('should load the home page', async ({ page }) => {
    // Check that the page loads with Carbex title
    await expect(page).toHaveTitle(/Carbex/);
  });

  test('should display hero section', async ({ page }) => {
    // Check hero title is visible
    const heroTitle = page.locator('h1');
    await expect(heroTitle).toBeVisible();

    // Check CTA buttons are present
    const registerButton = page.locator('a[href*="register"]').first();
    await expect(registerButton).toBeVisible();
  });

  test('should display navigation links', async ({ page }) => {
    // Check navigation links exist in the page
    const featuresLink = page.locator('a[href="#features"]');
    const pricingLink = page.locator('a[href="#pricing"]');

    // At least one of each should be visible (could be in nav or footer)
    await expect(featuresLink.first()).toBeAttached();
    await expect(pricingLink.first()).toBeAttached();
  });

  test('should display features section', async ({ page }) => {
    // Scroll to features section
    const featuresSection = page.locator('#features');
    await featuresSection.scrollIntoViewIfNeeded();

    // Check features section is visible
    await expect(featuresSection).toBeVisible();
  });

  test('should display pricing section with 4 plans', async ({ page }) => {
    // Scroll to pricing section
    const pricingSection = page.locator('#pricing');
    await pricingSection.scrollIntoViewIfNeeded();

    // Check pricing section is visible
    await expect(pricingSection).toBeVisible();

    // Check that pricing cards are present (should have 4 plans)
    const pricingCards = page.locator('#pricing .rounded-2xl');
    await expect(pricingCards).toHaveCount(4);
  });

  test('should have working login link', async ({ page }) => {
    const loginLink = page.locator('a[href*="login"]').first();
    await expect(loginLink).toBeVisible();

    // Click and check navigation
    await loginLink.click();
    await expect(page).toHaveURL(/login/);
  });

  test('should have working register link', async ({ page }) => {
    const registerLink = page.locator('a[href*="register"]').first();
    await expect(registerLink).toBeVisible();

    // Click and check navigation
    await registerLink.click();
    await expect(page).toHaveURL(/register/);
  });

  test('should display footer', async ({ page }) => {
    // Scroll to footer
    const footer = page.locator('footer');
    await footer.scrollIntoViewIfNeeded();

    // Check footer is visible
    await expect(footer).toBeVisible();

    // Check copyright
    await expect(footer).toContainText('Carbex');
  });

  test('should display language selector', async ({ page }) => {
    // The language selector component uses x-data
    const langSelector = page.locator('[x-data]').filter({ hasText: /FR|EN|DE/ }).first();
    await expect(langSelector).toBeVisible();
  });
});
