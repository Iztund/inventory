<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $primaryKey = 'login_activity_id';
    public $timestamps = false; // We use logged_in_at

    protected $fillable = ['user_id', 'user_agent', 'last_login_ip_address','last_login_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
