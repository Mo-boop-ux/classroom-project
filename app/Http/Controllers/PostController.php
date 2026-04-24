<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id'
        ]);

        $data['user_id'] = auth()->id();

        Post::create($data);

        return redirect()->route('classrooms.show', $data['classroom_id']);
    }
}
