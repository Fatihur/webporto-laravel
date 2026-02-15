<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class InfiniteScroll extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public bool $hasMorePages = true;
    public array $items = [];
    public bool $isLoading = false;

    protected string $model;
    protected array $with = [];
    protected array $scopes = [];
    protected string $orderBy = 'created_at';
    protected string $orderDirection = 'desc';

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadMore(): void
    {
        if ($this->isLoading || !$this->hasMorePages) {
            return;
        }

        $this->isLoading = true;

        // Increment page
        $this->page++;

        // Load new items
        $newItems = $this->getItems();

        // Merge with existing items
        $this->items = array_merge($this->items, $newItems->items());

        // Check if there are more pages
        $this->hasMorePages = $newItems->hasMorePages();

        $this->isLoading = false;
    }

    protected function loadItems(): void
    {
        $paginator = $this->getItems();
        $this->items = $paginator->items();
        $this->hasMorePages = $paginator->hasMorePages();
    }

    protected function getItems(): LengthAwarePaginator
    {
        $query = app($this->model)::query();

        // Eager load relationships
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        // Apply scopes
        foreach ($this->scopes as $scope => $args) {
            if (is_numeric($scope)) {
                $query->{$args}();
            } else {
                $query->{$scope}(...$args);
            }
        }

        // Order by
        $query->orderBy($this->orderBy, $this->orderDirection);

        return $query->paginate($this->perPage);
    }

    public function refresh(): void
    {
        $this->page = 1;
        $this->items = [];
        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.infinite-scroll');
    }
}
