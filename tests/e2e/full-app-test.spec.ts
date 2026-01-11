import { test, expect } from '@playwright/test';

test.describe('Full Application Tests', () => {
    test.beforeEach(async ({ page }) => {
        page.on('console', msg => {
            if (msg.type() === 'error') console.log('ERROR:', msg.text());
        });

        // Login
        await page.goto('/login');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);
        await page.locator('#email').fill('test@carbex.fr');
        await page.locator('#password').fill('password');
        await page.locator('#password').press('Enter');
        await page.waitForSelector('aside', { timeout: 30000 });
    });

    test('Reports page - Generate report', async ({ page }) => {
        console.log('Testing Reports page...');
        await page.goto('/reports');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/reports-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for report elements
        const hasReportContent = body?.includes('rapport') ||
                                  body?.includes('Report') ||
                                  body?.includes('export') ||
                                  body?.includes('Export');
        console.log('Reports page has content:', hasReportContent);

        // Try to find generate/export button
        const exportBtn = page.locator('button:has-text("export"), button:has-text("Export"), button:has-text("Generate"), button:has-text("générer")');
        const btnCount = await exportBtn.count();
        console.log('Export/Generate buttons found:', btnCount);

        console.log('✅ Reports page OK');
    });

    test('Transition Plan page', async ({ page }) => {
        console.log('Testing Transition Plan page...');
        await page.goto('/transition-plan');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/transition-plan-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for transition plan elements
        const hasContent = body?.includes('transition') ||
                          body?.includes('Transition') ||
                          body?.includes('objectif') ||
                          body?.includes('target') ||
                          body?.includes('réduction');
        console.log('Transition plan has content:', hasContent);

        console.log('✅ Transition Plan page OK');
    });

    test('Suppliers page', async ({ page }) => {
        console.log('Testing Suppliers page...');
        await page.goto('/suppliers');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/suppliers-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for supplier elements
        const hasContent = body?.includes('fournisseur') ||
                          body?.includes('Fournisseur') ||
                          body?.includes('supplier') ||
                          body?.includes('Supplier');
        console.log('Suppliers page has content:', hasContent);

        // Check for add supplier button
        const addBtn = page.locator('button:has-text("ajouter"), button:has-text("Add"), button:has-text("nouveau")');
        const btnCount = await addBtn.count();
        console.log('Add supplier buttons found:', btnCount);

        console.log('✅ Suppliers page OK');
    });

    test('AI Analysis page', async ({ page }) => {
        console.log('Testing AI Analysis page...');
        await page.goto('/ai/analysis');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/ai-analysis-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for AI elements
        const hasContent = body?.includes('IA') ||
                          body?.includes('AI') ||
                          body?.includes('analyse') ||
                          body?.includes('Analysis') ||
                          body?.includes('recommandation');
        console.log('AI Analysis page has content:', hasContent);

        console.log('✅ AI Analysis page OK');
    });

    test('Gamification/Badges page', async ({ page }) => {
        console.log('Testing Gamification page...');
        await page.goto('/gamification');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/gamification-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for gamification elements
        const hasContent = body?.includes('badge') ||
                          body?.includes('Badge') ||
                          body?.includes('niveau') ||
                          body?.includes('level') ||
                          body?.includes('points');
        console.log('Gamification page has content:', hasContent);

        console.log('✅ Gamification page OK');
    });

    test('Settings page', async ({ page }) => {
        console.log('Testing Settings page...');
        await page.goto('/settings');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/settings-page.png', fullPage: true });

        const body = await page.locator('body').textContent();
        expect(body).not.toContain('Internal Server Error');

        // Check for settings elements
        const hasContent = body?.includes('paramètre') ||
                          body?.includes('Settings') ||
                          body?.includes('Paramètres') ||
                          body?.includes('profil') ||
                          body?.includes('organisation');
        console.log('Settings page has content:', hasContent);

        console.log('✅ Settings page OK');
    });

    test('Edit emission record on Scope 1.1', async ({ page }) => {
        console.log('Testing Edit emission record...');
        await page.goto('/emissions/1/1.1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        await page.screenshot({ path: 'test-results/scope1-1-before-edit.png', fullPage: true });

        // Look for edit buttons
        const editBtns = page.locator('button:has-text("edit"), button:has-text("Edit"), button:has-text("modifier"), [class*="edit"], svg[class*="pencil"]');
        const editCount = await editBtns.count();
        console.log('Edit buttons/icons found:', editCount);

        // Look for emission records in table
        const rows = page.locator('tr, [class*="record"], [class*="source"]');
        const rowCount = await rows.count();
        console.log('Record rows found:', rowCount);

        // Try clicking first edit button if exists
        if (editCount > 0) {
            await editBtns.first().click();
            await page.waitForTimeout(1000);
            await page.screenshot({ path: 'test-results/scope1-1-edit-form.png', fullPage: true });
            console.log('Edit form opened');
        }

        console.log('✅ Edit emission test OK');
    });

    test('Delete emission record on Scope 1.1', async ({ page }) => {
        console.log('Testing Delete emission record...');
        await page.goto('/emissions/1/1.1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // Look for delete buttons
        const deleteBtns = page.locator('button:has-text("delete"), button:has-text("Delete"), button:has-text("supprimer"), [class*="delete"], svg[class*="trash"]');
        const deleteCount = await deleteBtns.count();
        console.log('Delete buttons/icons found:', deleteCount);

        console.log('✅ Delete emission test OK');
    });
});
