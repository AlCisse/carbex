import { test, expect } from '@playwright/test';

test.describe('Full Registration Flow', () => {
  test('should complete full registration process', async ({ page }) => {
    // Generate unique email
    const timestamp = Date.now();
    const testEmail = `test_user_${timestamp}@carbex-test.io`;

    // Navigate to registration page
    await page.goto('/register');

    // === STEP 1: User Information ===
    console.log('Step 1: Filling user information...');

    // Fill name
    await page.fill('#name', 'Max Mustermann');

    // Fill email
    await page.fill('#email', testEmail);

    // Fill password
    await page.fill('#password', 'TestPassword2026!');

    // Fill password confirmation
    await page.fill('#password_confirmation', 'TestPassword2026!');

    // Take screenshot of step 1
    await page.screenshot({ path: 'tests/Browser/screenshots/registration-step1.png' });

    // Click next button (Weiter)
    await page.locator('button[type="submit"]').click();

    // Wait for step 2 to load
    await page.waitForTimeout(2000);

    // === STEP 2: Organization Information ===
    console.log('Step 2: Filling organization information...');

    // Wait for organization name field to be visible
    await expect(page.locator('#organization_name')).toBeVisible({ timeout: 10000 });

    // Fill organization name
    await page.fill('#organization_name', 'Test GmbH');

    // Select country (Germany)
    await page.selectOption('#country', 'DE');

    // Select organization size
    const sizeSelector = page.locator('#organization_size');
    if (await sizeSelector.isVisible()) {
      await sizeSelector.selectOption('11-50');
    }

    // Select sector if available
    const sectorSelector = page.locator('#sector');
    if (await sectorSelector.isVisible()) {
      // Get first available option
      const options = await sectorSelector.locator('option').all();
      if (options.length > 1) {
        const firstValue = await options[1].getAttribute('value');
        if (firstValue) {
          await sectorSelector.selectOption(firstValue);
        }
      }
    }

    // Accept terms checkbox
    const termsCheckbox = page.locator('#accept_terms');
    if (await termsCheckbox.isVisible()) {
      await termsCheckbox.check();
    }

    // Accept privacy checkbox
    const privacyCheckbox = page.locator('#accept_privacy');
    if (await privacyCheckbox.isVisible()) {
      await privacyCheckbox.check();
    }

    // Take screenshot of step 2
    await page.screenshot({ path: 'tests/Browser/screenshots/registration-step2.png' });

    // Submit registration (Konto erstellen)
    await page.locator('button[type="submit"]').click();

    // Wait for registration to process
    await page.waitForTimeout(3000);

    // === VERIFICATION ===
    console.log('Verifying registration result...');

    // Should be redirected away from /register
    await expect(page).not.toHaveURL(/\/register$/);

    // Take screenshot of result
    await page.screenshot({ path: 'tests/Browser/screenshots/registration-result.png' });

    // Log the final URL
    console.log(`Registration completed! Final URL: ${page.url()}`);
  });
});
