<?php

namespace App\Livewire;

use Livewire\Component;

class RandomQuote extends Component
{
    public array $quote = [];

    private array $quotes = [
        [
            'text' => 'Design is not just what it looks like and feels like. Design is how it works.',
            'author' => 'Steve Jobs'
        ],
        [
            'text' => 'Good design is as little design as possible.',
            'author' => 'Dieter Rams'
        ],
        [
            'text' => 'Less is more.',
            'author' => 'Mies van der Rohe'
        ],
        [
            'text' => 'Simplicity is the ultimate sophistication.',
            'author' => 'Leonardo da Vinci'
        ],
        [
            'text' => 'Talk is cheap. Show me the code.',
            'author' => 'Linus Torvalds'
        ],
        [
            'text' => 'First, solve the problem. Then, write the code.',
            'author' => 'John Johnson'
        ],
        [
            'text' => 'Any sufficiently advanced technology is indistinguishable from magic.',
            'author' => 'Arthur C. Clarke'
        ],
        [
            'text' => 'The best way to predict the future is to invent it.',
            'author' => 'Alan Kay'
        ],
        [
            'text' => 'Creativity is intelligence having fun.',
            'author' => 'Albert Einstein'
        ],
        [
            'text' => 'Make it simple, but significant.',
            'author' => 'Don Draper'
        ],
        [
            'text' => 'The details are not the details. They make the design.',
            'author' => 'Charles Eames'
        ],
        [
            'text' => 'Everything is designed. Few things are designed well.',
            'author' => 'Brian Reed'
        ],
        [
            'text' => 'UI is the saddle, the stirrups, \u0026 the reins. UX is the feeling you get riding the horse.',
            'author' => 'Dain Miller'
        ],
        [
            'text' => 'A user interface is like a joke. If you have to explain it, it\'s not that good.',
            'author' => 'Martin LeBlanc'
        ],
        [
            'text' => 'Design is thinking made visual.',
            'author' => 'Saul Bass'
        ],
        [
            'text' => 'Learning never exhausts the mind.',
            'author' => 'Leonardo da Vinci'
        ],
        [
            'text' => 'The only way to do great work is to love what you do.',
            'author' => 'Steve Jobs'
        ],
        [
            'text' => 'Clean code always looks like it was written by someone who cares.',
            'author' => 'Robert C. Martin'
        ],
        [
            'text' => 'Software is like entropy: It is difficult to grasp, weighs nothing, and obeys the Second Law of Thermodynamics.',
            'author' => 'Norman Augustine'
        ],
        [
            'text' => 'Data is the new oil. But unlike oil, data is infinite.',
            'author' => 'Unknown'
        ],
        [
            'text' => 'Security is always excessive until it\'s not enough.',
            'author' => 'Robbie Sinclair'
        ],
        [
            'text' => 'The network is the computer.',
            'author' => 'John Gage'
        ],
        [
            'text' => 'Innovation distinguishes between a leader and a follower.',
            'author' => 'Steve Jobs'
        ],
        [
            'text' => 'Form follows function.',
            'author' => 'Louis Sullivan'
        ],
        [
            'text' => 'Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away.',
            'author' => 'Antoine de Saint-Exupéry'
        ],
    ];

    public function mount(): void
    {
        $this->refreshQuote();
    }

    public function refreshQuote(): void
    {
        $this->quote = $this->quotes[array_rand($this->quotes)];
    }

    public function render()
    {
        return view('livewire.random-quote');
    }
}
