<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Unsubscribe from newsletter using token.
     */
    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return view('newsletter.unsubscribe', [
                'success' => false,
                'message' => 'Invalid or expired unsubscribe link.',
            ]);
        }

        if ($subscriber->isUnsubscribed()) {
            return view('newsletter.unsubscribe', [
                'success' => true,
                'message' => 'You have already unsubscribed from our newsletter.',
            ]);
        }

        $subscriber->unsubscribe();

        return view('newsletter.unsubscribe', [
            'success' => true,
            'message' => 'You have been successfully unsubscribed from our newsletter.',
        ]);
    }
}
