<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::published()->orderBy('sort_order')->orderBy('created_at', 'desc');

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        // Filter by technology if provided
        if ($request->has('technology') && $request->technology) {
            $query->byTechnology($request->technology);
        }

        // Filter featured projects if requested
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $projects = $query->paginate($perPage);

        return response()->json([
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
            'links' => [
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        // Only show published projects
        if ($project->status !== 'published') {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Load related testimonials
        $project->load(['testimonials' => function ($query) {
            $query->published()->orderBy('sort_order')->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'data' => $project,
        ]);
    }

    /**
     * Get projects by category.
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        $query = Project::published()->byCategory($category)->orderBy('sort_order')->orderBy('created_at', 'desc');

        // Filter featured projects if requested
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $perPage = min($request->get('per_page', 12), 50);
        $projects = $query->paginate($perPage);

        return response()->json([
            'data' => $projects->items(),
            'category' => $category,
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
            'links' => [
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Get projects by technology.
     */
    public function byTechnology(Request $request, string $technology): JsonResponse
    {
        $query = Project::published()->byTechnology($technology)->orderBy('sort_order')->orderBy('created_at', 'desc');

        // Filter featured projects if requested
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $perPage = min($request->get('per_page', 12), 50);
        $projects = $query->paginate($perPage);

        return response()->json([
            'data' => $projects->items(),
            'technology' => $technology,
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
            'links' => [
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ],
        ]);
    }
}
