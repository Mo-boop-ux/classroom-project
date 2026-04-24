
<!DOCTYPE html>
<html>
<head>
    <title>Classroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<ul class="nav justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Stream</a>
  </li>
  <li class="nav-item">
    @if($classroom->teacher_id === auth()->id())<a class="nav-link" href="#">Classwork</a>@endif
    @if(auth()->user()->role === 'student')<a class="nav-link" href="#">Assignments</a>@endif
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">People</a>
  </li>
</ul>


<ul class="nav justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Stream</a>
  </li>
  <li class="nav-item">
    @if($classroom->teacher_id === auth()->id())<a class="nav-link" href="#">Classwork</a>@endif
    @if(auth()->user()->role === 'student')<a class="nav-link" href="#">Assignments</a>@endif
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">People</a>
  </li>

</ul>

<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>