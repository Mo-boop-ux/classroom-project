<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Classroom;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPostMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // ================= STORE =================
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('posts', 'public');
        }

        $post = Post::create([
            'description' => $request->description,
            'classroom_id' => $request->classroom_id,
            'user_id' => auth()->id(),
            'file' => $filePath,
        ]);

        $classroom = Classroom::with('students')
            ->findOrFail($request->classroom_id);

        $recipients = $classroom->students
            ->where('id', '!=', auth()->id());

        foreach ($recipients as $student) {
            Mail::to($student->email)->queue(
                new NewPostMail($post, $classroom)
            );
        }

        return back()->with('success', 'Post created with attachment!');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $post = Post::with('classroom')->findOrFail($id);

        $isOwner = $post->user_id === auth()->id();
        $isTeacher = $post->classroom->teacher_id === auth()->id();

        if (!$isOwner && !$isTeacher) {
            abort(403);
        }

        // delete file
        if ($post->file) {
            Storage::disk('public')->delete($post->file);
        }

        // delete comments
        $post->comments()->delete();

        $post->delete();

        return back()->with('success', 'Post deleted successfully');
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $post = Post::with('classroom')->findOrFail($id);

        $isOwner = $post->user_id === auth()->id();
        $isTeacher = $post->classroom->teacher_id === auth()->id();

        if (!$isOwner && !$isTeacher) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    // ================= UPDATE (🔥 FULL FIX) =================
    public function update(Request $request, $id)
    {
        $post = Post::with('classroom')->findOrFail($id);

        $isOwner = $post->user_id === auth()->id();
        $isTeacher = $post->classroom->teacher_id === auth()->id();

        if (!$isOwner && !$isTeacher) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        // 🔥 REMOVE FILE
        if ($request->has('remove_file') && $post->file) {
            Storage::disk('public')->delete($post->file);
            $post->file = null;
        }

        // 🔥 UPLOAD NEW FILE
        if ($request->hasFile('file')) {

            // delete old file
            if ($post->file) {
                Storage::disk('public')->delete($post->file);
            }

            $post->file = $request->file('file')->store('posts', 'public');
        }

        // update description
        $post->description = $request->description;
        $post->save();

        return redirect()
            ->route('classrooms.show', $post->classroom_id)
            ->with('success', 'Post updated successfully');
    }
}