@extends('layouts.app')

@section('content')

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">Classrooms</h2>

        <div class="d-flex gap-2">

            <a href="{{ route('classrooms.create') }}"
               class="btn btn-primary shadow-sm px-4">
                + Create
            </a>

            <a href="{{ route('classrooms.joinPage') }}"
               class="btn btn-outline-success shadow-sm px-4">
                Join Class
            </a>

        </div>

    </div>

    {{-- GRID --}}
    <div class="row g-4">

        @foreach(($created ?? collect())->merge($joined ?? collect()) as $classroom)

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100 hover-card">

                    <div class="card-body">

                        <h5 class="fw-bold mb-2">
                            {{ $classroom->name }}
                        </h5>

                        <p class="text-muted small mb-3">
                            {{ $classroom->section }}
                        </p>

                        <a href="{{ route('classrooms.show', $classroom->id) }}"
                           class="btn btn-sm btn-dark w-100">
                            Open Class
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

{{-- SIMPLE HOVER EFFECT --}}
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