<?php

namespace App\Jobs;

use App\Mail\ContactFormSubmitted;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Contact $contact) {}

    public function handle(): void
    {
        Mail::to(config('mail.admin_email'))
            ->send(new ContactFormSubmitted($this->contact));
    }
}
