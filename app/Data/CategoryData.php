<?php

namespace App\Data;

class CategoryData
{
    public static function all(): array
    {
        return [
            [
                'id' => 'graphic-design',
                'name' => 'Graphic Design',
                'icon' => 'palette',
                'color' => 'bg-mint',
                'description' => 'Visual identity, branding, and motion graphics.',
            ],
            [
                'id' => 'software-dev',
                'name' => 'Software Dev',
                'icon' => 'code',
                'color' => 'bg-zinc-950 text-white dark:bg-white dark:text-black',
                'description' => 'Full-stack applications and scalable architectures.',
            ],
            [
                'id' => 'data-analysis',
                'name' => 'Data Analysis',
                'icon' => 'chart',
                'color' => 'bg-violet',
                'description' => 'Insightful reporting and complex data processing.',
            ],
            [
                'id' => 'networking',
                'name' => 'Networking',
                'icon' => 'network',
                'color' => 'bg-lime',
                'description' => 'Robust infrastructure and cloud security.',
            ],
        ];
    }

    public static function find(string $id): ?array
    {
        $categories = self::all();

        foreach ($categories as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
        }

        return null;
    }

    public static function getNavLinks(): array
    {
        return [
            ['name' => 'Home', 'path' => route('home'), 'route' => 'home'],
            ['name' => 'Projects', 'path' => route('projects.category', 'graphic-design'), 'route' => 'projects.category'],
            ['name' => 'Blog', 'path' => route('blog.index'), 'route' => 'blog.index'],
            ['name' => 'Contact', 'path' => route('contact.index'), 'route' => 'contact.index'],
        ];
    }
}
