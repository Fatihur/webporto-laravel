import { test as setup, expect } from '@playwright/test';
import { AdminLoginPage } from './pages/admin/login.page';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const authFile = path.join(__dirname, '../.auth/admin.json');

/**
 * Global authentication setup
 * Runs once before all tests to login and save session state
 */
setup('authenticate as admin', async ({ page }) => {
  const loginPage = new AdminLoginPage(page);

  // Navigate to login page
  await loginPage.goto();

  // Login with test credentials
  // Note: These should be set in environment variables or .env.testing
  const adminEmail = process.env.ADMIN_EMAIL || 'admin@example.com';
  const adminPassword = process.env.ADMIN_PASSWORD || 'password';

  await loginPage.login(adminEmail, adminPassword);

  // Wait for successful login (redirect to dashboard)
  await loginPage.expectSuccessfulLogin();

  // Save authentication state
  await page.context().storageState({ path: authFile });
});
