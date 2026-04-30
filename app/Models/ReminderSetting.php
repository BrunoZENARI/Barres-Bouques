<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderSetting extends Model
{
    protected $table = 'reminder_settings';

    protected $fillable = [
        'days_before_due',
        'reminder_due_soon_enabled',
        'reminder_overdue_enabled',
        'librarian_email',
    ];

    protected $casts = [
        'reminder_due_soon_enabled' => 'boolean',
        'reminder_overdue_enabled'  => 'boolean',
    ];

    public static function getInstance(): self
    {
        return static::firstOrCreate([], [
            'days_before_due'           => 3,
            'reminder_due_soon_enabled' => true,
            'reminder_overdue_enabled'  => true,
            'librarian_email'           => null,
        ]);
    }
}
