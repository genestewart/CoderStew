<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'type',
        'status',
        'priority',
        'source',
        'ip_address',
        'user_agent',
        'responded_at',
        'responded_by',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'notes' => 'array',
        ];
    }

    /**
     * Scope a query to only include unread contacts.
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope a query to only include read contacts.
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope a query to only include responded contacts.
     */
    public function scopeResponded($query)
    {
        return $query->where('status', 'responded');
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the user who responded to this contact.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Mark the contact as read.
     */
    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Mark the contact as responded.
     */
    public function markAsResponded(User $user = null): void
    {
        $this->update([
            'status' => 'responded',
            'responded_at' => now(),
            'responded_by' => $user?->id,
        ]);
    }

    /**
     * Add a note to the contact.
     */
    public function addNote(string $note, User $user = null): void
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'content' => $note,
            'created_at' => now()->toISOString(),
            'created_by' => $user?->id,
            'created_by_name' => $user?->name,
        ];
        
        $this->update(['notes' => $notes]);
    }

    /**
     * Get the contact's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the contact's display name with company.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        
        if ($this->company) {
            $name .= ' (' . $this->company . ')';
        }
        
        return $name;
    }

    /**
     * Get a short excerpt of the message.
     */
    public function getExcerptAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->message, 100);
    }

    /**
     * Get the priority color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'red',
            'read' => 'yellow',
            'responded' => 'green',
            default => 'gray',
        };
    }
}
