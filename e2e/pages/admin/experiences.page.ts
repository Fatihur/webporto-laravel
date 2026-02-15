import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from '../base.page';

/**
 * Admin Experiences Page Object
 */
export class AdminExperiencesPage extends BasePage {
  readonly createButton: Locator;

  constructor(page: Page) {
    super(page);
    this.createButton = page.getByRole('link', { name: /add experience|new experience|create/i });
  }

  /**
   * Navigate to admin experiences page
   */
  async goto() {
    await this.navigate('/admin/experiences');
    await this.waitForPageLoad();
  }

  /**
   * Click create new experience
   */
  async clickCreate() {
    await this.createButton.click();
  }

  /**
   * Click edit experience
   */
  async clickEdit(company: string) {
    const experienceRow = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await experienceRow.getByRole('link', { name: /edit/i }).click();
  }

  /**
   * Click delete experience
   */
  async clickDelete(company: string) {
    const experienceRow = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await experienceRow.getByRole('button', { name: /delete/i }).click();
  }

  /**
   * Move experience up in order
   */
  async moveUp(company: string) {
    const experienceRow = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await experienceRow.getByRole('button', { name: /up|↑/i }).click();
  }

  /**
   * Move experience down in order
   */
  async moveDown(company: string) {
    const experienceRow = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await experienceRow.getByRole('button', { name: /down|↓/i }).click();
  }

  /**
   * Confirm delete
   */
  async confirmDelete() {
    const confirmButton = this.page.getByRole('button', { name: /delete|confirm|yes/i });
    await confirmButton.click();
  }

  /**
   * Expect experience in list
   */
  async expectExperienceInList(company: string) {
    const experience = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await expect(experience).toBeVisible();
  }

  /**
   * Expect experience not in list
   */
  async expectExperienceNotInList(company: string) {
    const experience = this.page.locator('tr, .experience-item').filter({ hasText: company });
    await expect(experience).not.toBeVisible();
  }

  /**
   * Expect experience position in list
   */
  async expectExperiencePosition(company: string, position: number) {
    const experiences = this.page.locator('tr, .experience-item');
    const companyAtPosition = experiences.nth(position - 1);
    await expect(companyAtPosition).toContainText(company);
  }
}

/**
 * Admin Experience Form Page Object
 */
export class AdminExperienceFormPage extends BasePage {
  readonly companyInput: Locator;
  readonly roleInput: Locator;
  readonly descriptionInput: Locator;
  readonly startDateInput: Locator;
  readonly endDateInput: Locator;
  readonly currentCheckbox: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.companyInput = page.getByLabel(/company/i);
    this.roleInput = page.getByLabel(/role|position/i);
    this.descriptionInput = page.getByLabel(/description/i);
    this.startDateInput = page.getByLabel(/start date/i);
    this.endDateInput = page.getByLabel(/end date/i);
    this.currentCheckbox = page.getByLabel(/current|present/i);
    this.saveButton = page.getByRole('button', { name: /save|create|update/i });
  }

  /**
   * Fill company
   */
  async fillCompany(company: string) {
    await this.companyInput.fill(company);
  }

  /**
   * Fill role
   */
  async fillRole(role: string) {
    await this.roleInput.fill(role);
  }

  /**
   * Fill description
   */
  async fillDescription(description: string) {
    await this.descriptionInput.fill(description);
  }

  /**
   * Fill start date
   */
  async fillStartDate(date: string) {
    await this.startDateInput.fill(date);
  }

  /**
   * Fill end date
   */
  async fillEndDate(date: string) {
    await this.endDateInput.fill(date);
  }

  /**
   * Toggle current position
   */
  async toggleCurrent() {
    await this.currentCheckbox.click();
  }

  /**
   * Submit form
   */
  async submit() {
    await this.saveButton.click();
  }

  /**
   * Expect end date to be disabled
   */
  async expectEndDateDisabled() {
    await expect(this.endDateInput).toBeDisabled();
  }

  /**
   * Fill complete experience form
   */
  async fillExperienceForm(data: {
    company: string;
    role: string;
    description?: string;
    startDate: string;
    endDate?: string;
    isCurrent?: boolean;
  }) {
    await this.fillCompany(data.company);
    await this.fillRole(data.role);
    if (data.description) {
      await this.fillDescription(data.description);
    }
    await this.fillStartDate(data.startDate);

    if (data.isCurrent) {
      await this.toggleCurrent();
    } else if (data.endDate) {
      await this.fillEndDate(data.endDate);
    }
  }
}
