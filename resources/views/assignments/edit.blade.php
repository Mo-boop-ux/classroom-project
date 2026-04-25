@extends('layouts.classroom')

@section('classroom-content')

@php use Illuminate\Support\Str; @endphp

<div class="container">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="fw-bold mb-3">Edit Assignment</h4>

            {{-- ================= MAIN FORM ================= --}}
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
                           value="{{ old('title', $assignment->title) }}"
                           required>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label class="form-label">Instructions</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ old('description', $assignment->description) }}</textarea>
                </div>

                {{-- DUE DATE --}}
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date"
                           name="due_date"
                           class="form-control"
                           value="{{ old('due_date', $assignment->due_date) }}">
                </div>

                {{-- ADD NEW FILES --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Add More Attachments
                    </label>

                    <input type="file"
                           name="files[]"
                           class="form-control"
                           multiple>

                    <small class="text-muted">
                        You can upload multiple files (PDF, images, docs…)
                    </small>
                </div>

                {{-- SAVE BUTTON --}}
                <button class="btn btn-primary w-100">
                    Save Changes
                </button>

            </form>

        </div>
    </div>

    {{-- ================= ATTACHMENTS SECTION ================= --}}
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">Current Attachments</h5>

                <span class="badge bg-secondary">
                    {{ $assignment->attachments->count() }} files
                </span>

            </div>

            @forelse($assignment->attachments as $file)

                <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light">

                    {{-- FILE INFO --}}
                    <div class="small text-muted">
                        📎 {{ basename($file->file_path) }}
                    </div>

                    <div class="d-flex gap-2 align-items-center">

                        {{-- OPEN FILE --}}
                        <a href="{{ asset('storage/' . $file->file_path) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">
                            Open
                        </a>

                        {{-- DELETE ATTACHMENT (FIXED ROUTE) --}}
    <form method="POST" action="{{ route('assignment.attachments.destroy', $file->id) }}" onsubmit="return confirm('Delete this file?')">

    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-sm btn-outline-danger">
        🗑
    </button>

    </form>

                    </div>

                </div>

            @empty

                <div class="text-muted small">
                    No attachments yet
                </div>

            @endforelse

        </div>
    </div>

</div>

@endsection