<!-- resources/views/partials/navbar.blade.php -->
<nav class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div>
            <a href="{{ url('/') }}" class="text-lg font-bold">MyApp</a>
        </div>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="mr-4">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mr-4">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>
</nav>
