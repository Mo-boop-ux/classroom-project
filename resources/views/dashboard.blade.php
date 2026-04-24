@extends('layouts.app')

@section('content')

<div class="container py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold">📊 Teacher Dashboard</h2>
        <p class="text-muted">Overview of your created classes</p>
    </div>

    {{-- CLASS SELECT DROPDOWN --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <label class="form-label fw-bold">Select Class</label>

            <select id="classSelect" class="form-select">

                <option value="">-- Choose a class --</option>

                @foreach($classes as $class)
                    <option value="class-{{ $class->id }}">
                        {{ $class->name }}
                    </option>
                @endforeach

            </select>

        </div>

    </div>

    {{-- CLASS CARDS --}}
    @forelse($classes as $class)

        <div id="class-{{ $class->id }}" class="class-box d-none">

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-body">

                    {{-- CLASS INFO --}}
                    <h4 class="fw-bold">{{ $class->name }}</h4>

                    <p class="text-muted mb-2">
                        {{ $class->section }} • {{ $class->subject }}
                    </p>

                    {{-- STATS --}}
                    <div class="row text-center mb-3">

                        <div class="col">
                            <div class="border rounded p-2">
                                <h5 class="mb-0">{{ $class->assignments->count() }}</h5>
                                <small class="text-muted">Assignments</small>
                            </div>
                        </div>

                        <div class="col">
                            <div class="border rounded p-2">
                                <h5 class="mb-0">
                                    {{ $class->assignments->sum(fn($a) => $a->submissions->count()) }}
                                </h5>
                                <small class="text-muted">Submissions</small>
                            </div>
                        </div>

                    </div>

                    {{-- ACTIONS --}}
                    <div class="d-flex gap-2">

                        <a href="{{ route('classrooms.show', $class->id) }}"
                           class="btn btn-primary btn-sm">
                            Open Class
                        </a>

                        <a href="{{ route('classrooms.classwork', $class->id) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Classwork
                        </a>

                    </div>

                </div>

            </div>

        </div>

    @empty

        <div class="text-center text-muted py-5">
            <h5>No classes yet</h5>
            <p>Create your first class to start using the dashboard</p>
        </div>

    @endforelse

</div>

{{-- SIMPLE JS --}}
<script>
document.getElementById('classSelect').addEventListener('change', function () {

    let boxes = document.querySelectorAll('.class-box');

    boxes.forEach(box => box.classList.add('d-none'));

    if (this.value) {
        document.getElementById(this.value).classList.remove('d-none');
    }

});
</script>

@endsection