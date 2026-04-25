<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Assignment;
use App\Models\Post;
use App\Models\AssignmentAttachment;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create($id)
    {
        $classroom = Classroom::findOrFail($id);

        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        return view('assignments.create', compact('classroom'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'classroom_id' => 'required|exists:classrooms,id',
            'files.*' => 'nullable|file|max:10240',
        ]);

        $classroom = Classroom::findOrFail($request->classroom_id);

        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        // CREATE ASSIGNMENT
        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'classroom_id' => $classroom->id,
            'user_id' => auth()->id(),
        ]);

        // ================= MULTIPLE FILES =================
        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $file) {

                $path = $file->store('assignments', 'public');

                AssignmentAttachment::create([
                    'assignment_id' => $assignment->id,
                    'file_path' => $path
                ]);
            }
        }

        // ================= STREAM POST =================
        Post::create([
            'description' => "📚 New Assignment: {$assignment->title}",
            'classroom_id' => $classroom->id,
            'user_id' => auth()->id(),
            'type' => 'assignment',
            'assignment_id' => $assignment->id
        ]);

        return redirect()
            ->route('classrooms.classwork', $classroom->id)
            ->with('success', 'Assignment created successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $assignment = Assignment::with([
            'submissions.user',
            'attachments',
            'classroom'
        ])->findOrFail($id);

        return view('assignments.show', [
            'assignment' => $assignment,
            'classroom' => $assignment->classroom
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $assignment = Assignment::with('attachments', 'submissions')->findOrFail($id);

        if ($assignment->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        // delete attachments files
        foreach ($assignment->attachments as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $assignment->attachments()->delete();

        // delete submissions files
        foreach ($assignment->submissions as $submission) {
            if ($submission->file) {
                Storage::disk('public')->delete($submission->file);
            }
        }

        $assignment->submissions()->delete();

        // delete related posts
        Post::where('assignment_id', $assignment->id)->delete();

        $assignment->delete();

        return redirect()
            ->route('classrooms.classwork', $assignment->classroom_id)
            ->with('success', 'Assignment deleted successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $assignment = Assignment::with(['classroom', 'attachments'])->findOrFail($id);

        if ($assignment->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        return view('assignments.edit', compact('assignment'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $assignment = Assignment::with('attachments')->findOrFail($id);

        if ($assignment->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'files.*' => 'nullable|file|max:10240',
        ]);

        // UPDATE BASIC INFO
        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        // ================= ADD NEW FILES =================
        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $file) {

                $path = $file->store('assignments', 'public');

                AssignmentAttachment::create([
                    'assignment_id' => $assignment->id,
                    'file_path' => $path
                ]);
            }
        }

        return redirect()
            ->route('classrooms.classwork', $assignment->classroom_id)
            ->with('success', 'Assignment updated successfully!');
    }
}