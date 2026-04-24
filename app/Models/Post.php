<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Comment;

class Post extends Model
{
protected $fillable = [
    'title',
    'description',
    'classroom_id',
    'user_id'
];
public function classroom()
{
    return $this->belongsTo(Classroom::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}
}
