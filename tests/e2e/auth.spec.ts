import { test, expect } from '@playwright/test';

test.describe('Authentication Pages', () => {
  test.describe('Login Page', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto('/login');
    });

    test('should display login form', async ({ page }) => {
      // Check form elements by id (from Livewire component)
      await expect(page.locator('#email')).toBeVisible();
      await expect(page.locator('#password')).toBeVisible();
      await expect(page.locator('button[type="submit"]')).toBeVisible();
    });

    test('should have link to register', async ({ page }) => {
      const registerLink = page.locator('a[href*="register"]');
      await expect(registerLink).toBeVisible();
    });

    test('should have forgot password link', async ({ page }) => {
      const forgotLink = page.locator('a[href*="password"]');
      await expect(forgotLink).toBeVisible();
    });

    test('should show validation for empty email', async ({ page }) => {
      // Try to submit without filling email
      const emailInput = page.locator('#email');
      await emailInput.focus();
      await emailInput.blur();
      await page.locator('#password').fill('password');
      await page.locator('button[type="submit"]').click();

      // Should show HTML5 validation or stay on page
      await expect(page).toHaveURL(/login/);
    });

    test('should show error for invalid credentials', async ({ page }) => {
      // Fill in invalid credentials
      await page.fill('#email', 'invalid@example.com');
      await page.fill('#password', 'wrongpassword');

      // Submit form
      await page.locator('button[type="submit"]').click();

      // Wait for Livewire response
      await page.waitForLoadState('networkidle');

      // Should still be on login page (credentials rejected)
      await expect(page).toHaveURL(/login/);
    });
  });

  test.describe('Register Page', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto('/register');
    });

    test('should display step 1 registration form', async ({ page }) => {
      // Step 1 has name, email, password, password_confirmation
      await expect(page.locator('#name')).toBeVisible();
      await expect(page.locator('#email')).toBeVisible();
      await expect(page.locator('#password')).toBeVisible();
      await expect(page.locator('#password_confirmation')).toBeVisible();
      await expect(page.locator('button[type="submit"]')).toBeVisible();
    });

    test('should have link to login', async ({ page }) => {
      const loginLink = page.locator('a[href*="login"]');
      await expect(loginLink).toBeVisible();
    });

    test('should show progress steps', async ({ page }) => {
      // Should show step indicators (1 and 2)
      await expect(page.locator('text=1')).toBeVisible();
      await expect(page.locator('text=2')).toBeVisible();
    });

    test('should proceed to step 2 with valid data', async ({ page }) => {
      // Fill step 1 form
      await page.fill('#name', 'Test User');
      await page.fill('#email', `test${Date.now()}@example.com`);
      await page.fill('#password', 'Password123!');
      await page.fill('#password_confirmation', 'Password123!');

      // Click next button
      await page.locator('button[type="submit"]').click();

      // Wait for Livewire to process
      await page.waitForTimeout(1000);

      // Should show step 2 elements (organization name)
      await expect(page.locator('#organization_name')).toBeVisible({ timeout: 5000 });
    });

    test('should show country selector in step 2', async ({ page }) => {
      // Fill step 1
      await page.fill('#name', 'Test User');
      await page.fill('#email', `test${Date.now()}@example.com`);
      await page.fill('#password', 'Password123!');
      await page.fill('#password_confirmation', 'Password123!');
      await page.locator('button[type="submit"]').click();

      await page.waitForTimeout(1000);

      // Check step 2 elements
      await expect(page.locator('#country')).toBeVisible({ timeout: 5000 });
      await expect(page.locator('#sector')).toBeVisible();
    });
  });
});
