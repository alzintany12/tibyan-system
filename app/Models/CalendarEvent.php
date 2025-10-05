<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_all_day',
        'location',
        'color',
        'reminders',
        'user_id',
        'legal_case_id',
        'is_recurring',
        'recurrence_type',
        'recurrence_end',
        'parent_event_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurrence_end' => 'datetime',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'reminders' => 'array'
    ];

    /**
     * Get the user that owns the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the legal case associated with the event.
     */
    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    /**
     * Get the parent event for recurring events.
     */
    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class, 'parent_event_id');
    }

    /**
     * Get the child events for recurring events.
     */
    public function childEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'parent_event_id');
    }

    /**
     * Get the attendees for the event.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attendees')
                    ->withPivot(['response', 'reminded_at'])
                    ->withTimestamps();
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope for events in date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted start time.
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_date ? $this->start_date->format('H:i') : null;
    }

    /**
     * Get formatted end time.
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_date ? $this->end_date->format('H:i') : null;
    }

    /**
     * Get event duration in minutes.
     */
    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInMinutes($this->end_date);
        }
        
        return null;
    }

    /**
     * Check if event is today.
     */
    public function getIsTodayAttribute()
    {
        return $this->start_date ? $this->start_date->isToday() : false;
    }

    /**
     * Check if event is in the past.
     */
    public function getIsPastAttribute()
    {
        return $this->start_date ? $this->start_date->isPast() : false;
    }
}