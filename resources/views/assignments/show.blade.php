@extends('layouts.classroom')

@section('classroom-content')

@php
    use Carbon\Carbon;
@endphp

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h3 class="fw-bold mb-1">
                {{ $assignment->title }}
            </h3>

            <p class="text-muted mb-2">
                {{ $assignment->description }}
            </p>

            <small class="text-danger">
                Due: {{ $assignment->due_date ?? 'No due date' }}
            </small>

            {{-- 📎 ATTACHMENT --}}
            @if($assignment->file)
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $assignment->file) }}"
                       target="_blank"
                       class="btn btn-outline-primary btn-sm">
                        📎 View Attachment
                    </a>
                </div>
            @endif

            {{-- 🛠 TEACHER ACTIONS --}}
            @if($assignment->classroom->teacher_id === auth()->id())

                <div class="mt-3 d-flex gap-2">

                    <a href="{{ route('assignments.edit', $assignment->id) }}"
                       class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('assignments.destroy', $assignment->id) }}"
                          onsubmit="return confirm('Delete this assignment?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>


    {{-- ================= TEACHER VIEW ================= --}}
    @if($assignment->classroom->teacher_id === auth()->id())

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <h5 class="fw-bold mb-3">Student Submissions</h5>

                @forelse($assignment->submissions as $submission)

                    <div class="border rounded p-3 mb-2 bg-light">

                        <div class="d-flex justify-content-between">

                            <strong>{{ $submission->user->name }}</strong>

                            <small class="text-muted">
                                {{ $submission->created_at->diffForHumans() }}
                            </small>

                        </div>

                        @if($submission->file)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $submission->file) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    View File
                                </a>
                            </div>
                        @endif

                    </div>

                @empty

                    <p class="text-muted">No submissions yet</p>

                @endforelse

            </div>

        </div>

    @endif


    {{-- ================= STUDENT VIEW ================= --}}
    @if($assignment->classroom->teacher_id !== auth()->id())

        @php
            $mySubmission = $assignment->submissions
                ->where('user_id', auth()->id())
                ->first();

            $isExpired = $assignment->due_date && Carbon::now()->gt($assignment->due_date);
        @endphp

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold mb-3">Your Work</h5>

                {{-- ALREADY SUBMITTED --}}
                @if($mySubmission)

                    <div class="alert alert-success">
                        ✅ You already submitted this assignment
                    </div>

                    @if($mySubmission->file)
                        <a href="{{ asset('storage/' . $mySubmission->file) }}"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            View Your Submission
                        </a>
                    @endif

                {{-- DEADLINE EXPIRED --}}
                @elseif($isExpired)

                    <div class="alert alert-danger">
                        ⛔ Deadline expired — you cannot submit this assignment
                    </div>

                {{-- NOT SUBMITTED --}}
                @else

                    <form method="POST"
                          action="{{ route('submissions.store') }}"
                          enctype="multipart/form-data">

                        @csrf

                        <input type="hidden"
                               name="assignment_id"
                               value="{{ $assignment->id }}">

                        <input type="file"
                               name="file"
                               class="form-control mb-3"
                               required>

                        <textarea name="note"
                                  class="form-control mb-3"
                                  placeholder="Add a note (optional)"></textarea>

                        <button class="btn btn-success w-100">
                            Submit Assignment
                        </button>

                    </form>

                @endif

            </div>

        </div>

    @endif

</div>

@endsection