@extends('layouts.classroom')

@section('classroom-content')

@php
    use Carbon\Carbon;
@endphp

<div class="container">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold">📄 Assignments</h2>
        <p class="text-muted">{{ $classroom->name }}</p>
    </div>

    {{-- FILTER --}}
    <div class="mb-3">
        <button class="btn btn-light border w-100 text-start"
                data-bs-toggle="collapse"
                data-bs-target="#filterBox">
            🔽 Filter Assignments
        </button>

        <div class="collapse mt-2" id="filterBox">

            <div class="d-flex gap-2 flex-wrap">

                <button class="btn btn-sm btn-outline-dark" onclick="filterAssignments('all')">
                    All
                </button>

                <button class="btn btn-sm btn-outline-success" onclick="filterAssignments('submitted')">
                    Submitted
                </button>

                <button class="btn btn-sm btn-outline-warning" onclick="filterAssignments('missing')">
                    Missing
                </button>

                <button class="btn btn-sm btn-outline-danger" onclick="filterAssignments('late')">
                    Late
                </button>

            </div>

        </div>
    </div>

    {{-- ASSIGNMENTS --}}
    @forelse($classroom->assignments as $assignment)

        @php
            $due = $assignment->due_date ? Carbon::parse($assignment->due_date) : null;

            $mySubmission = $assignment->submissions
                ->where('user_id', auth()->id())
                ->first();

            $isSubmitted = (bool) $mySubmission;

            $isLate = !$isSubmitted && $due && now()->gt($due);

            $status = $isSubmitted ? 'submitted' : ($isLate ? 'late' : 'missing');
        @endphp

        <div class="assignment-item card border-0 shadow-sm mb-3 hover-card"
             data-status="{{ $status }}">

            {{-- CLICKABLE CARD --}}
            <a href="{{ route('assignments.show', $assignment->id) }}"
               class="text-decoration-none text-dark">

                <div class="card-body d-flex justify-content-between align-items-center">

                    {{-- LEFT --}}
                    <div>

                        <h5 class="fw-bold mb-1">
                            {{ $assignment->title }}
                        </h5>

                        <p class="text-muted mb-2">
                            {{ $assignment->description }}
                        </p>

                        {{-- DUE DATE --}}
                        <small class="text-muted d-block">
                            @if($due)
                                Due: {{ $due->format('M d, Y - h:i A') }}
                            @else
                                No due date
                            @endif
                        </small>

                        {{-- BADGES --}}
                        <div class="mt-2 d-flex gap-2 flex-wrap">

                            @if($isSubmitted)
                                <span class="badge bg-success">Submitted</span>

                            @elseif($isLate)
                                <span class="badge bg-danger">Late</span>

                            @else
                                <span class="badge bg-warning text-dark">Missing</span>
                            @endif

                        </div>

                    </div>

                    {{-- RIGHT ARROW --}}
                    <div class="text-muted fs-4">
                        ›
                    </div>

                </div>

            </a>

        </div>

    @empty

        <div class="text-center text-muted py-5">
            No assignments yet
        </div>

    @endforelse

</div>

{{-- FILTER SCRIPT --}}
<script>
function filterAssignments(type) {

    document.querySelectorAll('.assignment-item').forEach(item => {

        const status = item.getAttribute('data-status');

        item.style.display =
            (type === 'all' || status === type) ? 'block' : 'none';
    });
}
</script>

{{-- STYLES --}}
<style>
.hover-card {
    transition: 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
</style>

@endsection