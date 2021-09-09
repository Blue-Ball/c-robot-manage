<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalRooms extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'unit',
        'floor',
        'room_number',
        'room_size',
        'creation_date',
        'last_edit_date',
    ];

    protected $table = 'hospital_rooms_table';
}
