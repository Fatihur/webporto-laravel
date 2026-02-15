<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Experience;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedProjects();
        $this->seedBlogs();
        $this->seedExperiences();
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'Neon Brand Identity',
                'slug' => 'neon-brand-identity',
                'description' => 'A full rebranding for a futuristic tech startup based in Tokyo.',
                'category' => 'graphic-design',
                'content' => '<p>Full visual exploration including typography, color palette, and digital assets.</p>',
                'project_date' => '2023-10-15',
                'tags' => ['Branding', 'Vector', 'Minimalism'],
                'tech_stack' => [],
                'stats' => [],
                'is_featured' => true,
            ],
            [
                'title' => 'Flow SaaS Platform',
                'slug' => 'flow-saas-platform',
                'description' => 'React-based enterprise dashboard with real-time collaboration.',
                'category' => 'software-dev',
                'content' => '<p>Built with React, Node.js and WebSockets.</p>',
                'project_date' => '2023-12-01',
                'tags' => ['React', 'TypeScript', 'Node.js'],
                'tech_stack' => ['React', 'TS', 'Tailwind', 'PostgreSQL'],
                'stats' => [
                    ['label' => 'Users', 'value' => '10K+'],
                    ['label' => 'Uptime', 'value' => '99.9%'],
                ],
                'is_featured' => true,
            ],
            [
                'title' => 'E-commerce Trends 2024',
                'slug' => 'ecommerce-trends-2024',
                'description' => 'Deep dive into consumer behavior using machine learning models.',
                'category' => 'data-analysis',
                'content' => '<p>Analyzed over 2M transactions to predict seasonal shifts.</p>',
                'project_date' => '2024-01-10',
                'tags' => ['Python', 'SQL', 'Tableau'],
                'tech_stack' => ['Python', 'Pandas', 'TensorFlow'],
                'stats' => [
                    ['label' => 'Accuracy', 'value' => '94.2%'],
                    ['label' => 'Data Points', 'value' => '2.1M'],
                    ['label' => 'Revenue Lift', 'value' => '+12%'],
                ],
                'is_featured' => true,
            ],
            [
                'title' => 'HyperNet Infrastructure',
                'slug' => 'hypernet-infrastructure',
                'description' => 'Zero-trust network architecture for a global finance firm.',
                'category' => 'networking',
                'content' => '<p>Implementation of SD-WAN and advanced firewall rules.</p>',
                'project_date' => '2024-02-01',
                'tags' => ['Cisco', 'Security', 'SD-WAN'],
                'tech_stack' => ['Cisco', 'Palo Alto', 'Fortinet'],
                'stats' => [
                    ['label' => 'Uptime', 'value' => '99.99%'],
                    ['label' => 'Latencies', 'value' => '<5ms'],
                ],
                'is_featured' => false,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        $this->command->info('Projects seeded successfully!');
    }

    private function seedBlogs(): void
    {
        $blogs = [
            [
                'title' => 'The Future of Minimalism in UI Design',
                'slug' => 'future-of-minimalism-ui-design',
                'excerpt' => 'Why less is always more in the age of information overload.',
                'content' => '<p>In an era where digital interfaces bombard us with information, minimalism has emerged not just as an aesthetic choice, but as a necessity. The philosophy of "less is more" has never been more relevant than in today\'s cluttered digital landscape.</p>

<h2>The Evolution of Digital Minimalism</h2>
<p>Digital minimalism has evolved from a trend to a fundamental principle of good design. Companies like Apple, Google, and countless startups have embraced clean interfaces that prioritize user intent over visual noise.</p>

<blockquote><p>Perfection is achieved not when there is nothing more to add, but when there is nothing left to take away.</p><cite>— Antoine de Saint-Exupéry</cite></blockquote>

<p>The benefits of minimalist UI extend beyond aesthetics. Studies have shown that users complete tasks faster and with fewer errors when presented with clean, focused interfaces.</p>',
                'category' => 'design',
                'author' => 'Fatih',
                'read_time' => 5,
                'is_published' => true,
                'published_at' => Carbon::parse('2024-03-10'),
            ],
            [
                'title' => 'Optimizing React for Scale',
                'slug' => 'optimizing-react-for-scale',
                'excerpt' => 'Key strategies for maintaining large-scale React codebases.',
                'content' => '<p>Building React applications is easy. Building React applications that scale is an art. As your application grows, the decisions you made early on can either enable rapid development or create technical debt.</p>

<h2>Component Architecture</h2>
<p>The foundation of a scalable React application lies in its component architecture. Atomic design principles, compound components, and proper separation of concerns are practical necessities.</p>

<h2>State Management Strategy</h2>
<p>One of the most critical decisions in large React applications is how to manage state. Modern alternatives like Zustand, Jotai, and React Query offer more targeted approaches than Redux.</p>

<ul>
<li>Use local state for component-specific UI</li>
<li>Context for shared but not global state</li>
<li>External libraries for server state</li>
<li>Global state only when truly necessary</li>
</ul>',
                'category' => 'technology',
                'author' => 'Fatih',
                'read_time' => 8,
                'is_published' => true,
                'published_at' => Carbon::parse('2024-03-15'),
            ],
            [
                'title' => 'The Power of Data Visualization',
                'slug' => 'power-of-data-visualization',
                'excerpt' => 'Transforming complex data into compelling visual stories.',
                'content' => '<p>Data is the new oil, but raw data is like crude oil—it needs refining to be valuable. Data visualization is the refinery that transforms complex datasets into actionable insights.</p>

<h2>Beyond Bar Charts</h2>
<p>While bar charts and line graphs have their place, modern data visualization goes far beyond these basics. Interactive dashboards, real-time visualizations, and narrative-driven data stories are becoming the standard.</p>

<blockquote><p>Numbers have an important story to tell. They rely on you to give them a clear and convincing voice.</p><cite>— Stephen Few</cite></blockquote>',
                'category' => 'insights',
                'author' => 'Fatih',
                'read_time' => 6,
                'is_published' => true,
                'published_at' => Carbon::parse('2024-03-22'),
            ],
            [
                'title' => 'Cybersecurity in the Cloud Era',
                'slug' => 'cybersecurity-cloud-era',
                'excerpt' => 'Best practices for securing modern cloud infrastructure.',
                'content' => '<p>As organizations migrate to the cloud, security paradigms must evolve. The traditional castle-and-moat approach no longer suffices in a world where data flows across multiple platforms and devices.</p>

<h2>Zero Trust Architecture</h2>
<p>Zero Trust is more than a buzzword—it\'s a necessary shift in security philosophy. "Never trust, always verify" is the core principle. Every access request must be authenticated, authorized, and encrypted.</p>

<ul>
<li>Multi-factor authentication everywhere</li>
<li>Principle of least privilege</li>
<li>Continuous monitoring and logging</li>
<li>Automated threat detection and response</li>
</ul>',
                'category' => 'technology',
                'author' => 'Fatih',
                'read_time' => 7,
                'is_published' => true,
                'published_at' => Carbon::parse('2024-03-28'),
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }

        $this->command->info('Blogs seeded successfully!');
    }

    private function seedExperiences(): void
    {
        $experiences = [
            [
                'company' => 'Penta Labs',
                'role' => 'Senior UX Designer',
                'description' => 'Leading design system initiatives and mentoring junior designers. Collaborating with cross-functional teams to deliver high-impact digital products.',
                'start_date' => '2023-01-01',
                'end_date' => null,
                'is_current' => true,
                'order' => 0,
            ],
            [
                'company' => 'DevSync',
                'role' => 'Fullstack Engineer',
                'description' => 'Developed and maintained scalable web applications using React and Node.js. Implemented CI/CD pipelines and improved application performance by 40%.',
                'start_date' => '2021-06-01',
                'end_date' => '2022-12-31',
                'is_current' => false,
                'order' => 1,
            ],
            [
                'company' => 'Moderno',
                'role' => 'UI/Visual Intern',
                'description' => 'Assisted senior designers in creating visual assets for marketing campaigns. Learned industry best practices and design tools.',
                'start_date' => '2020-03-01',
                'end_date' => '2021-05-31',
                'is_current' => false,
                'order' => 2,
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::create($experience);
        }

        $this->command->info('Experiences seeded successfully!');
    }
}
