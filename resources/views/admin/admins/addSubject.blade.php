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

            {{-- Subject Name --}}
            <div class="mb-4">
                <label for="name">Subject Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="input @error('name') ring-red-500 @enderror">

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Code --}}
            <div class="mb-4">
                <label for="code">Subject Code</label>
                <input type="text" name="code" value="{{ old('code') }}"
                    class="input @error('code') ring-red-500 @enderror">

                @error('code')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div class="mb-4">
                <label for="section">Section</label>
                <input type="text" name="section" value="{{ old('section') }}"
                    class="input @error('section') ring-red-500 @enderror">

                @error('section')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description">Description</label>

                <textarea name="description" rows="4" class="input @error('description') ring-red-500 @enderror">{{ old('description') }}</textarea>

                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Time --}}
            <div class="mb-4">
                <label for="start_time">Start Time</label>
                <input type="text" name="start_time" value="{{ old('start_time') }}" placeholder="1:00 PM"
                    class="input @error('start_time') ring-red-500 @enderror">

                @error('start_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Time --}}
            <div class="mb-4">
                <label for="end_time">End Time</label>
                <input type="text" name="end_time" value="{{ old('end_time') }}" placeholder="5:00 PM"
                    class="input @error('end_time') ring-red-500 @enderror">

                @error('end_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cover Photo --}}
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
            {{-- Subject card component --}}
            <x-subjectCard :subject="$subject">

                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="#" class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

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
