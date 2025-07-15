<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow authenticated users with admin or editor role
        return auth()->check() && auth()->user()->isEditor();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'category' => 'required|string|in:web-development,mobile-app,e-commerce,api-development,ui-ux-design,consulting',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png,gif,webp|max:5120', // 5MB max
            'featured_image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'demo_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'client_name' => 'nullable|string|max:255',
            'project_date' => 'nullable|date',
            'status' => 'required|string|in:draft,published,archived',
            'featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ];

        // If updating, exclude current record from unique validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $projectId = $this->route('project');
            $rules['slug'] = 'nullable|string|max:255|unique:projects,slug,' . $projectId . ',_id';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'project title',
            'slug' => 'URL slug',
            'description' => 'project description',
            'short_description' => 'short description',
            'category' => 'project category',
            'technologies' => 'technologies',
            'featured_image' => 'featured image',
            'demo_url' => 'demo URL',
            'github_url' => 'GitHub URL',
            'client_name' => 'client name',
            'project_date' => 'project date',
            'status' => 'project status',
            'featured' => 'featured status',
            'sort_order' => 'sort order',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The project title is required.',
            'title.max' => 'The project title cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken. Please choose a different one.',
            'description.required' => 'The project description is required.',
            'category.required' => 'Please select a project category.',
            'category.in' => 'Please select a valid project category.',
            'technologies.array' => 'Technologies must be provided as a list.',
            'images.*.mimes' => 'Images must be in JPG, JPEG, PNG, GIF, or WebP format.',
            'images.*.max' => 'Each image cannot exceed 5MB in size.',
            'featured_image.mimes' => 'Featured image must be in JPG, JPEG, PNG, GIF, or WebP format.',
            'featured_image.max' => 'Featured image cannot exceed 5MB in size.',
            'demo_url.url' => 'Please provide a valid demo URL.',
            'github_url.url' => 'Please provide a valid GitHub URL.',
            'project_date.date' => 'Please provide a valid project date.',
            'status.required' => 'Please select a project status.',
            'status.in' => 'Please select a valid project status.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'meta_title.max' => 'Meta title cannot exceed 60 characters.',
            'meta_description.max' => 'Meta description cannot exceed 160 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (empty($this->slug) && !empty($this->title)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title)
            ]);
        }

        // Convert featured checkbox to boolean
        if ($this->has('featured')) {
            $this->merge([
                'featured' => $this->boolean('featured')
            ]);
        }

        // Ensure sort_order is an integer
        if ($this->has('sort_order') && $this->sort_order !== null) {
            $this->merge([
                'sort_order' => (int) $this->sort_order
            ]);
        }
    }
}
