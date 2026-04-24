<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Post;
use App\Models\User;
class ClassroomController extends Controller
{

    public function index()
    {
    $user = auth()->user();

    $created = Classroom::where('teacher_id', $user->id)->get();
    $joined = $user->classrooms;

    return view('classrooms.index', compact('created', 'joined'));
    }

    public function create()
    {
        return view('classrooms.create');
    }


    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'section' => 'nullable|string',
            'subject' => 'nullable|string',
        ]);

        $data['teacher_id'] = auth()->id();
        $data['code'] = Str::upper(Str::random(6));
        Classroom::create($data);

        return redirect()->route('classrooms.index');
    }


    public function show($id)
    {
    $classroom = Classroom::findOrFail($id);

    $posts = Post::where('classroom_id', $id)->latest()->get();

    return view('classrooms.show', compact('classroom', 'posts'));
    }

  
    public function join($id)
    {
        $classroom = Classroom::findOrFail($id);

        if (!$classroom->students->contains(auth()->id())) {
            $classroom->students()->attach(auth()->id());
        }

        return back();
    }

    public function joinPage()
    {
    return view('classrooms.join');
    }

    public function joinByCode(Request $request)
{
    $request->validate([
        'code' => 'required|exists:classrooms,code',
    ]);

    $classroom = Classroom::where('code', $request->code)->first();
    $user = auth()->user();
 
    if ($classroom->teacher_id === $user->id) {
        return back()->with('error', 'You are the teacher of this classroom.');
    }

    if ($classroom->students()->where('user_id', $user->id)->exists()) {
        return back()->with('error', 'You already joined this classroom.');
    }

    $classroom->students()->attach($user->id);

    return redirect()->route('classrooms.show', $classroom->id)
                     ->with('success', 'Joined successfully!');
}


public function people($id)
{
    $classroom = Classroom::with('students')->findOrFail($id);

    $teacher = User::find($classroom->teacher_id);

    return view('classrooms.people', compact('classroom', 'teacher'));
}


public function classwork($id)
{
    $classroom = Classroom::findOrFail($id);

    if ($classroom->teacher_id !== auth()->id()) {
        abort(403);
    }

    return view('classrooms.classwork', compact('classroom'));
}

public function assignments($id)
{
    $classroom = Classroom::with('assignments')->findOrFail($id);

    return view('classrooms.assignments', compact('classroom'));
}

public function joinByLink($code)
{
    $classroom = Classroom::where('code', $code)->firstOrFail();

    $user = auth()->user();

    // prevent duplicate join
    if (!$classroom->students->contains($user->id)) {
        $classroom->students()->attach($user->id);
    }

    return redirect()->route('classrooms.show', $classroom->id);
}

public function dashboard()
{
    $classes = Classroom::where('teacher_id', auth()->id())->with('assignments.submissions')->get();
    return view('dashboard', compact('classes'));
}

}