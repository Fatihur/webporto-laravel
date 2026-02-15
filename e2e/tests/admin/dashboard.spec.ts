import { test, expect } from '@playwright/test';
import { AdminDashboardPage } from '../../pages';

/**
 * Dashboard Tests
 * Tests dashboard navigation, stats, and UI elements
 */
test.describe('Admin Dashboard', () => {
  let dashboardPage: AdminDashboardPage;

  test.beforeEach(async ({ page }) => {
    dashboardPage = new AdminDashboardPage(page);
    await dashboardPage.goto();
  });

  test('should display dashboard with sidebar navigation', async ({ page }) => {
    await dashboardPage.expectDashboardLoaded();

    // Check sidebar navigation links
    await expect(page.getByRole('link', { name: /dashboard/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /projects/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /blog/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /contacts/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /experience/i })).toBeVisible();
  });

  test('should display statistics cards', async ({ page }) => {
    // Check for stat cards (Projects, Blogs, Contacts, Experiences)
    const statCards = page.locator('.bg-white, .dark\\:bg-zinc-900').filter({
      has: page.locator('text=/projects|blogs|contacts|experiences/i')
    });

    // Should have at least 4 stat cards
    await expect(statCards).toHaveCount(4);
  });

  test('should navigate to projects page', async ({ page }) => {
    await dashboardPage.clickNavLink('Projects');
    await expect(page).toHaveURL(/.*admin\/projects/);
    await expect(page.getByRole('heading', { name: /projects/i })).toBeVisible();
  });

  test('should navigate to blogs page', async ({ page }) => {
    await dashboardPage.clickNavLink('Blog');
    await expect(page).toHaveURL(/.*admin\/blogs/);
    await expect(page.getByRole('heading', { name: /blog|posts/i })).toBeVisible();
  });

  test('should navigate to contacts page', async ({ page }) => {
    await dashboardPage.clickNavLink('Contacts');
    await expect(page).toHaveURL(/.*admin\/contacts/);
    await expect(page.getByRole('heading', { name: /contacts|messages/i })).toBeVisible();
  });

  test('should navigate to experiences page', async ({ page }) => {
    await dashboardPage.clickNavLink('Experience');
    await expect(page).toHaveURL(/.*admin\/experiences/);
    await expect(page.getByRole('heading', { name: /experience/i })).toBeVisible();
  });

  test('should have quick action buttons', async ({ page }) => {
    // Check for quick action buttons
    await expect(page.getByRole('button', { name: /new project|add project/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /new blog|add blog|new post/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /add experience/i })).toBeVisible();
  });

  test('should toggle dark/light mode', async ({ page }) => {
    // Check if theme toggle exists
    const themeToggle = page.getByRole('button', { name: /theme|dark|light/i });

    if (await themeToggle.isVisible().catch(() => false)) {
      // Get initial theme
      const html = page.locator('html');
      const initialClass = await html.getAttribute('class');

      // Toggle theme
      await themeToggle.click();

      // Wait for theme change
      await page.waitForTimeout(300);

      // Check if theme changed
      const newClass = await html.getAttribute('class');
      expect(newClass).not.toBe(initialClass);
    }
  });
});
