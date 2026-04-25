@extends('layouts.classroom')

@section('classroom-content')

@php
    use Carbon\Carbon;
@endphp

<div class="container">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold">📚 Classwork</h2>
        <p class="text-muted">{{ $classroom->name }}</p>
    </div>

    {{-- TEACHER ACTION --}}
    @if($classroom->teacher_id === auth()->id())

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">Create Assignment</h5>
                    <small class="text-muted">
                        Add homework, tasks, and materials for students
                    </small>
                </div>

                <a href="{{ route('assignments.create', $classroom->id) }}"
                   class="btn btn-primary">
                    + Create
                </a>

            </div>

        </div>

    @endif


    {{-- ASSIGNMENTS LIST --}}
    @forelse($classroom->assignments as $assignment)

        @php
            $due = $assignment->due_date ? Carbon::parse($assignment->due_date) : null;
            $isLate = $due && now()->greaterThan($due);
        @endphp

        {{-- CLICKABLE CARD --}}
        <a href="{{ route('assignments.show', $assignment->id) }}"
           class="text-decoration-none text-dark">

            <div class="card border-0 shadow-sm mb-3 hover-card">

                <div class="card-body d-flex justify-content-between align-items-center">

                    {{-- LEFT --}}
                    <div class="flex-grow-1">

                        <h5 class="fw-bold mb-1">
                            {{ $assignment->title }}
                        </h5>

                        <p class="text-muted mb-2">
                            {{ $assignment->description }}
                        </p>

                        {{-- STATUS --}}
                        <div class="d-flex gap-2 flex-wrap">

                            @if($due)
                                <span class="badge {{ $isLate ? 'bg-danger' : 'bg-success' }}">
                                    {{ $isLate ? 'Late' : 'Due' }}: {{ $due->format('M d, Y') }}
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    No due date
                                </span>
                            @endif

                        </div>

                    </div>

                    {{-- RIGHT ARROW --}}
                    <div class="ms-3 text-muted fs-4">
                        ›
                    </div>

                </div>

            </div>

        </a>

    @empty

        <div class="text-center text-muted py-5">
            No assignments yet
        </div>

    @endforelse

</div>

{{-- STYLE --}}
<style>
.hover-card {
    transition: 0.2s ease;
    cursor: pointer;
}

.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}
</style>

@endsection