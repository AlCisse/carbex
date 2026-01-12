import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';

test('Debug custom factor creation', async ({ page }) => {
    // Enable console logging
    page.on('console', msg => console.log('PAGE:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
    page.on('response', response => {
        if (response.status() >= 400) {
            console.log(`HTTP ${response.status()}: ${response.url()}`);
        }
    });

    await login(page);

    console.log('1. Navigating to /emissions/1/1.1...');
    await page.goto('/emissions/1/1.1');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    console.log('2. Clicking Add source button...');
    await page.getByRole('button', { name: /add source|ajouter|hinzufügen/i }).click();
    await page.waitForTimeout(500);

    console.log('3. Filling source name...');
    const nameInput = page.getByPlaceholder(/paris office|exemple|beispiel/i);
    await nameInput.fill('Custom Factor Debug Test');

    console.log('4. Opening emission factor selector...');
    await page.getByRole('button', { name: /select an emission factor|sélectionner|auswählen/i }).click();
    await page.waitForTimeout(1000);

    await page.screenshot({ path: 'test-results/debug-cf-1-modal.png' });

    console.log('5. Clicking Create custom factor button...');
    const customButton = page.getByRole('button', { name: /create custom|créer.*personnalisé|eigenen.*erstellen/i });
    await expect(customButton).toBeVisible({ timeout: 5000 });
    await customButton.click();
    await page.waitForTimeout(1000);

    await page.screenshot({ path: 'test-results/debug-cf-2-custom-modal.png' });

    console.log('6. Checking custom factor form fields...');

    // List all visible inputs
    const inputs = await page.locator('input:visible').all();
    console.log(`   Found ${inputs.length} visible inputs`);
    for (let i = 0; i < inputs.length; i++) {
        const placeholder = await inputs[i].getAttribute('placeholder');
        const id = await inputs[i].getAttribute('id');
        const name = await inputs[i].getAttribute('name');
        const type = await inputs[i].getAttribute('type');
        console.log(`   Input ${i}: id="${id}" name="${name}" type="${type}" placeholder="${placeholder}"`);
    }

    // List all visible textareas
    const textareas = await page.locator('textarea:visible').all();
    console.log(`   Found ${textareas.length} visible textareas`);

    // List all visible selects
    const selects = await page.locator('select:visible').all();
    console.log(`   Found ${selects.length} visible selects`);

    console.log('7. Filling custom factor form...');

    // Try different selectors for factor name
    const factorNameSelectors = [
        'input[wire\\:model="customName"]',
        'input[wire\\:model\\.defer="customName"]',
        'input#customName',
        'input[placeholder*="solar"]',
        'input[placeholder*="On-site"]',
    ];

    let factorNameInput = null;
    for (const selector of factorNameSelectors) {
        const el = page.locator(selector);
        if (await el.isVisible().catch(() => false)) {
            factorNameInput = el;
            console.log(`   Found factor name input with: ${selector}`);
            break;
        }
    }

    if (factorNameInput) {
        await factorNameInput.fill('Test Custom Factor E2E');
        console.log('   Factor name filled');
    } else {
        console.log('   ERROR: Could not find factor name input');
        // Try to find any input in the modal
        const modalInputs = await page.locator('.fixed input:visible').all();
        console.log(`   Modal inputs: ${modalInputs.length}`);
    }

    // Try to fill factor value
    const factorValueSelectors = [
        'input[wire\\:model="customFactorValue"]',
        'input[wire\\:model\\.defer="customFactorValue"]',
        'input#customFactorValue',
        'input[placeholder="0.0000"]',
    ];

    let factorValueInput = null;
    for (const selector of factorValueSelectors) {
        const el = page.locator(selector);
        if (await el.isVisible().catch(() => false)) {
            factorValueInput = el;
            console.log(`   Found factor value input with: ${selector}`);
            break;
        }
    }

    if (factorValueInput) {
        await factorValueInput.fill('0.5');
        console.log('   Factor value filled');
    } else {
        console.log('   ERROR: Could not find factor value input');
    }

    await page.screenshot({ path: 'test-results/debug-cf-3-filled.png' });

    console.log('8. Looking for submit button...');
    const submitButtons = await page.locator('button:visible').all();
    console.log(`   Found ${submitButtons.length} visible buttons`);
    for (let i = 0; i < Math.min(submitButtons.length, 10); i++) {
        const text = await submitButtons[i].textContent();
        const type = await submitButtons[i].getAttribute('type');
        console.log(`   Button ${i}: type="${type}" text="${text?.trim().substring(0, 50)}"`);
    }

    console.log('9. Clicking Create factor button...');
    const createButton = page.getByRole('button', { name: /create factor|créer le facteur|faktor erstellen/i });
    if (await createButton.isVisible().catch(() => false)) {
        await createButton.click({ force: true });
        console.log('   Create button clicked');
    } else {
        console.log('   ERROR: Create button not found');
        // Try alternative
        const altButton = page.locator('button[type="submit"]').filter({ hasText: /create|créer|erstellen/i });
        if (await altButton.isVisible().catch(() => false)) {
            await altButton.click({ force: true });
            console.log('   Alternative submit button clicked');
        }
    }

    await page.waitForTimeout(2000);
    await page.screenshot({ path: 'test-results/debug-cf-4-after-submit.png' });

    console.log('10. Checking result...');
    const pageContent = await page.content();
    if (pageContent.includes('Test Custom Factor E2E')) {
        console.log('   ✅ Custom factor appears to be created!');
    } else {
        console.log('   Custom factor name not found in page');
    }

    // Check for error messages
    const errors = await page.locator('.text-red-500, .text-red-600, .text-danger, [class*="error"]').all();
    if (errors.length > 0) {
        console.log(`   Found ${errors.length} potential error elements`);
        for (const err of errors) {
            const text = await err.textContent();
            if (text?.trim()) {
                console.log(`   Error: ${text.trim()}`);
            }
        }
    }

    console.log('Debug test completed!');
});
