<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentPublished
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public $content,
        public string $type // 'blog' or 'project'
    ) {}
}
