<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    // Disable default timestamps since we use attempted_at
    public $timestamps = false;
    protected $primaryKey = 'failed_login_id';
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent', 'location_hint', 'attempted_at'
    ];

    // Link back to the user if the attempt matched a real account
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
