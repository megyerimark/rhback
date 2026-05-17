<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoadout extends Model
{
    protected $fillable = ['user_id', 'headgear_id', 'top_id', 'bottom_id', 'accessory_id', 'background_id'];

    // Összekötjük a Laravel-lel, hogy tudja, kinek a felszerelése ez
    public function user() {
        return $this->belongsTo(User::class);
    }
}
