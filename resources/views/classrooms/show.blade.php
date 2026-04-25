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

                @error('description')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror

                <textarea name="description"
                          class="form-control mb-2"
                          placeholder="Share something with your class..."
                          required>{{ old('description') }}</textarea>

                <div class="d-flex gap-2 align-items-center">
                    <input type="file" name="file" class="form-control">

                    <button class="btn btn-primary">
                        Post
                    </button>
                </div>

                @error('file')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror

            </form>

        </div>

    </div>


    {{-- ================= POSTS ================= --}}
    @forelse($posts as $post)

        @php
            $isAssignment = !empty($post->assignment_id);
            $openLink = $isAssignment
                ? route('assignments.show', $post->assignment_id)
                : null;
        @endphp

        <div class="card shadow-sm mb-3 border-0 post-card position-relative">

            {{-- 🔥 CLICKABLE OVERLAY ONLY FOR ASSIGNMENTS --}}
            @if($isAssignment)
                <a href="{{ $openLink }}" class="stretched-link"></a>
            @endif

            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-2">

                    <div class="d-flex align-items-center gap-2">

                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center"
                             style="width:40px;height:40px;">
                            {{ strtoupper($post->user->name[0] ?? 'U') }}
                        </div>

                        <div>
                            <strong>{{ $post->user->name ?? 'Unknown' }}</strong>

                            <small class="text-muted d-block">
                                {{ $post->created_at->diffForHumans() }}
                            </small>

                            {{-- LABEL --}}
                            @if($isAssignment)
                                <small class="text-primary fw-bold">
                                    📚 Assignment
                                </small>
                            @endif
                        </div>

                    </div>

                    {{-- 3 DOT MENU --}}
                    @if(
                        $post->user_id === auth()->id() ||
                        $post->classroom->teacher_id === auth()->id()
                    )

                        <div class="dropdown position-relative" style="z-index: 9999;">

                            <button class="btn btn-sm btn-light"
                                    data-bs-toggle="dropdown">
                                ⋮
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('posts.edit', $post->id) }}">
                                        ✏️ Edit
                                    </a>
                                </li>

                                <li>
                                    <form method="POST"
                                          action="{{ route('posts.destroy', $post->id) }}"
                                          onsubmit="return confirm('Delete this post?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="dropdown-item text-danger">
                                            🗑 Delete
                                        </button>

                                    </form>
                                </li>

                            </ul>

                        </div>

                    @endif

                </div>

                {{-- CONTENT --}}
                <p class="mb-2">{{ $post->description }}</p>

                {{-- ================= ATTACHMENT ================= --}}
                @if($post->file)

                    <div class="attachment-box">

                        @if(Str::endsWith(strtolower($post->file), ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ asset('storage/' . $post->file) }}"
                                 class="img-fluid rounded mb-2 clickable-img"
                                 onclick="openImage(this.src)">
                        @endif

                        <div class="d-flex justify-content-between align-items-center">

                            <small class="text-muted">
                                {{ basename($post->file) }}
                            </small>

                            <a href="{{ asset('storage/' . $post->file) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Open
                            </a>

                        </div>

                    </div>

                @endif

                {{-- ================= COMMENTS ================= --}}
                <div class="mt-3">

                    <button class="btn btn-sm btn-light mb-2"
                            data-bs-toggle="collapse"
                            data-bs-target="#comments-{{ $post->id }}">
                        💬 Comments ({{ $post->comments->count() }})
                    </button>

                    <div class="collapse" id="comments-{{ $post->id }}">

                        @forelse($post->comments as $comment)

                            <div class="comment-box">

                                <div class="d-flex justify-content-between">

                                    <strong>{{ $comment->user->name ?? 'User' }}</strong>

                                    <div class="d-flex gap-2">

                                        <small class="text-muted">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </small>

                                        @if(
                                            $comment->user_id === auth()->id() ||
                                            $post->classroom->teacher_id === auth()->id()
                                        )

                                            <a href="{{ route('comments.edit', $comment->id) }}"
                                               class="text-warning small">Edit</a>

                                            <form method="POST"
                                                  action="{{ route('comments.destroy', $comment->id) }}">

                                                @csrf
                                                @method('DELETE')

                                                <button class="text-danger border-0 bg-transparent small">
                                                    Delete
                                                </button>

                                            </form>

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

                        {{-- ADD COMMENT --}}
                        <form method="POST" action="{{ route('comments.store') }}" class="mt-2">
                            @csrf

                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                            <div class="input-group">
                                <input type="text"
                                       name="description"
                                       class="form-control"
                                       placeholder="Write a comment..."
                                       required>

                                <button class="btn btn-outline-primary">
                                    Send
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    @empty

        <div class="text-center text-muted mt-5">
            No posts yet in this classroom
        </div>

    @endforelse

</div>

{{-- ================= IMAGE MODAL ================= --}}
<div id="imageModal" onclick="this.style.display='none'">
    <img id="modalImg">
</div>

{{-- ================= STYLES ================= --}}
<style>

.post-card {
    transition: 0.2s ease;
}

.post-card:hover {
    transform: translateY(-3px);
}

.attachment-box {
    border: 1px solid #eee;
    padding: 10px;
    border-radius: 8px;
    background: #fafafa;
}

.comment-box {
    background: #f5f5f5;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 6px;
}

.clickable-img {
    cursor: pointer;
    max-height: 250px;
}

#imageModal {
    display: none;
    position: fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.8);
    justify-content:center;
    align-items:center;
}

#imageModal img {
    max-width: 90%;
    max-height: 90%;
}

</style>

{{-- ================= SCRIPT ================= --}}
<script>
function openImage(src) {
    let modal = document.getElementById('imageModal');
    let img = document.getElementById('modalImg');

    img.src = src;
    modal.style.display = 'flex';
}
</script>

@endsection