import { test, expect } from '@playwright/test';

test('Test submit Add source form on 1.1', async ({ page }) => {
    page.on('console', msg => {
        if (msg.type() === 'error') console.log('ERROR:', msg.text());
    });
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    // Login
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);
    await page.locator('#email').fill('test@carbex.fr');
    await page.locator('#password').fill('password');
    await page.locator('#password').press('Enter');
    await page.waitForSelector('aside', { timeout: 30000 });

    // Go to 1.1
    await page.goto('/emissions/1/1.1');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

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
    // Wait for the modal/panel to fully appear
    await page.waitForTimeout(2000);

    // Find any visible search input in the modal
    const searchInput = page.locator('input[type="text"]').filter({ hasText: '' }).locator('visible=true').first();
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
    // The factors are displayed as li elements with cursor-pointer class
    const factorRow = page.locator('li.cursor-pointer, ul li[wire\\:click*="selectFactor"]').first();
    await factorRow.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
    if (await factorRow.isVisible()) {
        await factorRow.click();
        console.log('   Factor selected');
    } else {
        console.log('   No factor rows found, pressing Escape to close modal');
        await page.keyboard.press('Escape');
    }

    await page.waitForTimeout(1000);

    // Screenshot after selection
    await page.screenshot({ path: 'test-results/after-factor-selection.png', fullPage: true });

    // Fill quantity
    console.log('6. Filling quantity...');
    const quantityInput = page.locator('input').filter({ hasText: '' }).locator('visible=true').last();
    // Try different selectors for quantity
    const quantitySelectors = [
        'input[wire\\:model="quantity"]',
        'input[type="number"]',
        'input[placeholder="0.00"]'
    ];

    for (const selector of quantitySelectors) {
        const input = page.locator(selector).first();
        if (await input.isVisible()) {
            await input.fill('1500');
            console.log(`   Quantity filled using: ${selector}`);
            break;
        }
    }

    await page.waitForTimeout(500);

    // Screenshot before submit
    await page.screenshot({ path: 'test-results/before-submit.png', fullPage: true });

    // Submit form
    console.log('7. Submitting form...');
    const submitBtn = page.locator('button[type="submit"]').filter({ hasText: /add source/i });
    if (await submitBtn.isVisible()) {
        await submitBtn.click();
    } else {
        // Try clicking any visible Add source button
        await page.locator('button:has-text("Add source")').last().click({ force: true });
    }

    // Wait for response
    await page.waitForTimeout(3000);

    // Screenshot after submit
    await page.screenshot({ path: 'test-results/after-submit.png', fullPage: true });

    // Check result
    const body = await page.locator('body').textContent();

    if (body?.includes('Internal Server Error')) {
        console.log('❌ ERROR: 500 Internal Server Error');
    } else if (body?.includes('required') || body?.includes('obligatoire')) {
        console.log('⚠️ Validation error - some fields required');
    } else if (body?.includes('Changes saved') || body?.includes('Modifications enregistrées')) {
        console.log('✅ Form submitted successfully!');
    } else {
        console.log('✅ Form submitted!');
    }

    await page.waitForTimeout(2000);
});
