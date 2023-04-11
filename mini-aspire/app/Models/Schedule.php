<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    const STATUS_UNPAID = 0;
    const STATUS_PARTIALLY_PAID = 1;
    const STATUS_PAID = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'amount',
        'paid',
        'date',
        'status',
    ];

    /**
     * Relationship with Loan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Relationship with Repayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}
