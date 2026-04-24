<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    {{-- CUSTOM CSS --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>


<div class="auth-container">

    <div class="auth-card">

        {{-- TITLE --}}
        <h2 class="auth-title">Login</h2>

        {{-- LOGIN FORM --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- EMAIL --}}
            <input type="email"
                   name="email"
                   class="auth-input"
                   placeholder="Email"
                   required>

            {{-- PASSWORD --}}
            <input type="password"
                   name="password"
                   class="auth-input"
                   placeholder="Password"
                   required>

            {{-- REMEMBER ME --}}
            <div style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>

            {{-- BUTTON --}}
            <button type="submit" class="auth-btn">
                Login
            </button>

        </form>

        {{-- FOOTER --}}
        <div class="auth-footer">
            Don’t have an account?
            <a href="{{ route('register') }}">Register</a>
        </div>

    </div>

</div>



</body>
</html>
