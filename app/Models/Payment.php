<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
    protected $table='payment';
    protected $fillable = ['user_id', 'booking_id', 'date', 'payment_amount','payment_type'];


}
