<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Assignment;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW CREATE FORM (TEACHER ONLY)
    |--------------------------------------------------------------------------
    */
    public function create($id)
    {
        $classroom = Classroom::findOrFail($id);

        // Only teacher can create
        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        return view('assignments.create', compact('classroom'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE ASSIGNMENT (TEACHER)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $classroom = Classroom::findOrFail($request->classroom_id);

        // Security: only teacher can add assignment
        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Upload file
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        // Create assignment
        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'file' => $filePath,
            'classroom_id' => $classroom->id,
            'user_id' => auth()->id(),
        ]);

        // 🚀 AUTO CREATE STREAM POST
        Post::create([
            'description' => "📚 New Assignment: {$assignment->title}",
            'classroom_id' => $classroom->id,
            'user_id' => auth()->id(),
            // optional if you added column:
            // 'assignment_id' => $assignment->id,
        ]);

        return redirect()
            ->route('classrooms.classwork', $classroom->id)
            ->with('success', 'Assignment created & posted to stream!');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SINGLE ASSIGNMENT (TEACHER + STUDENT)
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $assignment = Assignment::with([
            'submissions.user',
            'classroom'
        ])->findOrFail($id);

        $classroom = $assignment->classroom;

        // Optional security: only members can access
        // (you can enhance later with pivot table check)

        return view('assignments.show', compact('assignment', 'classroom'));
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ASSIGNMENT (TEACHER ONLY)
    |--------------------------------------------------------------------------
    */
   public function destroy($id)
{
    $assignment = Assignment::findOrFail($id);

    // Only teacher can delete
    if ($assignment->classroom->teacher_id !== auth()->id()) {
        abort(403);
    }

    // 🗑 Delete assignment file
    if ($assignment->file) {
        \Storage::disk('public')->delete($assignment->file);
    }

    // 🗑 Delete submissions files
    foreach ($assignment->submissions as $submission) {
        if ($submission->file) {
            \Storage::disk('public')->delete($submission->file);
        }
    }

    $assignment->submissions()->delete();

    // 🗑 DELETE RELATED POSTS (STREAM ANNOUNCEMENTS)
    Post::where('classroom_id', $assignment->classroom_id)
        ->where('description', 'like', "%{$assignment->title}%")
        ->delete();

    // 🗑 Delete assignment itself
    $assignment->delete();

    return redirect()
        ->route('classrooms.classwork', $assignment->classroom_id)
        ->with('success', 'Assignment deleted successfully');
}

public function edit($id)
{
    $assignment = Assignment::with('classroom')->findOrFail($id);

    // only teacher can edit
    if ($assignment->classroom->teacher_id !== auth()->id()) {
        abort(403);
    }

    return view('assignments.edit', compact('assignment'));
}


public function update(Request $request, $id)
{
    $assignment = Assignment::with('classroom')->findOrFail($id);

    if ($assignment->classroom->teacher_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'nullable|date',
        'file' => 'nullable|file|max:10240',
    ]);

    // HANDLE FILE UPDATE
    if ($request->hasFile('file')) {

        // delete old file if exists
        if ($assignment->file) {
            Storage::disk('public')->delete($assignment->file);
        }

        $filePath = $request->file('file')->store('assignments', 'public');

        $assignment->file = $filePath;
    }

    // UPDATE OTHER FIELDS
    $assignment->title = $request->title;
    $assignment->description = $request->description;
    $assignment->due_date = $request->due_date;

    $assignment->save();

    return redirect()
        ->route('classrooms.classwork', $assignment->classroom_id)
        ->with('success', 'Assignment updated successfully');
}



}