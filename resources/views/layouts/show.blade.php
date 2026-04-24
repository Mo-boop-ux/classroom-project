
<h2>{{ $classroom->name }}</h2>

<ul class="nav justify-content-center mb-3">

  <li class="nav-item">
    <a class="nav-link" href="{{ route('classrooms.show', $classroom->id) }}">
        Stream
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="{{ route('classrooms.classwork', $classroom->id) }}">
        Classwork
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="{{ route('classrooms.people', $classroom->id) }}">
        People
    </a>
  </li>

</ul>

<hr>

@foreach($posts as $post)
    <div class="card mb-3">
        <div class="card-body">
            {{ $post->description }}
        </div>
    </div>
@endforeach

@endsection


