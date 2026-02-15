<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /** @test */
    public function authenticated_user_can_view_contacts_list(): void
    {
        Contact::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get('/admin/contacts');

        $response->assertStatus(200);
    }

    /** @test */
    public function contacts_list_shows_unread_first(): void
    {
        $readContact = Contact::factory()->read()->create(['created_at' => now()->subDay()]);
        $unreadContact = Contact::factory()->unread()->create(['created_at' => now()]);

        $response = $this->actingAs($this->user)
            ->get('/admin/contacts');

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_can_submit_contact_form(): void
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.',
        ];

        $response = $this->post('/contact', $contactData);

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function contact_requires_name(): void
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => 'john@example.com',
            'subject' => 'Test',
            'message' => 'Message',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function contact_requires_email(): void
    {
        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => '',
            'subject' => 'Test',
            'message' => 'Message',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function contact_requires_valid_email(): void
    {
        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'subject' => 'Test',
            'message' => 'Message',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function contact_requires_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test',
            'message' => '',
        ]);

        $response->assertSessionHasErrors('message');
    }

    /** @test */
    public function authenticated_user_can_mark_contact_as_read(): void
    {
        $contact = Contact::factory()->unread()->create();

        $response = $this->actingAs($this->user)
            ->patch("/admin/contacts/{$contact->id}/read");

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'is_read' => true,
        ]);
        $this->assertNotNull($contact->fresh()->read_at);
    }

    /** @test */
    public function authenticated_user_can_mark_contact_as_unread(): void
    {
        $contact = Contact::factory()->read()->create();

        $response = $this->actingAs($this->user)
            ->patch("/admin/contacts/{$contact->id}/unread");

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'is_read' => false,
        ]);
        $this->assertNull($contact->fresh()->read_at);
    }

    /** @test */
    public function authenticated_user_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
