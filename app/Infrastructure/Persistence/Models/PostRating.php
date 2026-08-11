<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRating extends Model
{
    use HasFactory;

    protected $table = 'post_ratings';

    protected $fillable = [
        'post_id',
        'user_id',
        'rating',
        'review',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
