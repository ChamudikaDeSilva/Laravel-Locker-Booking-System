<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_email',
        'receipt_number',
        'amount',
        'created_date',
        'created_time',
    ];

    protected $dates = ['created_date', 'created_time', 'created_at', 'updated_at','topUp_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
