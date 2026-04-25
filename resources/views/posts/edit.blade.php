@extends('layouts.classroom')

@section('classroom-content')

@php use Illuminate\Support\Str; @endphp

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="mb-4">
        <h3 class="fw-bold">✏️ Edit Post</h3>
        <p class="text-muted mb-0">Manage text and attachments</p>
    </div>

    {{-- ================= UPDATE POST FORM ================= --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('posts.update', $post->id) }}"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- DESCRIPTION --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description</label>

                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              required>{{ old('description', $post->description) }}</textarea>
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
                        You can upload multiple files (images, pdf, docs…)
                    </small>

                    @error('files.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ACTIONS --}}
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}"
                       class="btn btn-outline-secondary w-50">
                        Cancel
                    </a>

                    <button class="btn btn-primary w-50">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ================= ATTACHMENTS (SEPARATE SECTION) ================= --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">Attachments</h5>

                <span class="badge bg-secondary">
                    {{ $post->attachments->count() }} files
                </span>
            </div>

            @forelse($post->attachments as $attachment)

                <div class="attachment-item">

                    {{-- LEFT --}}
                    <div class="d-flex align-items-center gap-3 flex-grow-1">

                        {{-- IMAGE --}}
                        @if(Str::endsWith(strtolower($attachment->file_path), ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                 class="attachment-preview">
                        @endif

                        {{-- INFO --}}
                        <div>

                            <div class="small fw-semibold">
                                {{ basename($attachment->file_path) }}
                            </div>

                            <div class="d-flex gap-2 mt-1">
                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    Open
                                </a>
                            </div>

                        </div>

                    </div>

                    {{-- DELETE --}}
                    <form method="POST"
                          action="{{ route('attachments.destroy', $attachment->id) }}"
                          onsubmit="event.stopPropagation(); return confirm('Delete this file?')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                onclick="event.stopPropagation();">
                            🗑
                        </button>

                    </form>

                </div>

            @empty

                <div class="text-muted small">
                    No attachments yet
                </div>

            @endforelse

        </div>
    </div>

</div>

{{-- ================= STYLES ================= --}}
<style>

.attachment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafafa;
    border: 1px solid #eee;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: 0.2s;
}

.attachment-item:hover {
    background: #f5f5f5;
}

.attachment-preview {
    max-height: 60px;
    border-radius: 6px;
}

form, button {
    position: relative;
    z-index: 10;
}

</style>

@endsection