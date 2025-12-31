<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProjectRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequest;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use App\Services\Projects\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->list(
            $request->user(),
            $request->query('filter', []),
            $request->query('sort', 'created_at'),
            $request->query('page', [])
        );

        return response()->json([
            'data' => ProjectResource::collection($projects->items()),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        $project = $this->projectService->find($request->user(), $project->id);

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'data' => new ProjectResource($project),
            'message' => 'Project created successfully',
        ], 201);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->projectService->update(
            $request->user(),
            $project->id,
            $request->validated()
        );

        return response()->json([
            'data' => new ProjectResource($project),
            'message' => 'Project updated successfully',
        ]);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $this->projectService->delete($request->user(), $project->id);

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }
}

