
<x-layout>
        
    <h1 class="title">Register a new account</h1>

    <div class="mx-auto max-w-screen-sm card">

        <form action="{{ route('register') }}" method="post">

            @csrf

            {{-- Username --}}
            <div class="mb-4">
                <label for="username">Username:</label>
                <input type="text" name="username" value="{{ old('username')}}" 
                class="input @error('username') ring-red-500 @enderror">
                @error('username')
                    <p class="error"> {{ $message }} </p>
                @enderror
            </div>

             {{-- Email --}}
             <div class="mb-4">
                <label for="email">Email:</label>
                <input type="text" name="email" value="{{ old('email')}}" 
                class="input @error('email') ring-red-500 @enderror">
                @error('email')
                    <p class="error"> {{ $message }} </p>
                @enderror
            </div>

                        {{-- Instructor Number --}}
                        <div class="mb-4">
                            <label for="instructor_number">Instructor Number:</label>
                            <input type="text" name="instructor_number" value="{{ old('instructor_number')}}" 
                            class="input @error('instructor_number') ring-red-500 @enderror">
                            @error('instructor_number')
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

            {{-- Password Confirmation --}}
            <div class="mb-8">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" name="password_confirmation" id="" 
                class="input @error('password') ring-red-500 @enderror">
            </div>

            {{-- Button --}}
            <button class="btn">Register</button>
        </form>

    </div>

</x-layout>
