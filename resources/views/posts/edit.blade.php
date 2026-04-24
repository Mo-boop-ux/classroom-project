@extends('layouts.classroom')

@section('classroom-content')

@php use Illuminate\Support\Str; @endphp

<div class="container">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="fw-bold mb-3">Edit Post</h4>

            <form method="POST"
                  action="{{ route('posts.update', $post->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- DESCRIPTION --}}
                <textarea name="description"
                          class="form-control mb-3"
                          rows="4"
                          required>{{ $post->description }}</textarea>

                {{-- CURRENT FILE --}}
                @if($post->file)
                    <div class="mb-3">

                        <label class="fw-bold">Current Attachment:</label>

                        {{-- Image preview --}}
                        @if(Str::endsWith($post->file, ['jpg','jpeg','png','gif','webp']))
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $post->file) }}"
                                     class="img-fluid rounded"
                                     style="max-height: 250px;">
                            </div>
                        @endif

                        {{-- File link --}}
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $post->file) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary">
                                📎 View Attachment
                            </a>
                        </div>

                        {{-- DELETE FILE OPTION --}}
                        <div class="form-check mt-2">
                            <input type="checkbox"
                                   name="remove_file"
                                   value="1"
                                   class="form-check-input"
                                   id="removeFile">

                            <label class="form-check-label" for="removeFile">
                                Remove current file
                            </label>
                        </div>

                    </div>
                @endif

                {{-- UPLOAD NEW FILE --}}
                <div class="mb-3">
                    <label class="fw-bold">Upload New File:</label>

                    <input type="file"
                           name="file"
                           class="form-control mt-1">
                </div>

                {{-- BUTTON --}}
                <button class="btn btn-primary w-100">
                    Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

@endsection