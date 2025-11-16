<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $connection = 'mysql'; // Always use central database

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
