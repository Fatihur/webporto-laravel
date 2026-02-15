import { test, expect } from '@playwright/test';
import { AdminBlogsPage, AdminBlogFormPage } from '../../pages';

/**
 * Blogs CRUD Tests
 * Tests blog listing, search, filter, create, edit, delete, publish/unpublish
 */
test.describe('Admin Blogs', () => {
  let blogsPage: AdminBlogsPage;
  let blogFormPage: AdminBlogFormPage;

  test.beforeEach(async ({ page }) => {
    blogsPage = new AdminBlogsPage(page);
    blogFormPage = new AdminBlogFormPage(page);
    await blogsPage.goto();
  });

  test('should display blogs list', async ({ page }) => {
    await expect(page.getByRole('heading', { name: /blog|posts/i })).toBeVisible();

    // Check for table or blog list
    const blogList = page.locator('table, .blog-list, [role="table"]').first();
    await expect(blogList).toBeVisible();
  });

  test('should search for blog posts', async () => {
    await blogsPage.search('test');

    // Wait for search results
    await expect(blogsPage.searchInput).toHaveValue('test');
  });

  test('should filter by category', async () => {
    await blogsPage.filterByCategory('technology');

    // Check filter is applied
    await expect(blogsPage.categoryFilter).toHaveValue('technology');
  });

  test('should filter by status', async () => {
    await blogsPage.filterByStatus('published');

    // Check filter is applied
    await expect(blogsPage.statusFilter).toHaveValue('published');
  });

  test('should navigate to create blog page', async ({ page }) => {
    await blogsPage.clickCreate();

    await expect(page).toHaveURL(/.*admin\/blogs\/create/);
    await expect(page.getByRole('heading', { name: /new post|create post|add blog/i })).toBeVisible();
  });

  test('should create new blog post with required fields', async ({ page }) => {
    const timestamp = Date.now();
    const blogTitle = `Test Blog ${timestamp}`;

    await blogsPage.clickCreate();

    // Fill form
    await blogFormPage.fillTitle(blogTitle);
    await blogFormPage.fillExcerpt('This is a test blog excerpt');
    await blogFormPage.fillContent('<p>Detailed blog content for testing</p>');
    await blogFormPage.selectCategory('technology');
    await blogFormPage.fillAuthor('Test Author');
    await blogFormPage.fillReadTime(5);

    // Submit
    await blogFormPage.submit();

    // Wait for redirect to blogs list
    await expect(page).toHaveURL(/.*admin\/blogs/);

    // Verify blog was created
    await blogsPage.expectBlogInList(blogTitle);
  });

  test('should show validation errors for empty required fields', async ({ page }) => {
    await blogsPage.clickCreate();

    // Try to submit empty form
    await blogFormPage.submit();

    // Should show validation errors
    await expect(page.locator('.text-red-500')).toBeVisible();
  });

  test('should create published blog post', async ({ page }) => {
    const timestamp = Date.now();
    const blogTitle = `Published Blog ${timestamp}`;

    await blogsPage.clickCreate();

    await blogFormPage.fillTitle(blogTitle);
    await blogFormPage.fillExcerpt('Published blog excerpt');
    await blogFormPage.fillContent('<p>Published content</p>');
    await blogFormPage.selectCategory('technology');
    await blogFormPage.fillReadTime(5);

    // Mark as published
    await blogFormPage.togglePublished();

    await blogFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/blogs/);
    await blogsPage.expectBlogInList(blogTitle);
  });

  test('should edit existing blog post', async ({ page }) => {
    // First create a blog post
    const timestamp = Date.now();
    const blogTitle = `Edit Blog ${timestamp}`;

    await blogsPage.clickCreate();
    await blogFormPage.fillTitle(blogTitle);
    await blogFormPage.fillExcerpt('Original excerpt');
    await blogFormPage.fillContent('<p>Original content</p>');
    await blogFormPage.selectCategory('technology');
    await blogFormPage.fillReadTime(5);
    await blogFormPage.submit();

    // Go back to blogs list
    await blogsPage.goto();

    // Edit the blog
    await blogsPage.clickEdit(blogTitle);

    await expect(page).toHaveURL(/.*admin\/blogs\/.*\/edit/);

    // Update title
    const updatedTitle = `${blogTitle} Updated`;
    await blogFormPage.fillTitle(updatedTitle);
    await blogFormPage.submit();

    // Verify update
    await blogsPage.goto();
    await blogsPage.expectBlogInList(updatedTitle);
  });

  test('should delete blog post with confirmation', async ({ page }) => {
    // First create a blog to delete
    const timestamp = Date.now();
    const blogTitle = `Delete Blog ${timestamp}`;

    await blogsPage.clickCreate();
    await blogFormPage.fillTitle(blogTitle);
    await blogFormPage.fillExcerpt('Blog to be deleted');
    await blogFormPage.fillContent('<p>Delete me</p>');
    await blogFormPage.selectCategory('technology');
    await blogFormPage.fillReadTime(5);
    await blogFormPage.submit();

    // Go back to blogs list
    await blogsPage.goto();

    // Delete the blog
    await blogsPage.clickDelete(blogTitle);
    await blogsPage.confirmDelete();

    // Wait for deletion
    await page.waitForTimeout(1000);

    // Verify blog is deleted
    await blogsPage.goto();
    await blogsPage.expectBlogNotInList(blogTitle);
  });

  test('should toggle publish status from list', async ({ page }) => {
    // First create a draft blog
    const timestamp = Date.now();
    const blogTitle = `Toggle Blog ${timestamp}`;

    await blogsPage.clickCreate();
    await blogFormPage.fillTitle(blogTitle);
    await blogFormPage.fillExcerpt('Toggle test excerpt');
    await blogFormPage.fillContent('<p>Toggle content</p>');
    await blogFormPage.selectCategory('technology');
    await blogFormPage.fillReadTime(5);
    // Leave as draft (not published)
    await blogFormPage.submit();

    // Go back to blogs list
    await blogsPage.goto();

    // Toggle publish status
    await blogsPage.togglePublish(blogTitle);

    // Wait for toggle
    await page.waitForTimeout(1000);

    // Blog should still be in list
    await blogsPage.expectBlogInList(blogTitle);
  });

  test('should fill all blog form fields', async ({ page }) => {
    const timestamp = Date.now();

    await blogsPage.clickCreate();

    await blogFormPage.fillTitle(`Full Test ${timestamp}`);
    await blogFormPage.fillSlug(`full-test-${timestamp}`);
    await blogFormPage.fillExcerpt('Complete excerpt text');
    await blogFormPage.fillContent('<p>Complete content with <strong>formatting</strong></p>');
    await blogFormPage.selectCategory('tutorial');
    await blogFormPage.fillAuthor('John Doe');
    await blogFormPage.fillReadTime(10);
    await blogFormPage.fillPublishedAt('2024-01-15');
    await blogFormPage.togglePublished();

    await blogFormPage.submit();

    // Verify success
    await expect(page).toHaveURL(/.*admin\/blogs/);
  });
});
