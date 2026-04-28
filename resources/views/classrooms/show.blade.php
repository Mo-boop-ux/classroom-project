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

                <textarea name="description"
                          class="form-control mb-2"
                          placeholder="Share something with your class..."
                          required>{{ old('description') }}</textarea>

                {{-- MULTIPLE FILES --}}
                <input type="file"
                       name="files[]"
                       multiple
                       class="form-control mb-2">

                <button class="btn btn-primary">
                    Post
                </button>

            </form>

        </div>

    </div>

    {{-- ================= POSTS ================= --}}
    @forelse($posts as $post)

        @php
            $isAssignment = !empty($post->assignment_id);
        @endphp

        <div class="card shadow-sm mb-3 border-0 post-card">

            {{-- CLICK ONLY IF ASSIGNMENT --}}
            @if($isAssignment)
                <a href="{{ route('assignments.show', $post->assignment_id) }}"
                   class="stretched-link"></a>
            @endif

            <div class="card-body">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start">

                    {{-- LEFT --}}
                    <div class="d-flex gap-2">

                        <div class="avatar">
                            {{ strtoupper($post->user->name[0] ?? 'U') }}
                        </div>

                        <div>

                            <strong>{{ $post->user->name ?? 'Unknown' }}</strong>

                            <small class="text-muted d-block">
                                {{ $post->created_at->diffForHumans() }}
                            </small>

                            @if($isAssignment)
                                <small class="text-primary fw-bold">
                                    Assignment
                                </small>
                            @endif

                        </div>

                    </div>

                    {{-- DROPDOWN (FIXED Z-INDEX ISSUE) --}}
                    @if(
                        $post->user_id === auth()->id() ||
                        $post->classroom->teacher_id === auth()->id()
                    )

        <div class="dropdown position-relative" style="z-index: 10;">
            <button class="btn btn-light btn-sm dropdown-toggle"data-bs-toggle="dropdown"data-bs-display="static">
             ⋮
            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('posts.edit', $post->id) }}">
                                        Edit
                                    </a>
                                </li>

                                <li>
                                    <form method="POST"
                                          action="{{ route('posts.destroy', $post->id) }}"
                                          onsubmit="return confirmDeletePost(event)">
                                        @csrf
                                        @method('DELETE')

                                        <button class="dropdown-item text-danger">
                                            Delete
                                        </button>
                                    </form>
                                </li>

                            </ul>

                        </div>

                    @endif

                </div>

                {{-- CONTENT --}}
                <p class="mt-2 mb-2">{{ $post->description }}</p>

                {{-- ================= ATTACHMENTS ================= --}}
                @if($post->attachments->count())

                    <div class="attachment-box">

                        @foreach($post->attachments as $file)

                            @php
                                $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                            @endphp

                            {{-- IMAGE --}}
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/' . $file->file_path) }}"
                                     class="img-fluid rounded mb-2 clickable-img">
                            @endif

                            {{-- FILE LINK --}}
                            <div class="d-flex justify-content-between align-items-center mb-1">

                                <small class="text-muted">
                                    {{ basename($file->file_path) }}
                                </small>

                                <a href="{{ asset('storage/' . $file->file_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    Open
                                </a>

                            </div>

                        @endforeach

                    </div>

                @endif

               {{-- ================= COMMENTS ONLY FOR POSTS ================= --}}
                @if(!$isAssignment)

                <div class="mt-3">

                    {{-- LINK NOT BUTTON --}}
                    <a href="#"
                       class="text-decoration-none"
                       data-bs-toggle="collapse"
                       data-bs-target="#comments-{{ $post->id }}">
                        💬 Comments ({{ $post->comments->count() }})
                    </a>

                    <div class="collapse mt-2" id="comments-{{ $post->id }}">

                        {{-- COMMENTS LIST --}}
                        @forelse($post->comments as $comment)

                        <div class="comment-box d-flex justify-content-between">

                            <div class="w-100">

                                <div class="d-flex justify-content-between">
                                    <strong>{{ $comment->user->name }}</strong>

                                    <small class="text-muted">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                {{-- TEXT --}}
                                <div id="text-{{ $comment->id }}">
                                    {{ $comment->description }}
                                </div>

                                {{-- EDIT --}}
                                <form method="POST"
                                      action="{{ route('comments.update', $comment->id) }}"
                                      class="d-none mt-2"
                                      id="form-{{ $comment->id }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="d-flex gap-2">
                                        <input type="text"
                                               name="description"
                                               value="{{ $comment->description }}"
                                               class="form-control">

                                        <button class="btn btn-success btn-sm">Save</button>
                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                onclick="cancelEdit({{ $comment->id }})">
                                            Cancel
                                        </button>
                                    </div>
                                </form>

                            </div>

                            {{-- COMMENT 3 DOTS --}}
                            @if(
                                $comment->user_id === auth()->id() ||
                                $post->classroom->teacher_id === auth()->id()
                            )
                            <div class="dropdown ms-2">
                                <button class="btn btn-light btn-sm"
                                        data-bs-toggle="dropdown">
                                    ⋮
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <button class="dropdown-item"
                                                onclick="editComment({{ $comment->id }})">
                                            Edit
                                        </button>
                                    </li>

                                    <li>
                                        <form method="POST"
                                              action="{{ route('comments.destroy', $comment->id) }}"
                                              onsubmit="return confirmDeleteComment(event)">
                                            @csrf
                                            @method('DELETE')

                                            <button class="dropdown-item text-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </li>

                                </ul>
                            </div>
                            @endif

                        </div>

                        @empty
                            <small>No comments yet</small>
                        @endforelse

                        {{-- ADD COMMENT --}}
                        <form method="POST"
                              action="{{ route('comments.store') }}"
                              class="mt-2">
                            @csrf

                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                            <div class="d-flex gap-2">
                                <input type="text"
                                       name="description"
                                       class="form-control"
                                       placeholder="Write comment...">

                                <button class="btn btn-primary btn-sm">
                                    Send
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

                @endif

            </div>

        </div>

    @empty
        <div class="text-center text-muted mt-5">
            No posts yet
        </div>
    @endforelse

</div>

{{-- ================= JS ================= --}}
<script>
function editComment(id){
    document.getElementById('text-'+id).classList.add('d-none');
    document.getElementById('form-'+id).classList.remove('d-none');
}

function cancelEdit(id){
    document.getElementById('text-'+id).classList.remove('d-none');
    document.getElementById('form-'+id).classList.add('d-none');
}

function confirmDeletePost(e){
    e.preventDefault();

    Swal.fire({
        title: 'Delete post?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });

    return false;
}

function confirmDeleteComment(e){
    e.preventDefault();

    Swal.fire({
        title: 'Delete comment?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });

    return false;
}

</script>

{{-- ================= STYLE ================= --}}
<style>

.post-card {
    transition: 0.2s ease;
}

.post-card:hover {
    transform: translateY(-3px);
}

.avatar {
    width:40px;
    height:40px;
    border-radius:50%;
    background:#6c757d;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
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
    display:block;
    margin-bottom: 10px;
}

.post-card .dropdown {
    position: relative;
    z-index: 20;
}

.post-card .stretched-link {
    z-index: 1;
}

</style>

@endsection