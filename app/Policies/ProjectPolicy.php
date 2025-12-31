<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('projects.read');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.read') &&
            ($user->id === $project->user_id || $user->hasRole('admin'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.update') &&
            ($user->id === $project->user_id || $user->hasRole('admin'));
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.delete') &&
            ($user->id === $project->user_id || $user->hasRole('admin'));
    }
}

