import { test, expect } from '@playwright/test';
import { AdminContactsPage } from '../../pages';

/**
 * Contacts Management Tests
 * Tests viewing, searching, filtering, and managing contact messages
 */
test.describe('Admin Contacts', () => {
  let contactsPage: AdminContactsPage;

  test.beforeEach(async ({ page }) => {
    contactsPage = new AdminContactsPage(page);
    await contactsPage.goto();
  });

  test('should display contacts list', async ({ page }) => {
    await expect(page.getByRole('heading', { name: /contacts|messages/i })).toBeVisible();

    // Check for table or contact list
    const contactList = page.locator('table, .contact-list, [role="table"]').first();
    await expect(contactList).toBeVisible();
  });

  test('should search for contacts', async () => {
    await contactsPage.search('test');

    // Wait for search results
    await expect(contactsPage.searchInput).toHaveValue('test');
  });

  test('should filter by read status', async () => {
    await contactsPage.filterByStatus('unread');

    // Check filter is applied
    await expect(contactsPage.statusFilter).toHaveValue('unread');
  });

  test('should filter by unread status', async () => {
    await contactsPage.filterByStatus('read');

    // Check filter is applied
    await expect(contactsPage.statusFilter).toHaveValue('read');
  });

  test('should mark contact as read', async ({ page }) => {
    // This test requires existing contact data
    // First check if any contacts exist
    const contactRows = page.locator('table tbody tr, .contact-item');
    const count = await contactRows.count();

    if (count > 0) {
      const firstContact = await contactRows.first().textContent() || 'Test';
      await contactsPage.markAsRead(firstContact.substring(0, 10));

      // Wait for status update
      await page.waitForTimeout(500);
    } else {
      test.skip();
    }
  });

  test('should mark contact as unread', async ({ page }) => {
    const contactRows = page.locator('table tbody tr, .contact-item');
    const count = await contactRows.count();

    if (count > 0) {
      const firstContact = await contactRows.first().textContent() || 'Test';
      await contactsPage.markAsUnread(firstContact.substring(0, 10));

      await page.waitForTimeout(500);
    } else {
      test.skip();
    }
  });

  test('should view contact details', async ({ page }) => {
    const contactRows = page.locator('table tbody tr, .contact-item');
    const count = await contactRows.count();

    if (count > 0) {
      const firstContact = await contactRows.first().textContent() || 'Test';
      await contactsPage.viewContact(firstContact.substring(0, 10));

      // Should show contact details
      await expect(page.locator('.contact-details, [role="dialog"], .modal')).toBeVisible();
    } else {
      test.skip();
    }
  });

  test('should delete contact with confirmation', async ({ page }) => {
    const contactRows = page.locator('table tbody tr, .contact-item');
    const count = await contactRows.count();

    if (count > 0) {
      const firstContact = await contactRows.first().textContent() || 'Test';
      const contactName = firstContact.substring(0, 10);

      await contactsPage.deleteContact(contactName);
      await contactsPage.confirmDelete();

      // Wait for deletion
      await page.waitForTimeout(1000);
    } else {
      test.skip();
    }
  });

  test('should show unread badge in sidebar', async ({ page }) => {
    // Check if sidebar has unread badge
    const sidebar = page.locator('aside, nav').first();
    const contactsLink = sidebar.getByRole('link', { name: /contacts/i });

    await expect(contactsLink).toBeVisible();

    // Look for badge indicator
    const badge = contactsLink.locator('.badge, .indicator, .unread-count');

    // Badge might or might not be present depending on unread count
    const hasBadge = await badge.isVisible().catch(() => false);

    if (hasBadge) {
      const badgeText = await badge.textContent();
      expect(badgeText).toMatch(/\d+/);
    }
  });
});
