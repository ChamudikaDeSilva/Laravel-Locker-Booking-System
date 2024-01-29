<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table='contact';
    protected $fillable = ['user_id','locker_id','booking_id','name' ,'email', 'message','rating','sentiment','action','final_state'];

}
