<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Testimonial::published()->orderBy('sort_order')->orderBy('created_at', 'desc');

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by minimum rating if provided
        if ($request->has('min_rating') && $request->min_rating) {
            $query->byRating($request->min_rating);
        }

        // Filter featured testimonials if requested
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Load related project data
        $query->with(['project:_id,title,slug']);

        // Pagination
        $perPage = min($request->get('per_page', 10), 50);
        $testimonials = $query->paginate($perPage);

        return response()->json([
            'data' => $testimonials->items(),
            'meta' => [
                'current_page' => $testimonials->currentPage(),
                'last_page' => $testimonials->lastPage(),
                'per_page' => $testimonials->perPage(),
                'total' => $testimonials->total(),
            ],
            'links' => [
                'first' => $testimonials->url(1),
                'last' => $testimonials->url($testimonials->lastPage()),
                'prev' => $testimonials->previousPageUrl(),
                'next' => $testimonials->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display featured testimonials.
     */
    public function featured(Request $request): JsonResponse
    {
        $query = Testimonial::published()->featured()->orderBy('sort_order')->orderBy('created_at', 'desc');

        // Filter by minimum rating if provided
        if ($request->has('min_rating') && $request->min_rating) {
            $query->byRating($request->min_rating);
        }

        // Load related project data
        $query->with(['project:_id,title,slug']);

        // Limit results for featured testimonials
        $limit = min($request->get('limit', 6), 20);
        $testimonials = $query->limit($limit)->get();

        return response()->json([
            'data' => $testimonials,
            'meta' => [
                'total' => $testimonials->count(),
                'limit' => $limit,
            ],
        ]);
    }
}
