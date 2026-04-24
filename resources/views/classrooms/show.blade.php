@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

        <div class="card shadow-sm mb-4 border-0">

            <div class="card-body">

                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf

                    <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                    <textarea name="description"
                              class="form-control mb-2"
                              placeholder="Write an announcement..."></textarea>

                    <button class="btn btn-primary w-100">
                        Post
                    </button>

                </form>

            </div>

        </div>

   

    {{-- POSTS --}}
    @forelse($posts as $post)

        <div class="card shadow-sm mb-3 border-0">

            <div class="card-body">

                {{-- POST HEADER --}}
                <div class="mb-2">

                    <strong>
                        {{ $post->user->name ?? 'Unknown' }}
                    </strong>

                    <small class="text-muted">
                        • {{ $post->created_at->diffForHumans() }}
                    </small>

                </div>

                {{-- POST CONTENT --}}
                <p class="mb-2">
                    {{ $post->description }}
                </p>

                <hr>

                {{-- COMMENTS --}}
                <div class="mb-3">

                    @forelse($post->comments as $comment)

                        <div class="border rounded p-2 mb-2 bg-light">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    {{ $comment->user->name ?? 'User' }}
                                </strong>

                                <small class="text-muted">
                                    {{ $comment->created_at->diffForHumans() }}
                                </small>

                            </div>

                            <div class="mt-1">
                                {{ $comment->description }}
                            </div>

                        </div>

                    @empty

                        <small class="text-muted">No comments yet</small>

                    @endforelse

                </div>

                {{-- ADD COMMENT (EVERYONE CAN COMMENT) --}}
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf

                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                    <div class="input-group">

                        <input type="text"
                               name="description"
                               class="form-control"
                               placeholder="Write a comment...">

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