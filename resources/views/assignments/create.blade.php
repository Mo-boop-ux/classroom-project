@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

    <h3 class="fw-bold mb-3">Create Assignment</h3>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('assignments.store') }}"
                  enctype="multipart/form-data">

                @csrf

                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                <input type="text"
                       name="title"
                       class="form-control mb-2"
                       placeholder="Assignment title">

                <textarea name="description"
                          class="form-control mb-2"
                          placeholder="Instructions"></textarea>

                <input type="date"
                       name="due_date"
                       class="form-control mb-2">

                <input type="file"
                       name="file"
                       class="form-control mb-3">

                <button class="btn btn-primary w-100">
                    Create Assignment
                </button>

            </form>

        </div>

    </div>

</div>

@endsection