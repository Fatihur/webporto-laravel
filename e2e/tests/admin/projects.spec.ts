import { test, expect } from '@playwright/test';
import { AdminProjectsPage, AdminProjectFormPage } from '../../pages';

/**
 * Projects CRUD Tests
 * Tests project listing, search, filter, create, edit, delete
 */
test.describe('Admin Projects', () => {
  let projectsPage: AdminProjectsPage;
  let projectFormPage: AdminProjectFormPage;

  test.beforeEach(async ({ page }) => {
    projectsPage = new AdminProjectsPage(page);
    projectFormPage = new AdminProjectFormPage(page);
    await projectsPage.goto();
  });

  test('should display projects list', async ({ page }) => {
    await expect(page.getByRole('heading', { name: /projects/i })).toBeVisible();

    // Check for table or project list
    const projectList = page.locator('table, .project-list, [role="table"]').first();
    await expect(projectList).toBeVisible();
  });

  test('should search for projects', async () => {
    await projectsPage.search('test');

    // Wait for search results
    await expect(projectsPage.searchInput).toHaveValue('test');
  });

  test('should filter by category', async () => {
    await projectsPage.filterByCategory('software-dev');

    // Check filter is applied
    await expect(projectsPage.categoryFilter).toHaveValue('software-dev');
  });

  test('should navigate to create project page', async ({ page }) => {
    await projectsPage.clickCreate();

    await expect(page).toHaveURL(/.*admin\/projects\/create/);
    await expect(page.getByRole('heading', { name: /add project|new project|create/i })).toBeVisible();
  });

  test('should create new project with required fields', async ({ page }) => {
    const timestamp = Date.now();
    const projectTitle = `Test Project ${timestamp}`;

    await projectsPage.clickCreate();

    // Fill form
    await projectFormPage.fillTitle(projectTitle);
    await projectFormPage.fillDescription('This is a test project description');
    await projectFormPage.fillContent('<p>Detailed project content for testing</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');

    // Submit
    await projectFormPage.submit();

    // Wait for redirect to projects list
    await expect(page).toHaveURL(/.*admin\/projects/);

    // Verify project was created
    await projectsPage.expectProjectInList(projectTitle);
  });

  test('should show validation errors for empty required fields', async () => {
    await projectsPage.clickCreate();

    // Try to submit empty form
    await projectFormPage.submit();

    // Should show validation errors
    await expect(page.locator('.text-red-500')).toBeVisible();
  });

  test('should edit existing project', async ({ page }) => {
    // First create a project
    const timestamp = Date.now();
    const projectTitle = `Edit Test ${timestamp}`;

    await projectsPage.clickCreate();
    await projectFormPage.fillTitle(projectTitle);
    await projectFormPage.fillDescription('Original description');
    await projectFormPage.fillContent('<p>Original content</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');
    await projectFormPage.submit();

    // Go back to projects list
    await projectsPage.goto();

    // Edit the project
    await projectsPage.clickEdit(projectTitle);

    await expect(page).toHaveURL(/.*admin\/projects\/.*\/edit/);

    // Update title
    const updatedTitle = `${projectTitle} Updated`;
    await projectFormPage.fillTitle(updatedTitle);
    await projectFormPage.submit();

    // Verify update
    await projectsPage.goto();
    await projectsPage.expectProjectInList(updatedTitle);
  });

  test('should delete project with confirmation', async ({ page }) => {
    // First create a project to delete
    const timestamp = Date.now();
    const projectTitle = `Delete Test ${timestamp}`;

    await projectsPage.clickCreate();
    await projectFormPage.fillTitle(projectTitle);
    await projectFormPage.fillDescription('Project to be deleted');
    await projectFormPage.fillContent('<p>Delete me</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');
    await projectFormPage.submit();

    // Go back to projects list
    await projectsPage.goto();

    // Delete the project
    await projectsPage.clickDelete(projectTitle);
    await projectsPage.confirmDelete();

    // Wait for deletion
    await page.waitForTimeout(1000);

    // Verify project is deleted
    await projectsPage.goto();
    await projectsPage.expectProjectNotInList(projectTitle);
  });

  test('should add tags to project', async ({ page }) => {
    await projectsPage.clickCreate();

    await projectFormPage.fillTitle(`Tag Test ${Date.now()}`);
    await projectFormPage.fillDescription('Test description');
    await projectFormPage.fillContent('<p>Test content</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');

    // Add tags
    await projectFormPage.addTag('Laravel');
    await projectFormPage.addTag('Vue.js');

    await projectFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/projects/);
  });

  test('should add tech stack to project', async ({ page }) => {
    await projectsPage.clickCreate();

    await projectFormPage.fillTitle(`Tech Test ${Date.now()}`);
    await projectFormPage.fillDescription('Test description');
    await projectFormPage.fillContent('<p>Test content</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');

    // Add tech stack
    await projectFormPage.addTechStack('PHP');
    await projectFormPage.addTechStack('MySQL');

    await projectFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/projects/);
  });

  test('should add stats to project', async ({ page }) => {
    await projectsPage.clickCreate();

    await projectFormPage.fillTitle(`Stats Test ${Date.now()}`);
    await projectFormPage.fillDescription('Test description');
    await projectFormPage.fillContent('<p>Test content</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');

    // Add stats
    await projectFormPage.addStat('Client', 'Test Corp');
    await projectFormPage.addStat('Duration', '3 months');

    await projectFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/projects/);
  });

  test('should toggle featured status', async ({ page }) => {
    await projectsPage.clickCreate();

    await projectFormPage.fillTitle(`Featured Test ${Date.now()}`);
    await projectFormPage.fillDescription('Test description');
    await projectFormPage.fillContent('<p>Test content</p>');
    await projectFormPage.selectCategory('software-dev');
    await projectFormPage.fillProjectDate('2024-01-15');

    // Toggle featured
    await projectFormPage.toggleFeatured();

    await projectFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/projects/);
  });
});
