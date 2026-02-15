<?php

namespace App\Data;

class BlogData
{
    public static function all(): array
    {
        return [
            [
                'id' => '1',
                'title' => 'The Future of Minimalism in UI Design',
                'excerpt' => 'Why less is always more in the age of information overload.',
                'date' => 'March 10, 2024',
                'image' => 'https://picsum.photos/1200/600?random=5',
                'category' => 'Design',
                'read_time' => '5 min read',
                'author' => 'Fatih',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'text' => 'In an era where digital interfaces bombard us with information, minimalism has emerged not just as an aesthetic choice, but as a necessity. The philosophy of "less is more" has never been more relevant than in today\'s cluttered digital landscape.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'The Evolution of Digital Minimalism'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'Digital minimalism has evolved from a trend to a fundamental principle of good design. Companies like Apple, Google, and countless startups have embraced clean interfaces that prioritize user intent over visual noise. This shift represents a mature understanding of how humans interact with technology.'
                    ],
                    [
                        'type' => 'quote',
                        'text' => 'Perfection is achieved not when there is nothing more to add, but when there is nothing left to take away.',
                        'author' => 'Antoine de Saint-Exupéry'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'The benefits of minimalist UI extend beyond aesthetics. Studies have shown that users complete tasks faster and with fewer errors when presented with clean, focused interfaces. Cognitive load is reduced, allowing users to focus on what truly matters.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'Key Principles'
                    ],
                    [
                        'type' => 'list',
                        'items' => [
                            'Whitespace is not empty space—it\'s an active design element',
                            'Every element must earn its place on the screen',
                            'Typography can carry the visual weight',
                            'Color should be used intentionally, not decoratively'
                        ]
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'As we move forward, the challenge for designers will be maintaining simplicity while adding functionality. The future belongs to interfaces that feel invisible—tools that serve their purpose without demanding attention.'
                    ]
                ]
            ],
            [
                'id' => '2',
                'title' => 'Optimizing React for Scale',
                'excerpt' => 'Key strategies for maintaining large-scale React codebases.',
                'date' => 'March 15, 2024',
                'image' => 'https://picsum.photos/1200/600?random=6',
                'category' => 'Development',
                'read_time' => '8 min read',
                'author' => 'Fatih',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'text' => 'Building React applications is easy. Building React applications that scale is an art. As your application grows, the decisions you made early on can either enable rapid development or create technical debt that slows your team to a crawl.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'Component Architecture'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'The foundation of a scalable React application lies in its component architecture. Atomic design principles, compound components, and proper separation of concerns are not just theoretical concepts—they\'re practical necessities.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'State Management Strategy'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'One of the most critical decisions in large React applications is how to manage state. While Redux has been the go-to solution for years, modern alternatives like Zustand, Jotai, and React Query offer more targeted approaches.'
                    ],
                    [
                        'type' => 'list',
                        'items' => [
                            'Use local state for component-specific UI',
                            'Context for shared but not global state',
                            'External libraries for server state',
                            'Global state only when truly necessary'
                        ]
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'Performance optimization is another crucial aspect. Code splitting, memoization, and virtualization are techniques every React developer should master. But remember: premature optimization is the root of all evil. Measure first, then optimize.'
                    ]
                ]
            ],
            [
                'id' => '3',
                'title' => 'The Power of Data Visualization',
                'excerpt' => 'Transforming complex data into compelling visual stories.',
                'date' => 'March 22, 2024',
                'image' => 'https://picsum.photos/1200/600?random=10',
                'category' => 'Data Analysis',
                'read_time' => '6 min read',
                'author' => 'Fatih',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'text' => 'Data is the new oil, but raw data is like crude oil—it needs refining to be valuable. Data visualization is the refinery that transforms complex datasets into actionable insights.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'Beyond Bar Charts'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'While bar charts and line graphs have their place, modern data visualization goes far beyond these basics. Interactive dashboards, real-time visualizations, and narrative-driven data stories are becoming the standard.'
                    ],
                    [
                        'type' => 'quote',
                        'text' => 'Numbers have an important story to tell. They rely on you to give them a clear and convincing voice.',
                        'author' => 'Stephen Few'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'The key to effective visualization is understanding your audience. Executives need high-level summaries. Analysts need drill-down capabilities. The general public needs intuitive, engaging visuals.'
                    ]
                ]
            ],
            [
                'id' => '4',
                'title' => 'Cybersecurity in the Cloud Era',
                'excerpt' => 'Best practices for securing modern cloud infrastructure.',
                'date' => 'March 28, 2024',
                'image' => 'https://picsum.photos/1200/600?random=11',
                'category' => 'Security',
                'read_time' => '7 min read',
                'author' => 'Fatih',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'text' => 'As organizations migrate to the cloud, security paradigms must evolve. The traditional castle-and-moat approach no longer suffices in a world where data flows across multiple platforms and devices.'
                    ],
                    [
                        'type' => 'heading',
                        'text' => 'Zero Trust Architecture'
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'Zero Trust is more than a buzzword—it\'s a necessary shift in security philosophy. "Never trust, always verify" is the core principle. Every access request must be authenticated, authorized, and encrypted.'
                    ],
                    [
                        'type' => 'list',
                        'items' => [
                            'Multi-factor authentication everywhere',
                            'Principle of least privilege',
                            'Continuous monitoring and logging',
                            'Automated threat detection and response'
                        ]
                    ],
                    [
                        'type' => 'paragraph',
                        'text' => 'The future of cybersecurity lies in automation and AI. Manual security processes cannot keep pace with automated attacks. Organizations must embrace intelligent security systems that can adapt in real-time.'
                    ]
                ]
            ]
        ];
    }

    public static function find(string $id): ?array
    {
        $posts = self::all();

        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                return $post;
            }
        }

        return null;
    }

    public static function getRelated(string $excludeId, string $category, int $limit = 2): array
    {
        $posts = self::all();
        $related = [];

        foreach ($posts as $post) {
            if ($post['id'] !== $excludeId) {
                $related[] = $post;
            }
        }

        // Sort by same category first
        usort($related, function ($a, $b) use ($category) {
            $aMatch = $a['category'] === $category ? 0 : 1;
            $bMatch = $b['category'] === $category ? 0 : 1;
            return $aMatch - $bMatch;
        });

        return array_slice($related, 0, $limit);
    }
}
