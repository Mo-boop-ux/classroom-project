@extends('layouts.app')

@section('content')

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">Classrooms</h2>

        <div class="d-flex gap-2">

            <a href="{{ route('classrooms.create') }}"
               class="btn btn-primary px-4">
                Create
            </a>

            <a href="{{ route('classrooms.joinPage') }}"
               class="btn btn-outline-success px-4">
                Join
            </a>

        </div>

    </div>

    {{-- GRID --}}
    <div class="row g-4">

        @foreach(($created ?? collect())->merge($joined ?? collect()) as $classroom)

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100 hover-card">

                    <div class="card-body d-flex flex-column justify-content-between">

                        {{-- CLASS INFO --}}
                        <div>

                            <h5 class="fw-bold mb-1">
                                {{ $classroom->name }}
                            </h5>

                            <p class="text-muted small mb-3">
                                {{ $classroom->section }}
                            </p>

                            @if($classroom->teacher_id === auth()->id())
                                <span class="badge bg-primary mb-2">Teacher</span>
                            @else
                                <span class="badge bg-secondary mb-2">Student</span>
                            @endif

                        </div>

                        {{-- ACTIONS --}}
                        <div class="d-flex flex-column gap-2">

                            {{-- OPEN --}}
                            <a href="{{ route('classrooms.show', $classroom->id) }}"
                               class="btn btn-dark btn-sm w-100">
                                Open
                            </a>

                            {{-- EDIT (ONLY TEACHER) --}}
                            @if($classroom->teacher_id === auth()->id())

                                <a href="{{ route('classrooms.edit', $classroom->id) }}"
                                   class="btn btn-warning btn-sm w-100">
                                    Edit
                                </a>

                                {{-- DELETE CLASS (OPTIONAL BUT POWERFUL) --}}
                                <form method="POST"
                                      action="{{ route('classrooms.destroy', $classroom->id) }}"
                                      onsubmit="return confirm('Delete this class permanently?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm w-100">
                                        Delete
                                    </button>

                                </form>

                            @endif

                            {{-- UNENROLL (ONLY STUDENT) --}}
                            @if($classroom->teacher_id !== auth()->id())

                                <form method="POST"
                                      action="{{ route('classrooms.leave', $classroom->id) }}"
                                      onsubmit="return confirm('Leave this class?')">

                                    @csrf

                                    <button class="btn btn-outline-danger btn-sm w-100">
                                        Leave
                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

{{-- HOVER EFFECT --}}
<style>
.hover-card {
    transition: 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
</style>

@endsection