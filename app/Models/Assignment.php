<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Classroom;

class Assignment extends Model
{
    protected $fillable = [
    'title',
    'description',
    'due_date',
    'file',
    'classroom_id',
    'user_id',
];

 public function classroom()
{
    return $this->belongsTo(Classroom::class);
}

public function submissions()
{
    return $this->hasMany(Submission::class);
}

public function teacher()
{
    return $this->belongsTo(User::class, 'user_id');
}


}
