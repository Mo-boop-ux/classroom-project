@extends('layouts.classroom')

@section('classroom-content')

<div class="container">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="fw-bold mb-3">Edit Comment</h4>

            <form method="POST" action="{{ route('comments.update', $comment->id) }}">
                @csrf
                @method('PUT')

                <textarea name="description"
                          class="form-control mb-3"
                          rows="3"
                          required>{{ $comment->description }}</textarea>

                <button class="btn btn-primary w-100">
                    Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

@endsection