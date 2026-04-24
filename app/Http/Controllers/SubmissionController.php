<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;


class SubmissionController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'assignment_id' => 'required',
        'file' => 'required|file',
    ]);

    $path = $request->file('file')->store('submissions', 'public');

    Submission::create([
        'assignment_id' => $request->assignment_id,
        'user_id' => auth()->id(),
        'file' => $path,
    ]);

    return back();
}

}
