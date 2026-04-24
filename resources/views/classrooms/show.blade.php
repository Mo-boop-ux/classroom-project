@extends('layouts.classroom')

@section('classroom-content')

@php use Illuminate\Support\Str; @endphp

<div class="container">

    {{-- ================= CREATE POST ================= --}}
    <div class="card shadow-sm mb-4 border-0">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('posts.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                {{-- ERROR --}}
                @error('description')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror

                <textarea name="description"
                          class="form-control mb-2"
                          placeholder="Write an announcement..."
                          required>{{ old('description') }}</textarea>

                {{-- FILE INPUT --}}
                <input type="file"
                       name="file"
                       class="form-control mb-2">

                @error('file')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror

                <button class="btn btn-primary w-100">
                    Post
                </button>

            </form>

        </div>

    </div>

    {{-- ================= POSTS ================= --}}
    @forelse($posts as $post)

        <div class="card shadow-sm mb-3 border-0">

            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-2">

                    <div>
                        <strong>{{ $post->user->name ?? 'Unknown' }}</strong>

                        <small class="text-muted d-block">
                            {{ $post->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- ACTIONS --}}
                    @if(
                        $post->user_id === auth()->id() ||
                        $post->classroom->teacher_id === auth()->id()
                    )

                        <div class="d-flex gap-2">

                            <a href="{{ route('posts.edit', $post->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('posts.destroy', $post->id) }}"
                                  onsubmit="return confirm('Delete this post?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>

                        </div>

                    @endif

                </div>

                {{-- CONTENT --}}
                <p class="mb-2">
                    {{ $post->description }}
                </p>

                {{-- ================= ATTACHMENT ================= --}}
                @if($post->file)

                    <div class="mb-3 p-2 border rounded bg-light">

                        {{-- FILE NAME --}}
                        <div class="small text-muted mb-1">
                            {{ basename($post->file) }}
                        </div>

                        {{-- IMAGE PREVIEW --}}
                        @if(Str::endsWith(strtolower($post->file), ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ asset('storage/' . $post->file) }}"
                                 class="img-fluid rounded mb-2"
                                 style="max-height: 250px;">
                        @endif

                        {{-- BUTTON --}}
                        <a href="{{ asset('storage/' . $post->file) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">
                            📎 Open File
                        </a>

                    </div>

                @endif

                <hr>

                {{-- ================= COMMENTS ================= --}}
                <div class="mb-3">

                    @forelse($post->comments as $comment)

                        <div class="border rounded p-2 mb-2 bg-light">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    {{ $comment->user->name ?? 'User' }}
                                </strong>

                                <div class="d-flex align-items-center gap-2">

                                    <small class="text-muted">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>

                                    @if(
                                        $comment->user_id === auth()->id() ||
                                        $post->classroom->teacher_id === auth()->id()
                                    )

                                        <div class="d-flex gap-1">

                                            <a href="{{ route('comments.edit', $comment->id) }}"
                                               class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('comments.destroy', $comment->id) }}"
                                                  onsubmit="return confirm('Delete this comment?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger">
                                                    🗑
                                                </button>

                                            </form>

                                        </div>

                                    @endif

                                </div>

                            </div>

                            <div class="mt-1">
                                {{ $comment->description }}
                            </div>

                        </div>

                    @empty

                        <small class="text-muted">No comments yet</small>

                    @endforelse

                </div>

                {{-- ================= ADD COMMENT ================= --}}
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf

                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                    <div class="input-group">

                        <input type="text"
                               name="description"
                               class="form-control"
                               placeholder="Write a comment..."
                               required>

                        <button class="btn btn-outline-primary">
                            Comment
                        </button>

                    </div>

                </form>

            </div>

        </div>

    @empty

        <div class="text-center text-muted mt-5">
            No posts yet in this classroom
        </div>

    @endforelse

</div>

@endsection