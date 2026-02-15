<?php

namespace App\Livewire;

use App\Data\CategoryData;
use App\Models\Experience;
use App\Models\Project;
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
        $this->tags = ['Minimalism', 'Precision', 'Scalability', 'Innovation'];

        // About Me data
        $this->aboutMe = [
            'name' => 'Fatih',
            'role' => 'Tech Enthusiast',
            'bio' => "I'm a passionate tech enthusiast with expertise in graphic design, software development, data analysis, and networking. With over 5 years of experience, I specialize in creating elegant solutions that bridge the gap between aesthetics and functionality.",
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
        // Cache featured projects for 1 hour
        $featuredProjects = Cache::remember('projects.featured', 3600, function () {
            return Project::featured()
                ->recent()
                ->limit(3)
                ->get();
        });

        // Cache experiences for 6 hours
        $experiences = Cache::remember('experiences.ordered', 21600, function () {
            return Experience::ordered()
                ->limit(5)
                ->get();
        });

        return view('livewire.home-page', [
            'featuredProjects' => $featuredProjects,
            'experiences' => $experiences,
            'aboutMe' => $this->aboutMe,
        ])->layout('layouts.app');
    }
}
