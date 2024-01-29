<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table='booking';
    protected $fillable = ['start_time', 'end_time', 'date', 'locker_id','unit_amount','user_id','usage','reviewed'];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
