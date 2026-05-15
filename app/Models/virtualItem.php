<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class virtualItem extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'image_url'];
    public function users(){
        return $this->belongsToMany(User::class, 'user_items')->withPivot('acquired_at');
    }
}
