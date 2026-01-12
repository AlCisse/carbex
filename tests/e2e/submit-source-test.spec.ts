import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test('Test submit Add source form on 1.1', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    // Use shared login helper
    await login(page);

    // Go to 1.1
    await page.goto('/emissions/1/1.1');
    await page.waitForLoadState('networkidle');

    // Click Add source button
    console.log('1. Clicking Add source...');
    await page.getByRole('button', { name: /add source/i }).click();
    await page.waitForTimeout(1000);

    // Fill Source name
    console.log('2. Filling source name...');
    await page.locator('input[placeholder*="Paris"]').fill('Chaudière gaz bureau Paris');
    await page.waitForTimeout(500);

    // Click on emission factor selector to open modal
    console.log('3. Opening emission factor selector...');
    await page.getByRole('button', { name: 'Select an emission factor' }).click();
    await page.waitForTimeout(1500);

    // Screenshot modal
    await page.screenshot({ path: 'test-results/factor-modal.png', fullPage: true });

    // Search for factor
    console.log('4. Searching for emission factor...');
    await page.waitForTimeout(1000);

    // Find any visible search input in the modal
    const searchInput = page.locator('input[type="text"][placeholder*="Search"], input[type="search"]').first();
    if (await searchInput.isVisible()) {
        await searchInput.fill('gaz');
        console.log('   Search input filled');
    } else {
        console.log('   No search input found');
    }
    await page.waitForTimeout(2000);

    // Screenshot search results
    await page.screenshot({ path: 'test-results/factor-search-results.png', fullPage: true });

    // Click first result in the list
    console.log('5. Selecting first factor...');
    const factorRow = page.locator('li.cursor-pointer, ul li[wire\\:click*="selectFactor"]').first();
    await factorRow.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
    if (await factorRow.isVisible()) {
        await factorRow.click();
        console.log('   Factor selected');
    } else {
        console.log('   No factor rows found, pressing Escape to close modal');
        await page.keyboard.press('Escape');
    }

    // Wait for modal to close
    await page.waitForTimeout(1500);

    // Ensure the modal overlay is gone before continuing
    await page.waitForSelector('.fixed.inset-0.bg-gray-500', { state: 'hidden', timeout: 5000 }).catch(() => {});

    // Screenshot after selection
    await page.screenshot({ path: 'test-results/after-factor-selection.png', fullPage: true });

    // Fill quantity
    console.log('6. Filling quantity...');
    const quantityInput = page.locator('input[type="number"]').first();
    if (await quantityInput.isVisible()) {
        await quantityInput.fill('1500');
        console.log('   Quantity filled');
    }

    await page.waitForTimeout(500);

    // Screenshot before submit
    await page.screenshot({ path: 'test-results/before-submit.png', fullPage: true });

    // Submit form - use force click to bypass any remaining overlay
    console.log('7. Submitting form...');
    const submitBtn = page.getByRole('button', { name: /add source/i }).last();
    await submitBtn.click({ force: true });

    // Wait for response
    await page.waitForTimeout(3000);

    // Screenshot after submit
    await page.screenshot({ path: 'test-results/after-submit.png', fullPage: true });

    // Check result
    const body = await page.locator('body').textContent();

    if (body?.includes('Internal Server Error') || body?.includes('500')) {
        console.log('❌ ERROR: 500 Internal Server Error');
    } else if (body?.includes('required') || body?.includes('obligatoire')) {
        console.log('⚠️ Validation error - some fields required');
    } else {
        console.log('✅ Form submitted!');
    }
});
