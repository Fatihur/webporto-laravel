import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Login Page Object
 */
export class AdminLoginPage extends BasePage {
  readonly emailInput: Locator;
  readonly passwordInput: Locator;
  readonly rememberMeCheckbox: Locator;
  readonly loginButton: Locator;
  readonly errorMessage: Locator;

  constructor(page: Page) {
    super(page);
    this.emailInput = page.getByLabel(/email/i);
    this.passwordInput = page.getByLabel(/password/i);
    this.rememberMeCheckbox = page.getByLabel(/remember me/i);
    this.loginButton = page.getByRole('button', { name: /sign in|login/i });
    this.errorMessage = page.locator('.text-red-500, .error-message, [role="alert"]');
  }

  /**
   * Navigate to admin login page
   */
  async goto() {
    await this.navigate('/admin/login');
    await this.waitForPageLoad();
  }

  /**
   * Login with credentials
   */
  async login(email: string, password: string, rememberMe: boolean = false) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    if (rememberMe) {
      await this.rememberMeCheckbox.check();
    }
    await this.loginButton.click();
  }

  /**
   * Expect login error message
   */
  async expectLoginError(message: string = 'Invalid credentials') {
    await expect(this.errorMessage).toBeVisible();
    await expect(this.errorMessage).toContainText(message);
  }

  /**
   * Expect successful login (redirected to dashboard)
   */
  async expectSuccessfulLogin() {
    await this.expectUrlToContain('/admin');
    await expect(this.page.getByRole('heading', { name: /dashboard|admin/i })).toBeVisible();
  }

  /**
   * Expect to be on login page
   */
  async expectOnLoginPage() {
    await this.expectUrlToContain('/admin/login');
    await expect(this.emailInput).toBeVisible();
    await expect(this.passwordInput).toBeVisible();
  }
}
