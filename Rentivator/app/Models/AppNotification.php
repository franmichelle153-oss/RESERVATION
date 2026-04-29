<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'booking_placed'      => 'fa-calendar-plus',
            'booking_approved'    => 'fa-circle-check',
            'booking_cancelled'   => 'fa-calendar-xmark',
            'booking_completed'   => 'fa-flag-checkered',
            'booking_rescheduled' => 'fa-calendar-clock',
            'audit_added'         => 'fa-clipboard-check',
            'payment_received'    => 'fa-peso-sign',
            'message'             => 'fa-message',
            default               => 'fa-bell',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'booking_placed'      => '#6DBE47',
            'booking_approved'    => '#2d9e5a',
            'booking_cancelled'   => '#e74c3c',
            'booking_completed'   => '#3498db',
            'booking_rescheduled' => '#f39c12',
            'audit_added'         => '#9b59b6',
            'payment_received'    => '#f39c12',
            'message'             => '#1abc9c',
            default               => '#8aaa92',
        };
    }
}