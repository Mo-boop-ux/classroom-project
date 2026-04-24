@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold">📚 Classwork</h2>
        <p class="text-muted">{{ $classroom->name }}</p>
    </div>

    {{-- TEACHER ACTION --}}
    @if($classroom->teacher_id === auth()->id())

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body text-center">

                <h5 class="fw-bold">Create Assignment</h5>

                <p class="text-muted">
                    Add tasks, homework, and materials for students
                </p>

                <a href="{{ route('assignments.create', $classroom->id) }}"
                   class="btn btn-primary">
                    + Create Assignment
                </a>

            </div>

        </div>

    @endif

    {{-- ASSIGNMENTS LIST --}}
    @forelse($classroom->assignments as $assignment)

        <div class="card shadow-sm mb-3 border-0">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="fw-bold mb-1">
                        {{ $assignment->title }}
                    </h5>

                    <p class="text-muted mb-1">
                        {{ $assignment->description }}
                    </p>

                    {{-- 📎 ATTACHMENT INDICATOR --}}
                    @if(!empty($assignment->file))
                        <small class="text-primary">
                            📎 Attachment available
                        </small>
                    @endif

                </div>

                <div class="text-end">

                    {{-- OPEN ASSIGNMENT --}}
                    <a href="{{ route('assignments.show', $assignment->id) }}"
                       class="btn btn-outline-primary btn-sm mb-1">
                        Open
                    </a>

                    {{-- QUICK VIEW FILE --}}
                    @if(!empty($assignment->file))
                        <br>
                        <a href="{{ Storage::url($assignment->file) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm mt-1">
                            View File
                        </a>
                    @endif

                </div>

            </div>

        </div>

    @empty

        <div class="text-center text-muted py-5">
            No assignments yet
        </div>

    @endforelse

</div>

@endsection