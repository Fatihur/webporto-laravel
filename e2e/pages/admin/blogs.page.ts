import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Blogs Page Object
 */
export class AdminBlogsPage extends BasePage {
  readonly searchInput: Locator;
  readonly categoryFilter: Locator;
  readonly statusFilter: Locator;
  readonly createButton: Locator;

  constructor(page: Page) {
    super(page);
    this.searchInput = page.getByPlaceholder(/search/i);
    this.categoryFilter = page.getByLabel(/category/i).first();
    this.statusFilter = page.getByLabel(/status/i);
    this.createButton = page.getByRole('link', { name: /add post|new post|create/i });
  }

  /**
   * Navigate to admin blogs page
   */
  async goto() {
    await this.navigate('/admin/blogs');
    await this.waitForPageLoad();
  }

  /**
   * Search for blog post
   */
  async search(query: string) {
    await this.searchInput.fill(query);
    await this.page.keyboard.press('Enter');
    await this.page.waitForTimeout(500);
  }

  /**
   * Filter by category
   */
  async filterByCategory(category: string) {
    await this.categoryFilter.selectOption(category);
    await this.page.waitForTimeout(500);
  }

  /**
   * Filter by status (published/draft)
   */
  async filterByStatus(status: 'published' | 'draft') {
    await this.statusFilter.selectOption(status);
    await this.page.waitForTimeout(500);
  }

  /**
   * Click create new blog post
   */
  async clickCreate() {
    await this.createButton.click();
  }

  /**
   * Click edit blog post
   */
  async clickEdit(blogTitle: string) {
    const blogRow = this.page.locator('tr').filter({ hasText: blogTitle });
    await blogRow.getByRole('link', { name: /edit/i }).click();
  }

  /**
   * Click delete blog post
   */
  async clickDelete(blogTitle: string) {
    const blogRow = this.page.locator('tr').filter({ hasText: blogTitle });
    await blogRow.getByRole('button', { name: /delete/i }).click();
  }

  /**
   * Toggle publish status
   */
  async togglePublish(blogTitle: string) {
    const blogRow = this.page.locator('tr').filter({ hasText: blogTitle });
    await blogRow.getByRole('button', { name: /publish|unpublish/i }).click();
  }

  /**
   * Confirm delete
   */
  async confirmDelete() {
    const confirmButton = this.page.getByRole('button', { name: /delete|confirm|yes/i });
    await confirmButton.click();
  }

  /**
   * Expect blog post in list
   */
  async expectBlogInList(blogTitle: string) {
    const blog = this.page.locator('tr, .blog-item').filter({ hasText: blogTitle });
    await expect(blog).toBeVisible();
  }

  /**
   * Expect blog post not in list
   */
  async expectBlogNotInList(blogTitle: string) {
    const blog = this.page.locator('tr, .blog-item').filter({ hasText: blogTitle });
    await expect(blog).not.toBeVisible();
  }
}

/**
 * Admin Blog Form Page Object
 */
export class AdminBlogFormPage extends BasePage {
  readonly titleInput: Locator;
  readonly slugInput: Locator;
  readonly excerptEditor: Locator;
  readonly contentEditor: Locator;
  readonly categorySelect: Locator;
  readonly authorInput: Locator;
  readonly readTimeInput: Locator;
  readonly publishedAtInput: Locator;
  readonly publishedCheckbox: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.titleInput = page.getByLabel(/title/i).first();
    this.slugInput = page.getByLabel(/slug/i);
    this.excerptEditor = page.locator('.ck-editor__editable').first();
    this.contentEditor = page.locator('.ck-editor__editable').nth(1);
    this.categorySelect = page.getByLabel(/category/i).first();
    this.authorInput = page.getByLabel(/author/i);
    this.readTimeInput = page.getByLabel(/read time/i);
    this.publishedAtInput = page.getByLabel(/publish date|published at/i);
    this.publishedCheckbox = page.getByLabel(/publish/i);
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
   * Fill excerpt (CKEditor)
   */
  async fillExcerpt(excerpt: string) {
    await this.page.waitForSelector('.ck-editor__editable');
    await this.page.locator('.ck-editor__editable').first().fill(excerpt);
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
   * Fill author
   */
  async fillAuthor(author: string) {
    await this.authorInput.fill(author);
  }

  /**
   * Fill read time
   */
  async fillReadTime(minutes: number) {
    await this.readTimeInput.fill(minutes.toString());
  }

  /**
   * Fill published date
   */
  async fillPublishedAt(date: string) {
    await this.publishedAtInput.fill(date);
  }

  /**
   * Toggle published status
   */
  async togglePublished() {
    await this.publishedCheckbox.click();
  }

  /**
   * Upload featured image
   */
  async uploadImage(filePath: string) {
    const fileInput = this.page.locator('input[type="file"]').first();
    await fileInput.setInputFiles(filePath);
    await this.page.waitForTimeout(1000);
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
