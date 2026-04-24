<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW CREATE FORM (TEACHER ONLY UI)
    |--------------------------------------------------------------------------
    */
    public function create($id)
    {
        $classroom = Classroom::findOrFail($id);

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

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'file' => $filePath,
            'classroom_id' => $request->classroom_id,
            'user_id' => auth()->id(), // teacher
        ]);

        return redirect()
            ->route('classrooms.classwork', $request->classroom_id)
            ->with('success', 'Assignment created successfully');
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

        return view('assignments.show', compact('assignment'));
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL: DELETE ASSIGNMENT (TEACHER)
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);

        // Only teacher of classroom can delete
        if ($assignment->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Assignment deleted');
    }
}