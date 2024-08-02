
<x-adminlayout>
        
    <h1 class="title">Admin Login</h1>

    <div class="mx-auto max-w-screen-sm card">

        <form action="{{ route('admin_login_submit') }}" method="post">

            @csrf

             {{-- Email --}}
             <div class="mb-4">
                <label for="username">Username:</label>
                <input type="text" name="username" value="{{ old('username')}}" 
                class="input @error('username') ring-red-500 @enderror">
                @error('username')
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

            @error('failed')
                    <p class="error"> {{ $message }} </p>
            @enderror

            {{-- Button --}}
            <button class="btn">Login</button>
        </form>

    </div>

</x-adminlayout>
