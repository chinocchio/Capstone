<x-layout>

    <h1 class="title">Welcome back</h1>

    <div class="mx-auto max-w-screen-sm card">

        <form action="{{ route('login') }}" method="post">

            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email">Email:</label>
                <input type="text" name="email" value="{{ old('email')}}" 
                class="input @error('email') ring-red-500 @enderror">
                @error('email')
                    <p class="error"> {{ $message }} </p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password">Password:</label>
                <input type="password" name="password" id="" 
                class="input @error('password') ring-red-500 @enderror">
                @error('password')
                    <p class="error"> {{ $message }} </p>
                @enderror
            </div>

            {{-- Remember checkbox --}}
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember">Remember Me</label>
            </div>

            @error('failed')
                <p class="error"> {{ $message }} </p>
            @enderror

            {{-- Button --}}
            <button class="btn">Login</button>
            
            {{-- Google Sign-In Button --}}
            <div class="w-full mt-4">
                <a href="{{ route('google-auth') }}" class="btn google-signin-btn flex items-center justify-center">
                    <img src={{ asset('storage/posts_images/google.png') }} alt="Google Logo" class="w-5 h-5 mr-2">
                    Sign in with Google
                </a>
            </div>

        </form>

    </div>

</x-layout>

<style>
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 4px;
        background-color: #007BFF; /* Button color */
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #0056b3; /* Darker shade for hover effect */
    }

    .google-signin-btn {
        background-color: #007BFF; /* Match the login button color */
    }

    .google-signin-btn:hover {
        background-color: #0056b3; /* Match the login button hover color */
    }

    .google-signin-btn img {
        width: 20px;
        height: 20px;
    }
</style>
