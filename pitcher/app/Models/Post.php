<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'categories',
    ];

    protected $casts = [
        'title' => 'string',
        'content' => 'string'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function setCategoriesAttribute($value) {
        if (is_array($value)) {
            $values = array_filter(array_map('trim', $value), function ($item) {
                return $item !== '';
            });

            $this->attributes['categories'] = empty($values) ? null : implode(', ', $values);
            return;
        }

        $this->attributes['categories'] = $value;
    }

    public function getCategoryListAttribute() {
        if (empty($this->attributes['categories'])) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->attributes['categories'])), function ($item) {
            return $item !== '';
        }));
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }
}
