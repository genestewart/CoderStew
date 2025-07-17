<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimonial extends Model
{
    use HasFactory;

    protected $table = 'testimonials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_name',
        'client_title',
        'client_company',
        'client_avatar',
        'content',
        'rating',
        'project_id',
        'featured',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include published testimonials.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Get the project this testimonial belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created this testimonial.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this testimonial.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the client's full name with title.
     */
    public function getClientFullNameAttribute(): string
    {
        $name = $this->client_name;
        
        if ($this->client_title) {
            $name .= ', ' . $this->client_title;
        }
        
        if ($this->client_company) {
            $name .= ' at ' . $this->client_company;
        }
        
        return $name;
    }

    /**
     * Get the star rating as HTML.
     */
    public function getStarRatingAttribute(): string
    {
        $stars = '';
        $rating = $this->rating ?? 5;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        
        return $stars;
    }

    /**
     * Get a short excerpt of the content.
     */
    public function getExcerptAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->content, 150);
    }
}
