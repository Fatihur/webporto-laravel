import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Contacts Page Object
 */
export class AdminContactsPage extends BasePage {
  readonly searchInput: Locator;
  readonly statusFilter: Locator;

  constructor(page: Page) {
    super(page);
    this.searchInput = page.getByPlaceholder(/search/i);
    this.statusFilter = page.getByLabel(/status/i);
  }

  /**
   * Navigate to admin contacts page
   */
  async goto() {
    await this.navigate('/admin/contacts');
    await this.waitForPageLoad();
  }

  /**
   * Search for contact
   */
  async search(query: string) {
    await this.searchInput.fill(query);
    await this.page.keyboard.press('Enter');
    await this.page.waitForTimeout(500);
  }

  /**
   * Filter by status
   */
  async filterByStatus(status: 'read' | 'unread') {
    await this.statusFilter.selectOption(status);
    await this.page.waitForTimeout(500);
  }

  /**
   * Mark contact as read
   */
  async markAsRead(contactName: string) {
    const contactRow = this.page.locator('tr').filter({ hasText: contactName });
    await contactRow.getByRole('button', { name: /mark as read/i }).click();
  }

  /**
   * Mark contact as unread
   */
  async markAsUnread(contactName: string) {
    const contactRow = this.page.locator('tr').filter({ hasText: contactName });
    await contactRow.getByRole('button', { name: /mark as unread/i }).click();
  }

  /**
   * Delete contact
   */
  async deleteContact(contactName: string) {
    const contactRow = this.page.locator('tr').filter({ hasText: contactName });
    await contactRow.getByRole('button', { name: /delete/i }).click();
  }

  /**
   * Confirm delete
   */
  async confirmDelete() {
    const confirmButton = this.page.getByRole('button', { name: /delete|confirm|yes/i });
    await confirmButton.click();
  }

  /**
   * View contact details
   */
  async viewContact(contactName: string) {
    const contactRow = this.page.locator('tr').filter({ hasText: contactName });
    await contactRow.getByRole('button', { name: /view/i }).click();
  }

  /**
   * Expect contact in list
   */
  async expectContactInList(contactName: string) {
    const contact = this.page.locator('tr, .contact-item').filter({ hasText: contactName });
    await expect(contact).toBeVisible();
  }

  /**
   * Expect contact status
   */
  async expectContactStatus(contactName: string, status: 'read' | 'unread') {
    const contactRow = this.page.locator('tr').filter({ hasText: contactName });
    const statusIndicator = contactRow.locator('.status, .badge, .indicator');
    await expect(statusIndicator).toContainText(status, { ignoreCase: true });
  }
}
