import { test, expect } from '@playwright/test';
import { AdminLoginPage, AdminDashboardPage } from '../../pages';

/**
 * Authentication Tests
 * Tests login, logout, and protected routes
 */
test.describe('Admin Authentication', () => {
  test.beforeEach(async ({ page }) => {
    // Clear any existing auth state
    await page.context().clearCookies();
  });

  test('should login with valid credentials', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);

    await loginPage.goto();
    await loginPage.login('admin@example.com', 'password');
    await loginPage.expectSuccessfulLogin();
  });

  test('should show error with invalid credentials', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);

    await loginPage.goto();
    await loginPage.login('wrong@example.com', 'wrongpassword');
    await loginPage.expectLoginError();
  });

  test('should show validation errors for empty fields', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);

    await loginPage.goto();
    await loginPage.loginButton.click();

    // Expect validation errors
    await expect(page.locator('.text-red-500')).toBeVisible();
  });

  test('should logout successfully', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);
    const dashboardPage = new AdminDashboardPage(page);

    // Login first
    await loginPage.goto();
    await loginPage.login('admin@example.com', 'password');
    await loginPage.expectSuccessfulLogin();

    // Logout
    await dashboardPage.logout();

    // Should redirect to login
    await loginPage.expectOnLoginPage();
  });

  test('should redirect to login when accessing protected route without auth', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);

    // Try to access dashboard directly
    await page.goto('/admin');

    // Should be redirected to login
    await loginPage.expectOnLoginPage();
  });

  test('should redirect to intended page after login', async ({ page }) => {
    const loginPage = new AdminLoginPage(page);

    // Try to access projects page directly
    await page.goto('/admin/projects');

    // Should be on login page
    await loginPage.expectOnLoginPage();

    // Login
    await loginPage.login('admin@example.com', 'password');

    // Should redirect to projects page
    await expect(page).toHaveURL(/.*admin\/projects/);
  });
});
