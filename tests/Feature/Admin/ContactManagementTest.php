<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function authenticated_user_can_view_contacts_list(): void
    {
        Contact::factory()->count(5)->create();

        $this->actingAs($this->user)
            ->get('/admin/contacts')
            ->assertOk();
    }

    #[Test]
    public function contacts_list_shows_unread_first(): void
    {
        Contact::factory()->read()->create(['created_at' => now()->subDay()]);
        Contact::factory()->unread()->create(['created_at' => now()]);

        $this->actingAs($this->user)
            ->get('/admin/contacts')
            ->assertOk()
            ->assertSee('new');
    }

    #[Test]
    public function guest_can_submit_contact_form(): void
    {
        Livewire::test(\App\Livewire\ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('project_type', 'software_dev')
            ->set('message', 'This is a test message from contact form.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'software_dev',
        ]);
    }

    #[Test]
    public function contact_requires_name(): void
    {
        Livewire::test(\App\Livewire\ContactForm::class)
            ->set('name', '')
            ->set('email', 'john@example.com')
            ->set('project_type', 'software_dev')
            ->set('message', 'This is a test message from contact form.')
            ->call('submit')
            ->assertHasErrors(['name']);
    }

    #[Test]
    public function contact_requires_email(): void
    {
        Livewire::test(\App\Livewire\ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', '')
            ->set('project_type', 'software_dev')
            ->set('message', 'This is a test message from contact form.')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function contact_requires_valid_email(): void
    {
        Livewire::test(\App\Livewire\ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'not-an-email')
            ->set('project_type', 'software_dev')
            ->set('message', 'This is a test message from contact form.')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function contact_requires_message(): void
    {
        Livewire::test(\App\Livewire\ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('project_type', 'software_dev')
            ->set('message', '')
            ->call('submit')
            ->assertHasErrors(['message']);
    }

    #[Test]
    public function authenticated_user_can_mark_contact_as_read(): void
    {
        $contact = Contact::factory()->unread()->create();
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Admin\Contacts\Index::class)
            ->call('markAsRead', $contact->id);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'is_read' => true,
        ]);
        $this->assertNotNull($contact->fresh()?->read_at);
    }

    #[Test]
    public function authenticated_user_can_mark_contact_as_unread(): void
    {
        $contact = Contact::factory()->read()->create();
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Admin\Contacts\Index::class)
            ->call('markAsUnread', $contact->id);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'is_read' => false,
        ]);
        $this->assertNull($contact->fresh()?->read_at);
    }

    #[Test]
    public function authenticated_user_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();
        $this->actingAs($this->user);

        Livewire::test(\App\Livewire\Admin\Contacts\Index::class)
            ->call('delete', $contact->id);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
