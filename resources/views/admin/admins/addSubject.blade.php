<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('admin_dashboard') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back to your dashboard</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Add a new subject</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Create Post Form --}}
        <form action="{{ route('subjects.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- Post Title --}}
            <div class="mb-4">
                <label for="name">Subject Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="input @error('name') ring-red-500 @enderror">

                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="code">Subject Code</label>
                <input type="text" name="code" value="{{ old('code') }}"
                    class="input @error('code') ring-red-500 @enderror">

                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Post Body --}}
            <div class="mb-4">
                <label for="description">Description</label>

                <textarea name="description" rows="4" class="input @error('description') ring-red-500 @enderror">{{ old('description') }}</textarea>

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

    <div class="grid grid-cols-2 gap-6">
        @foreach ($subject as $subject)
            {{-- Post card component --}}
            <x-subjectCard :subject="$subject">

                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="#"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                    {{-- Delete post --}}
                    <form action="#" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-2 py-1 text-xs rounded-md">Delete</button>
                    </form>
                </div>
            </x-subjectCard>
        @endforeach
    </div>




</x-adminlayout>