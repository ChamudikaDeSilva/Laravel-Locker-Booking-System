<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    use HasFactory;
    protected $table='locker';
    protected $fillable = [
        'locker_id',
        'position_x',
        'position_y',
        'locker_type',
        '_token',
    ];
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
