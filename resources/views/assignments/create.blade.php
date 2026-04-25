@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

    {{-- HEADER --}}
    <h3 class="fw-bold mb-3">📚 Create Assignment</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('assignments.store') }}"
                  enctype="multipart/form-data">

                @csrf

                {{-- CLASSROOM --}}
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                {{-- TITLE --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Assignment title"
                           required>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Instructions</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              placeholder="Write instructions for students"></textarea>
                </div>

                {{-- DUE DATE --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Due Date</label>
                    <input type="date"
                           name="due_date"
                           class="form-control">
                </div>

                {{-- MULTIPLE FILES --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Attachments (optional)
                    </label>

                    <input type="file"
                           name="files[]"
                           class="form-control"
                           multiple>

                    <small class="text-muted">
                        You can upload multiple files (PDF, images, docs…)
                    </small>
                </div>

                {{-- SUBMIT --}}
                <button class="btn btn-primary w-100">
                    ➕ Create Assignment
                </button>

            </form>

        </div>
    </div>

</div>

@endsection