<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('subjects.index') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Edit Subject</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Edit Subject Form --}}
        <form action="{{ route('subjects.update', $subject->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Subject Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}"
                    class="input mt-1 block w-full @error('name') border-red-500 @enderror" placeholder="Enter the subject name" required>
                
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Code --}}
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}"
                    class="input mt-1 block w-full @error('code') border-red-500 @enderror" placeholder="Enter the subject code" required>

                @error('code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div class="mb-4">
                <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                <input type="text" name="section" id="section" value="{{ old('section', $subject->section) }}"
                    class="input mt-1 block w-full @error('section') border-red-500 @enderror" placeholder="Enter the section" required>

                @error('section')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" 
                    class="input mt-1 block w-full @error('description') border-red-500 @enderror" placeholder="Enter a brief description" required>{{ old('description', $subject->description) }}</textarea>

                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Time --}}
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $subject->start_time) }}"
                    class="input mt-1 block w-full @error('start_time') border-red-500 @enderror" required>

                @error('start_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Time --}}
            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $subject->end_time) }}"
                    class="input mt-1 block w-full @error('end_time') border-red-500 @enderror" required>

                @error('end_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Day --}}
            <div class="mb-4">
                <label for="day" class="block text-sm font-medium text-gray-700">Subject Day</label>
                <select name="day" id="day" class="input mt-1 block w-full @error('day') border-red-500 @enderror" required>
                    <option value="">Select a day</option>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <option value="{{ $day }}" {{ old('day', $subject->day) == $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>

                @error('day')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cover Photo --}}
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Cover Photo</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full @error('image') border-red-500 @enderror">

                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Save Changes
            </button>
        </form>
    </div>
</x-adminlayout>
