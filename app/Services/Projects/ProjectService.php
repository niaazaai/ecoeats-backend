<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class ProjectService
{
    public function list(User $user, array $filters = [], string $sort = 'created_at', array $page = []): LengthAwarePaginator
    {
        $query = Project::query();

        // Apply filters
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        // Apply sorting
        $sortDirection = 'asc';
        if (str_starts_with($sort, '-')) {
            $sort = substr($sort, 1);
            $sortDirection = 'desc';
        }
        $query->orderBy($sort, $sortDirection);

        // Pagination
        $perPage = $page['size'] ?? 15;
        $pageNumber = $page['number'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $pageNumber);
    }

    public function find(User $user, string $id): Project
    {
        $project = Project::findOrFail($id);

        Gate::authorize('view', $project);

        return $project;
    }

    public function create(User $user, array $data): Project
    {
        Gate::authorize('create', Project::class);

        return Project::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'user_id' => $user->id,
        ]);
    }

    public function update(User $user, string $id, array $data): Project
    {
        $project = Project::findOrFail($id);

        Gate::authorize('update', $project);

        $project->update($data);

        return $project->fresh();
    }

    public function delete(User $user, string $id): void
    {
        $project = Project::findOrFail($id);

        Gate::authorize('delete', $project);

        $project->delete();
    }
}

