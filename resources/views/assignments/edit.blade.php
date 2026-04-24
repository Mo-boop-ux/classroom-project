@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="fw-bold mb-3">Edit Assignment</h4>

            <form method="POST"
                  action="{{ route('assignments.update', $assignment->id) }}"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- TITLE --}}
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ $assignment->title }}"
                           required>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label class="form-label">Instructions</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ $assignment->description }}</textarea>
                </div>

                {{-- DUE DATE --}}
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date"
                           name="due_date"
                           class="form-control"
                           value="{{ $assignment->due_date }}">
                </div>

                {{-- CURRENT FILE --}}
                @if($assignment->file)
                    <div class="mb-2">
                        <a href="{{ asset('storage/' . $assignment->file) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm">
                            View Current Attachment
                        </a>
                    </div>
                @endif

                {{-- NEW FILE --}}
                <div class="mb-3">
                    <label class="form-label">Replace Attachment</label>
                    <input type="file"
                           name="file"
                           class="form-control">
                </div>

                <button class="btn btn-primary w-100">
                    Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

@endsection