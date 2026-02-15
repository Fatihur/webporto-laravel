<?php

namespace App\Data;

class ProjectData
{
    public static function all(): array
    {
        return [
            [
                'id' => '1',
                'title' => 'Neon Brand Identity',
                'description' => 'A full rebranding for a futuristic tech startup based in Tokyo.',
                'category' => 'graphic-design',
                'thumbnail' => 'https://picsum.photos/800/600?random=1',
                'content' => 'Full visual exploration including typography, color palette, and digital assets.',
                'date' => 'Oct 2023',
                'tags' => ['Branding', 'Vector', 'Minimalism'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=10',
                    'https://picsum.photos/800/600?random=11',
                ],
            ],
            [
                'id' => '2',
                'title' => 'Flow SaaS Platform',
                'description' => 'React-based enterprise dashboard with real-time collaboration.',
                'category' => 'software-dev',
                'thumbnail' => 'https://picsum.photos/800/600?random=2',
                'content' => 'Built with React, Node.js and WebSockets.',
                'date' => 'Dec 2023',
                'tags' => ['React', 'TypeScript', 'Node.js'],
                'tech_stack' => ['React', 'TS', 'Tailwind', 'PostgreSQL'],
                'gallery' => [
                    'https://picsum.photos/800/600?random=20',
                    'https://picsum.photos/800/600?random=21',
                    'https://picsum.photos/800/600?random=22',
                ],
            ],
            [
                'id' => '3',
                'title' => 'E-commerce Trends 2024',
                'description' => 'Deep dive into consumer behavior using machine learning models.',
                'category' => 'data-analysis',
                'thumbnail' => 'https://picsum.photos/800/600?random=3',
                'content' => 'Analyzed over 2M transactions to predict seasonal shifts.',
                'date' => 'Jan 2024',
                'tags' => ['Python', 'SQL', 'Tableau'],
                'stats' => [
                    ['label' => 'Accuracy', 'value' => '94.2%'],
                    ['label' => 'Data Points', 'value' => '2.1M'],
                    ['label' => 'Revenue Lift', 'value' => '+12%'],
                ],
                'gallery' => [
                    'https://picsum.photos/800/600?random=30',
                    'https://picsum.photos/800/600?random=31',
                    'https://picsum.photos/800/600?random=32',
                ],
            ],
            [
                'id' => '4',
                'title' => 'HyperNet Infrastructure',
                'description' => 'Zero-trust network architecture for a global finance firm.',
                'category' => 'networking',
                'thumbnail' => 'https://picsum.photos/800/600?random=4',
                'content' => 'Implementation of SD-WAN and advanced firewall rules.',
                'date' => 'Feb 2024',
                'tags' => ['Cisco', 'Security', 'SD-WAN'],
                'stats' => [
                    ['label' => 'Uptime', 'value' => '99.99%'],
                    ['label' => 'Latencies', 'value' => '<5ms'],
                ],
                'gallery' => [
                    'https://picsum.photos/800/600?random=40',
                    'https://picsum.photos/800/600?random=41',
                    'https://picsum.photos/800/600?random=42',
                ],
            ],
        ];
    }

    public static function find(string $id): ?array
    {
        $projects = self::all();

        foreach ($projects as $project) {
            if ($project['id'] === $id) {
                return $project;
            }
        }

        return null;
    }

    public static function byCategory(string $category): array
    {
        return array_filter(self::all(), function ($project) use ($category) {
            return $project['category'] === $category;
        });
    }
}
