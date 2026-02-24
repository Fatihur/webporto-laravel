<?php

namespace App\Livewire;

use App\Data\CategoryData;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteContact;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class HomePage extends Component
{
    public array $categories = [];

    public array $tags = [];

    public array $aboutMe = [];

    public function mount(): void
    {
        // Cache static category data for 24 hours
        $this->categories = Cache::remember('categories.all', 86400, function () {
            return CategoryData::all();
        });
        $this->tags = [
            'minimalism' => 'Minimalism',
            'precision' => 'Precision',
            'scalability' => 'Scalability',
            'innovation' => 'Innovation',
        ];

        // About Me data
        $this->aboutMe = [
            'name' => 'Fatihurroyyan',
            'role' => 'Tech Enthusiast',
            'bio' => "Hi, I'm Fatihurroyyan — also known as Fatih. A passionate tech enthusiast with expertise in graphic design, software development, data analysis, and networking. I specialize in creating elegant solutions that bridge the gap between aesthetics and functionality.",
            'email' => 'fatihur17@gmail.com',
            // 'socials' => [
            //     'github' => 'https://github.com/fatih',
            //     'linkedin' => 'https://linkedin.com/in/fatih',
            //     'twitter' => 'https://twitter.com/fatih',
            // ]
        ];
    }

    public function render()
    {
        $cacheConfig = config('performance.cache.home', []);
        $homeVersion = (int) Cache::get('cache.version.home', 1);

        $featuredProjects = Cache::flexible("home.v{$homeVersion}.projects.featured", [
            (int) ($cacheConfig['featured_projects_fresh'] ?? 900),
            (int) ($cacheConfig['featured_projects_stale'] ?? 3600),
        ], function () {
            return Project::featured()
                ->recent()
                ->limit(3)
                ->get();
        });

        $experiences = Cache::flexible("home.v{$homeVersion}.experiences.ordered", [
            (int) ($cacheConfig['experiences_fresh'] ?? 3600),
            (int) ($cacheConfig['experiences_stale'] ?? 21600),
        ], function () {
            return Experience::ordered()
                ->limit(5)
                ->get();
        });

        $siteContact = Cache::flexible("home.v{$homeVersion}.site-contact", [
            (int) ($cacheConfig['site_contact_fresh'] ?? 300),
            (int) ($cacheConfig['site_contact_stale'] ?? 1200),
        ], function (): ?SiteContact {
            return SiteContact::getSettings();
        });

        return view('livewire.home-page', [
            'featuredProjects' => $featuredProjects,
            'experiences' => $experiences,
            'aboutMe' => $this->aboutMe,
            'siteContact' => $siteContact,
        ])->layout('layouts.app');
    }
}
