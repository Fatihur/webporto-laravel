import { test, expect } from '@playwright/test';
import { AdminExperiencesPage, AdminExperienceFormPage } from '../../pages';

/**
 * Experiences CRUD Tests
 * Tests experience listing, create, edit, delete, reordering
 */
test.describe('Admin Experiences', () => {
  let experiencesPage: AdminExperiencesPage;
  let experienceFormPage: AdminExperienceFormPage;

  test.beforeEach(async ({ page }) => {
    experiencesPage = new AdminExperiencesPage(page);
    experienceFormPage = new AdminExperienceFormPage(page);
    await experiencesPage.goto();
  });

  test('should display experiences list', async ({ page }) => {
    await expect(page.getByRole('heading', { name: /experience/i })).toBeVisible();

    // Check for experience list
    const experienceList = page.locator('table, .experience-list, [role="table"]').first();
    await expect(experienceList).toBeVisible();
  });

  test('should navigate to create experience page', async ({ page }) => {
    await experiencesPage.clickCreate();

    await expect(page).toHaveURL(/.*admin\/experiences\/create/);
    await expect(page.getByRole('heading', { name: /add experience|new experience|create/i })).toBeVisible();
  });

  test('should create new experience with required fields', async ({ page }) => {
    const timestamp = Date.now();
    const companyName = `Test Company ${timestamp}`;

    await experiencesPage.clickCreate();

    // Fill form
    await experienceFormPage.fillExperienceForm({
      company: companyName,
      role: 'Test Developer',
      description: 'Test experience description',
      startDate: '2023-01-01',
      endDate: '2024-01-01',
      isCurrent: false,
    });

    // Submit
    await experienceFormPage.submit();

    // Wait for redirect to experiences list
    await expect(page).toHaveURL(/.*admin\/experiences/);

    // Verify experience was created
    await experiencesPage.expectExperienceInList(companyName);
  });

  test('should create current position experience', async ({ page }) => {
    const timestamp = Date.now();
    const companyName = `Current Company ${timestamp}`;

    await experiencesPage.clickCreate();

    await experienceFormPage.fillCompany(companyName);
    await experienceFormPage.fillRole('Senior Developer');
    await experienceFormPage.fillDescription('Current position description');
    await experienceFormPage.fillStartDate('2023-01-01');
    await experienceFormPage.toggleCurrent();

    // End date should be disabled
    await experienceFormPage.expectEndDateDisabled();

    await experienceFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/experiences/);
    await experiencesPage.expectExperienceInList(companyName);
  });

  test('should show validation errors for empty required fields', async ({ page }) => {
    await experiencesPage.clickCreate();

    // Try to submit empty form
    await experienceFormPage.submit();

    // Should show validation errors
    await expect(page.locator('.text-red-500')).toBeVisible();
  });

  test('should edit existing experience', async ({ page }) => {
    // First create an experience
    const timestamp = Date.now();
    const companyName = `Edit Company ${timestamp}`;

    await experiencesPage.clickCreate();
    await experienceFormPage.fillExperienceForm({
      company: companyName,
      role: 'Developer',
      startDate: '2023-01-01',
      endDate: '2023-12-31',
    });
    await experienceFormPage.submit();

    // Go back to experiences list
    await experiencesPage.goto();

    // Edit the experience
    await experiencesPage.clickEdit(companyName);

    await expect(page).toHaveURL(/.*admin\/experiences\/.*\/edit/);

    // Update role
    await experienceFormPage.fillRole('Senior Developer');
    await experienceFormPage.submit();

    // Verify update
    await experiencesPage.goto();
    await experiencesPage.expectExperienceInList(companyName);
  });

  test('should delete experience with confirmation', async ({ page }) => {
    // First create an experience to delete
    const timestamp = Date.now();
    const companyName = `Delete Company ${timestamp}`;

    await experiencesPage.clickCreate();
    await experienceFormPage.fillExperienceForm({
      company: companyName,
      role: 'Temp Developer',
      startDate: '2023-01-01',
      endDate: '2023-06-01',
    });
    await experienceFormPage.submit();

    // Go back to experiences list
    await experiencesPage.goto();

    // Delete the experience
    await experiencesPage.clickDelete(companyName);
    await experiencesPage.confirmDelete();

    // Wait for deletion
    await page.waitForTimeout(1000);

    // Verify experience is deleted
    await experiencesPage.goto();
    await experiencesPage.expectExperienceNotInList(companyName);
  });

  test('should reorder experiences', async ({ page }) => {
    // Create multiple experiences
    const timestamp = Date.now();

    for (let i = 1; i <= 3; i++) {
      await experiencesPage.clickCreate();
      await experienceFormPage.fillExperienceForm({
        company: `Reorder Company ${i} ${timestamp}`,
        role: `Role ${i}`,
        startDate: '2023-01-01',
        endDate: '2023-12-31',
      });
      await experienceFormPage.submit();
      await experiencesPage.goto();
    }

    // Try to move first item down
    const firstCompany = `Reorder Company 1 ${timestamp}`;
    await experiencesPage.moveDown(firstCompany);

    // Wait for reorder
    await page.waitForTimeout(1000);
  });

  test('should fill complete experience form', async ({ page }) => {
    const timestamp = Date.now();

    await experiencesPage.clickCreate();

    await experienceFormPage.fillCompany(`Complete Company ${timestamp}`);
    await experienceFormPage.fillRole('Full Stack Developer');
    await experienceFormPage.fillDescription('Complete experience description with details about responsibilities and achievements.');
    await experienceFormPage.fillStartDate('2020-03-15');
    await experienceFormPage.fillEndDate('2023-08-30');

    await experienceFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/experiences/);
  });
});
