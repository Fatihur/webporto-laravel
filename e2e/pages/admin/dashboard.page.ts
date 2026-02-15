import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Dashboard Page Object
 */
export class AdminDashboardPage extends BasePage {
  readonly sidebar: Locator;
  readonly header: Locator;
  readonly statsCards: Locator;

  constructor(page: Page) {
    super(page);
    this.sidebar = page.locator('aside, nav').first();
    this.header = page.locator('header').first();
    this.statsCards = page.locator('.stats-card, [data-testid="stat-card"]').or(page.locator('.bg-white, .dark\\:bg-zinc-900').filter({ hasText: /projects|blogs|contacts|experiences/i }));
  }

  /**
   * Navigate to admin dashboard
   */
  async goto() {
    await this.navigate('/admin');
    await this.waitForPageLoad();
  }

  /**
   * Click sidebar navigation link
   */
  async clickNavLink(name: string) {
    await this.sidebar.getByRole('link', { name: new RegExp(name, 'i') }).click();
  }

  /**
   * Click logout button
   */
  async logout() {
    const logoutButton = this.sidebar.getByRole('button', { name: /logout/i });
    await logoutButton.click();
  }

  /**
   * Click quick action button
   */
  async clickQuickAction(name: string) {
    await this.page.getByRole('button', { name: new RegExp(name, 'i') }).click();
  }

  /**
   * Expect to see specific stat card
   */
  async expectStatCard(name: string) {
    const card = this.page.locator('.bg-white, .dark\\:bg-zinc-900').filter({ hasText: new RegExp(name, 'i') });
    await expect(card).toBeVisible();
  }

  /**
   * Expect dashboard is loaded
   */
  async expectDashboardLoaded() {
    await expect(this.sidebar).toBeVisible();
    await expect(this.page.getByRole('heading', { name: /dashboard/i })).toBeVisible();
  }

  /**
   * Toggle dark/light mode
   */
  async toggleTheme() {
    const themeToggle = this.page.getByRole('button', { name: /theme|dark|light/i });
    await themeToggle.click();
  }
}
