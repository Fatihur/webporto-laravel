import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Projects Page Object
 */
export class AdminProjectsPage extends BasePage {
  readonly searchInput: Locator;
  readonly categoryFilter: Locator;
  readonly createButton: Locator;
  readonly projectsTable: Locator;

  constructor(page: Page) {
    super(page);
    this.searchInput = page.getByPlaceholder(/search/i);
    this.categoryFilter = page.getByLabel(/category/i);
    this.createButton = page.getByRole('link', { name: /add project|new project|create/i });
    this.projectsTable = page.locator('table, [role="table"]').first();
  }

  /**
   * Navigate to admin projects page
   */
  async goto() {
    await this.navigate('/admin/projects');
    await this.waitForPageLoad();
  }

  /**
   * Search for project
   */
  async search(query: string) {
    await this.searchInput.fill(query);
    await this.page.keyboard.press('Enter');
    await this.page.waitForTimeout(500); // Wait for debounce
  }

  /**
   * Filter by category
   */
  async filterByCategory(category: string) {
    await this.categoryFilter.selectOption(category);
    await this.page.waitForTimeout(500);
  }

  /**
   * Click create new project button
   */
  async clickCreate() {
    await this.createButton.click();
  }

  /**
   * Click edit project
   */
  async clickEdit(projectName: string) {
    const projectRow = this.page.locator('tr').filter({ hasText: projectName });
    await projectRow.getByRole('link', { name: /edit/i }).click();
  }

  /**
   * Click delete project
   */
  async clickDelete(projectName: string) {
    const projectRow = this.page.locator('tr').filter({ hasText: projectName });
    await projectRow.getByRole('button', { name: /delete/i }).click();
  }

  /**
   * Confirm delete in modal
   */
  async confirmDelete() {
    const confirmButton = this.page.getByRole('button', { name: /delete|confirm|yes/i });
    await confirmButton.click();
  }

  /**
   * Expect project to be in list
   */
  async expectProjectInList(projectName: string) {
    const project = this.page.locator('tr, .project-item').filter({ hasText: projectName });
    await expect(project).toBeVisible();
  }

  /**
   * Expect project not in list
   */
  async expectProjectNotInList(projectName: string) {
    const project = this.page.locator('tr, .project-item').filter({ hasText: projectName });
    await expect(project).not.toBeVisible();
  }

  /**
   * Sort by column
   */
  async sortBy(columnName: string) {
    const header = this.page.getByRole('columnheader', { name: new RegExp(columnName, 'i') });
    await header.click();
    await this.page.waitForTimeout(500);
  }
}

/**
 * Admin Project Form Page Object
 */
export class AdminProjectFormPage extends BasePage {
  readonly titleInput: Locator;
  readonly slugInput: Locator;
  readonly descriptionEditor: Locator;
  readonly contentEditor: Locator;
  readonly categorySelect: Locator;
  readonly projectDateInput: Locator;
  readonly featuredCheckbox: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.titleInput = page.getByLabel(/title/i).first();
    this.slugInput = page.getByLabel(/slug/i);
    this.descriptionEditor = page.locator('[data-testid="description-editor"], .ck-editor__editable').first();
    this.contentEditor = page.locator('[data-testid="content-editor"], .ck-editor__editable').nth(1);
    this.categorySelect = page.getByLabel(/category/i).first();
    this.projectDateInput = page.getByLabel(/project date|date/i);
    this.featuredCheckbox = page.getByLabel(/featured/i);
    this.saveButton = page.getByRole('button', { name: /save|create|update/i });
  }

  /**
   * Fill title
   */
  async fillTitle(title: string) {
    await this.titleInput.fill(title);
  }

  /**
   * Fill slug
   */
  async fillSlug(slug: string) {
    await this.slugInput.fill(slug);
  }

  /**
   * Fill description (CKEditor)
   */
  async fillDescription(description: string) {
    // Wait for CKEditor to initialize
    await this.page.waitForSelector('.ck-editor__editable');
    await this.page.locator('.ck-editor__editable').first().fill(description);
  }

  /**
   * Fill content (CKEditor)
   */
  async fillContent(content: string) {
    await this.page.waitForSelector('.ck-editor__editable');
    await this.page.locator('.ck-editor__editable').nth(1).fill(content);
  }

  /**
   * Select category
   */
  async selectCategory(category: string) {
    await this.categorySelect.selectOption(category);
  }

  /**
   * Fill project date
   */
  async fillProjectDate(date: string) {
    await this.projectDateInput.fill(date);
  }

  /**
   * Toggle featured
   */
  async toggleFeatured() {
    await this.featuredCheckbox.click();
  }

  /**
   * Upload thumbnail
   */
  async uploadThumbnail(filePath: string) {
    const fileInput = this.page.locator('input[type="file"]').first();
    await fileInput.setInputFiles(filePath);
    await this.page.waitForTimeout(1000); // Wait for upload
  }

  /**
   * Add tag
   */
  async addTag(tag: string) {
    const tagInput = this.page.getByPlaceholder(/add tag/i);
    await tagInput.fill(tag);
    await this.page.getByRole('button', { name: /\+|add/i }).filter({ has: tagInput }).click();
  }

  /**
   * Add tech stack
   */
  async addTechStack(tech: string) {
    const techInput = this.page.getByPlaceholder(/add technology/i);
    await techInput.fill(tech);
    await this.page.getByRole('button', { name: /\+|add/i }).filter({ has: techInput }).click();
  }

  /**
   * Add stat
   */
  async addStat(label: string, value: string) {
    await this.page.getByRole('button', { name: /add stat/i }).click();
    const statRows = this.page.locator('[wire\\:key^="stat-"]');
    const lastRow = statRows.last();
    await lastRow.locator('input').first().fill(label);
    await lastRow.locator('input').nth(1).fill(value);
  }

  /**
   * Submit form
   */
  async submit() {
    await this.saveButton.click();
  }

  /**
   * Expect form validation error
   */
  async expectValidationError(fieldName: string) {
    const error = this.page.locator('.text-red-500').filter({ hasText: new RegExp(fieldName, 'i') });
    await expect(error).toBeVisible();
  }
}
