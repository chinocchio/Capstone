<x-adminlayout>
    {{-- Heading --}}
    {{-- <h1 class="title">Welcome {{ auth()->user()->username }}, you have {{ $posts->total() }} subjects</h1> --}}
    <h1 class="title">Welcome Admin</h1>

    <div class="card mb-4">
        <h2 class="font-bold mb-4">Add a new subject</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Create Post Form --}}
        <form action="{{ route('posts.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- Post Title --}}
            <div class="mb-4">
                <label for="title">Subject Code and Subject Name</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="input @error('title') ring-red-500 @enderror">

                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Post Body --}}
            <div class="mb-4">
                <label for="body">Description</label>

                <textarea name="body" rows="4" class="input @error('body') ring-red-500 @enderror">{{ old('body') }}</textarea>

                @error('body')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Post Image --}}
            <div class="mb-4">
                <label for="image">Cover photo</label>
                <input type="file" name="image" id="image">

                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button class="btn">Add</button>

        </form>
    </div>

    {{-- User Posts --}}
    <h2 class="font-bold mb-4">Your Subjects</h2>


</x-adminlayout>