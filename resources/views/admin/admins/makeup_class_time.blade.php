<x-adminlayout>
    {{-- Heading --}}
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('subjects.index') }}" class="text-xs text-blue-500 hover:text-blue-700 transition">&larr; Go back to Subjects</a>
    </div>

    <h2 class="font-bold text-2xl text-gray-700 mb-6">Create Makeup Class for: <span class="text-blue-500">{{ $subject->name }}</span></h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Form for selecting date and time --}}
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-700 mb-4">Makeup Class Details</h3>
            <form action="{{ route('makeupClass.store', $subject->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="date" class="block mb-2 font-semibold text-gray-600">Select Date</label>
                    <input type="date" name="date" id="date" class="w-full p-3 border rounded-md @error('date') ring-red-500 @enderror" required>
                    @error('date')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block mb-2 font-semibold text-gray-600">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="w-full p-3 border rounded-md @error('start_time') ring-red-500 @enderror" required>
                    @error('start_time')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block mb-2 font-semibold text-gray-600">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="w-full p-3 border rounded-md @error('end_time') ring-red-500 @enderror" required>
                    @error('end_time')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-md transition">Create Makeup Class</button>
                </div>
            </form>
        </div>

        {{-- Linked Instructor --}}
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-700 mb-4">Linked Instructor</h3>
            @if ($instructors->isEmpty())
                <p class="text-gray-600">No instructors are linked to this subject.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($instructors as $instructor)
                        <li class="bg-gray-100 p-3 rounded-md shadow-sm">
                            <span class="font-semibold text-blue-600">{{ $instructor->username }}</span> 
                            <br><span class="text-sm text-gray-500">{{ $instructor->email }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Enrolled Students --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mt-8">
        <h3 class="font-semibold text-lg text-gray-700 mb-4">Enrolled Students</h3>
        @if ($students->isEmpty())
            <p class="text-gray-600">No students are enrolled in this subject.</p>
        @else
            <ul class="space-y-2">
                @foreach ($students as $student)
                    <li class="bg-gray-100 p-3 rounded-md shadow-sm">
                        <span class="font-semibold text-blue-600">{{ $student->name }}</span> 
                        <br><span class="text-sm text-gray-500">{{ $student->email }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-adminlayout>
