<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Newsletter extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'newsletter_subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'status',
        'source',
        'preferences',
        'listmonk_subscriber_id',
        'verification_token',
        'verified_at',
        'unsubscribed_at',
        'ip_address',
        'user_agent',
        'tags',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'preferences' => 'array',
            'tags' => 'array',
        ];
    }

    /**
     * Scope a query to only include active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending subscribers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include unsubscribed subscribers.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    /**
     * Scope a query to only include verified subscribers.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope a query to filter by source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeByTag($query, $tag)
    {
        return $query->where('tags', 'like', "%{$tag}%");
    }

    /**
     * Check if the subscriber is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the subscriber is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if the subscriber is unsubscribed.
     */
    public function isUnsubscribed(): bool
    {
        return $this->status === 'unsubscribed';
    }

    /**
     * Verify the subscriber.
     */
    public function verify(): void
    {
        $this->update([
            'status' => 'active',
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    /**
     * Unsubscribe the subscriber.
     */
    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Resubscribe the subscriber.
     */
    public function resubscribe(): void
    {
        $this->update([
            'status' => 'active',
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * Add a tag to the subscriber.
     */
    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    /**
     * Remove a tag from the subscriber.
     */
    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        
        $this->update(['tags' => array_values($tags)]);
    }

    /**
     * Update preferences.
     */
    public function updatePreferences(array $preferences): void
    {
        $this->update(['preferences' => array_merge($this->preferences ?? [], $preferences)]);
    }

    /**
     * Generate a verification token.
     */
    public function generateVerificationToken(): string
    {
        $token = \Illuminate\Support\Str::random(64);
        $this->update(['verification_token' => $token]);
        
        return $token;
    }

    /**
     * Get the subscriber's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'pending' => 'yellow',
            'unsubscribed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->verification_token)) {
                $subscriber->verification_token = \Illuminate\Support\Str::random(64);
            }
            
            if (empty($subscriber->status)) {
                $subscriber->status = 'pending';
            }
        });
    }
}
