@extends('layouts.classroom')

@section('classroom-content')

@php
    use Carbon\Carbon;

    $isExpired = $assignment->due_date && Carbon::now()->gt($assignment->due_date);
@endphp

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start">

                {{-- LEFT SIDE --}}
                <div>

                    <h3 class="fw-bold mb-1">
                        {{ $assignment->title }}
                    </h3>

                    <p class="text-muted mb-2">
                        {{ $assignment->description }}
                    </p>

                    {{-- DUE DATE --}}
                    <div>
                        @if($assignment->due_date)

                            @if($isExpired)
                                <span class="badge bg-danger">
                                    ⛔ Expired ({{ $assignment->due_date }})
                                </span>
                            @else
                                <span class="badge bg-success">
                                    ⏳ Due: {{ $assignment->due_date }}
                                </span>
                            @endif

                        @else
                            <span class="badge bg-secondary">
                                No due date
                            </span>
                        @endif
                    </div>

                </div>

                {{-- ================= 3 DOT MENU (TEACHER) ================= --}}
                @if($assignment->classroom->teacher_id === auth()->id())

                    <div class="dropdown">

                        <button class="btn btn-light btn-sm dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            ⋮
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('assignments.edit', $assignment->id) }}">
                                    Edit
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST"
                                      action="{{ route('assignments.destroy', $assignment->id) }}"
                                      onsubmit="return confirm('Delete this assignment?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="dropdown-item text-danger">
                                        Delete
                                    </button>

                                </form>
                            </li>

                        </ul>

                    </div>

                @endif

            </div>

            {{-- 📎 ATTACHMENT --}}
            @if($assignment->file)
                <div class="mt-3 p-2 border rounded bg-light">

                    <div class="small text-muted mb-1">
                        {{ basename($assignment->file) }}
                    </div>

                    <a href="{{ asset('storage/' . $assignment->file) }}"
                       target="_blank"
                       class="btn btn-outline-primary btn-sm">
                        📎 Open Attachment
                    </a>

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

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                {{ $submission->user->name }}
                            </strong>

                            <div class="d-flex gap-2 align-items-center">

                                @if($assignment->due_date && $submission->created_at > $assignment->due_date)
                                    <span class="badge bg-warning text-dark">
                                        Late
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        Submitted
                                    </span>
                                @endif

                                <small class="text-muted">
                                    {{ $submission->created_at->diffForHumans() }}
                                </small>

                            </div>

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
        @endphp

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h5 class="fw-bold mb-3">Your Work</h5>

                {{-- SUBMITTED --}}
                @if($mySubmission)

                    <div class="alert alert-success d-flex justify-content-between align-items-center">

                        <div>
                            ✅ Submitted
                        </div>

                        @if($assignment->due_date && $mySubmission->created_at > $assignment->due_date)
                            <span class="badge bg-warning text-dark">
                                Late
                            </span>
                        @endif

                    </div>

                    @if($mySubmission->file)
                        <a href="{{ asset('storage/' . $mySubmission->file) }}"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            View Your Submission
                        </a>
                    @endif

                {{-- EXPIRED --}}
                @elseif($isExpired)

                    <div class="alert alert-danger">
                        ⛔ Missing (Deadline passed)
                    </div>

                {{-- NOT SUBMITTED --}}
                @else

                    <div class="alert alert-info">
                        📌 Not submitted yet
                    </div>

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