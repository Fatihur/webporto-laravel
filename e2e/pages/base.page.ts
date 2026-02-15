import { Page, Locator, expect } from '@playwright/test';

/**
 * Base Page Object
 * Provides common functionality for all page objects
 */
export abstract class BasePage {
  constructor(protected page: Page) {}

  /**
   * Navigate to a specific path
   */
  async navigate(path: string = '/') {
    await this.page.goto(path);
  }

  /**
   * Wait for page to fully load
   */
  async waitForPageLoad() {
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Get page title
   */
  async getTitle(): Promise<string> {
    return this.page.title();
  }

  /**
   * Get current URL
   */
  async getUrl(): Promise<string> {
    return this.page.url();
  }

  /**
   * Expect URL to contain specific text
   */
  async expectUrlToContain(text: string) {
    await expect(this.page).toHaveURL(new RegExp(text));
  }

  /**
   * Expect success toast/notification
   */
  async expectSuccessMessage(message?: string) {
    const notification = this.page.locator('[role="alert"], .notification, .toast, .success-message').first();
    await expect(notification).toBeVisible();
    if (message) {
      await expect(notification).toContainText(message);
    }
  }

  /**
   * Expect error message
   */
  async expectErrorMessage(message?: string) {
    const error = this.page.locator('.error-message, .text-red-500, [role="alert"]').first();
    await expect(error).toBeVisible();
    if (message) {
      await expect(error).toContainText(message);
    }
  }

  /**
   * Click button with specific text
   */
  async clickButton(name: string) {
    await this.page.getByRole('button', { name }).click();
  }

  /**
   * Click link with specific text
   */
  async clickLink(name: string) {
    await this.page.getByRole('link', { name }).click();
  }

  /**
   * Fill input field
   */
  async fillInput(label: string, value: string) {
    await this.page.getByLabel(label).fill(value);
  }

  /**
   * Select option from dropdown
   */
  async selectOption(label: string, option: string) {
    await this.page.getByLabel(label).selectOption(option);
  }

  /**
   * Check/uncheck checkbox
   */
  async toggleCheckbox(label: string, checked: boolean = true) {
    const checkbox = this.page.getByLabel(label);
    if (checked) {
      await checkbox.check();
    } else {
      await checkbox.uncheck();
    }
  }

  /**
   * Wait for element to be visible
   */
  async waitForElement(selector: string, timeout?: number) {
    await this.page.locator(selector).waitFor({ state: 'visible', timeout });
  }
}
