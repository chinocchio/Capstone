<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('subjects.index') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Edit subject</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Create Post Form --}}
        <form action="{{ route('subjects.update', $subject->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- Subject Name --}}
            <div class="mb-4">
                <label for="name">Subject Name</label>
                <input type="text" name="name" value="{{ $subject->name }}"
                    class="input @error('name') ring-red-500 @enderror">

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Code --}}
            <div class="mb-4">
                <label for="code">Subject Code</label>
                <input type="text" name="code" value="{{ $subject->code }}"
                    class="input @error('code') ring-red-500 @enderror">

                @error('code')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div class="mb-4">
                <label for="section">Section</label>
                <input type="text" name="section" value="{{ $subject->section }}"
                    class="input @error('section') ring-red-500 @enderror">

                @error('section')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description">Description</label>

                <textarea name="description" rows="4" class="input @error('description') ring-red-500 @enderror">{{ $subject->description }}</textarea>

                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Time --}}
            <div class="mb-4">
                <label for="start_time">Start Time</label>
                <input type="text" name="start_time" value="{{ $subject->start_time }}" placeholder="1:00 PM"
                    class="input @error('start_time') ring-red-500 @enderror">

                @error('start_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Time --}}
            <div class="mb-4">
                <label for="end_time">End Time</label>
                <input type="text" name="end_time" value="{{ $subject->end_time }}" placeholder="5:00 PM"
                    class="input @error('end_time') ring-red-500 @enderror">

                @error('end_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Day --}}
            <div class="mb-4">
                <label for="day">Subject Day</label>
                <input type="text" name="day" value="{{ $subject->day }}"
                    class="input @error('day') ring-red-500 @enderror">

                @error('name')
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
            <button class="btn">Edit</button>

        </form>
    </div>
</x-adminlayout>