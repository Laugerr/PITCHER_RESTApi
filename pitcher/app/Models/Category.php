<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
    ];

    protected $casts = [
        "title" => "string",
        "description" => "string",
    ];

    public function posts() {
        return $this->belongsToMany(Post::class);
    }
}
