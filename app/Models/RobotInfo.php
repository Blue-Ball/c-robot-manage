<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RobotInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'robot_serial',
        'robot_name',
        'robot_password',
        'robot_number',
        'user_id',
    ];

    protected $table = 'robots_info_table';
}
