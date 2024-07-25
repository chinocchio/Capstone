
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

            {{-- Remembe checkbox --}}
            <div class="mb-4">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>

            @error('failed')
                    <p class="error"> {{ $message }} </p>
            @enderror

            {{-- Button --}}
            <button class="btn">Login</button>
            <div class="w-3/5 mx-auto mt-4">
                <a href="{{ route('google-auth') }}" >Sign in with Google</a>
            </div>
            
        </form>

    </div>

</x-layout>
