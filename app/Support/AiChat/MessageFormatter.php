<?php

declare(strict_types=1);

namespace App\Support\AiChat;

class MessageFormatter
{
    /**
     * Format message by converting markdown to HTML and extracting buttons/suggestions.
     *
     * @return array{text: string, buttons: array<int, array<string, mixed>>, suggestions: array<int, array<string, string>>, gameInputs: array<int, array<string, mixed>>}
     */
    public function format(string $content): array
    {
        $gameInputs = [];

        if (preg_match('/\[INPUT:(.*?)\|(game_answer)\]/', $content, $matches)) {
            $gameInputs[] = [
                'type' => 'number',
                'action' => $matches[2],
            ];
            $content = preg_replace('/\[INPUT:(.*?)\|game_answer\]/', '', $content);
        }

        if (preg_match('/\[SELECT:(game_answer)\|(.*?)\]/', $content, $matches)) {
            $options = explode(',', $matches[2]);
            $gameInputs[] = [
                'type' => 'select',
                'action' => $matches[1],
                'options' => array_map('trim', $options),
            ];
            $content = preg_replace('/\[SELECT:game_answer\|.*?\]/', '', $content);
        }

        $buttons = [];
        $content = preg_replace_callback(
            '/\[BUTTON:(.*?)\|(.*?)\]/',
            function ($matches) use (&$buttons) {
                $buttons[] = [
                    'label' => trim($matches[1]),
                    'url' => trim($matches[2]),
                    'isGameAction' => str_starts_with(trim($matches[2]), 'game:'),
                ];

                return '';
            },
            $content
        );

        $suggestions = [];
        $content = preg_replace_callback(
            '/\[SUGGEST:(.*?)\|(.*?)\]/',
            function ($matches) use (&$suggestions) {
                $suggestions[] = [
                    'label' => trim($matches[1]),
                    'question' => trim($matches[2]),
                ];

                return '';
            },
            $content
        );

        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-semibold text-zinc-900 dark:text-white">$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);

        $content = preg_replace_callback('/```[\s\S]*?```/', function ($matches) {
            $code = trim(substr($matches[0], 3, -3));

            return "<code class=\"bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs font-mono\">{$code}</code>";
        }, $content);

        $content = preg_replace('/`(.*?)`/', '<code class="bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs font-mono">$1</code>', $content);

        $content = preg_replace_callback('/\[(.*?)\]\(https?:\/\/.*?\)/', function ($matches) {
            $text = $matches[1];
            $url = preg_replace('/\[(.*?)\]\((.*?)\)/', '$2', $matches[0]);

            return "<a href=\"{$url}\" target=\"_blank\" rel=\"noopener\" class=\"text-mint hover:underline\">{$text}</a>";
        }, $content);

        $content = preg_replace('/\n/', '<br>', $content);
        $content = preg_replace('/(<br>\s*){3,}/', '<br><br>', $content);

        return [
            'text' => trim($content),
            'buttons' => $buttons,
            'suggestions' => $suggestions,
            'gameInputs' => $gameInputs,
        ];
    }
}
