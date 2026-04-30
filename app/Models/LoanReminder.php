<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanReminder extends Model
{
    protected $table = 'loan_reminders';

    protected $fillable = [
        'loan_id',
        'type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
