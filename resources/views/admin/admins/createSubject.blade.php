<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('subjects.index') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Add a new subject</h2>

    {{-- Session Messages --}}
    @if (session('success'))
        <x-flashMsg msg="{{ session('success') }}" />
    @elseif (session('delete'))
        <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
    @endif

    @if (session('duplicate_sections'))
    <div class="bg-yellow-200 text-yellow-800 p-4 rounded-md mb-4">
        <h3 class="font-bold">Duplicate Subjects Detected</h3>
        <table class="min-w-full bg-white border border-gray-300 rounded-md">
            <thead class="bg-yellow-300">
                <tr>
                    <th class="px-4 py-2 border">Code</th>
                    <th class="px-4 py-2 border">Day</th>
                    <th class="px-4 py-2 border">Section</th>
                </tr>
            </thead>
            <tbody>
                @foreach (session('duplicate_sections') as $duplicate)
                    <tr>
                        <td class="px-4 py-2 border">{{ $duplicate['code'] }}</td>
                        <td class="px-4 py-2 border">{{ $duplicate['day'] }}</td>
                        <td class="px-4 py-2 border">{{ $duplicate['section'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="mt-4">These subjects were skipped due to duplication.</p>
    </div>
    @endif

    @if (session('conflicting_subjects'))
    <div class="bg-yellow-200 text-yellow-800 p-4 rounded-md mb-4">
        <h3 class="font-bold">Conflicting Subjects Detected</h3>
        <table class="min-w-full bg-white border border-gray-300 rounded-md">
            <thead class="bg-yellow-300">
                <tr>
                    <th class="px-4 py-2 border">Code</th>
                    <th class="px-4 py-2 border">Day</th>
                    <th class="px-4 py-2 border">Section</th>
                    <th class="px-4 py-2 border">Start Time</th>
                    <th class="px-4 py-2 border">End Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach (session('conflicting_subjects') as $conflict)
                    <tr>
                        <td class="px-4 py-2 border">{{ $conflict->code }}</td>
                        <td class="px-4 py-2 border">{{ $conflict->day }}</td>
                        <td class="px-4 py-2 border">{{ $conflict->section }}</td>
                        <td class="px-4 py-2 border">{{ $conflict->start_time }}</td>
                        <td class="px-4 py-2 border">{{ $conflict->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="mt-4">The added subject's time is fully within the time range of these existing subjects. Please modify the time or day.</p>
    </div>
@endif

        {{-- Create Subject Form --}}
        <form action="{{ route('subjects.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- Subject Name --}}
            <div class="mb-4">
                <label for="name">Subject Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="input @error('name') ring-red-500 @enderror" placeholder="Enter the subject name" required>

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Code --}}
            <div class="mb-4">
                <label for="code">Subject Code</label>
                <input type="text" name="code" value="{{ old('code') }}"
                    class="input @error('code') ring-red-500 @enderror" placeholder="Enter the subject code" required>

                @error('code')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div class="mb-4">
                <label for="section">Section</label>
                <input type="text" name="section" value="{{ old('section') }}"
                    class="input @error('section') ring-red-500 @enderror" placeholder="Enter the section" required>

                @error('section')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description">Description</label>
                <textarea name="description" rows="4" class="input @error('description') ring-red-500 @enderror" placeholder="Enter a brief description of the subject" required>{{ old('description') }}</textarea>

                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Time --}}
            <div class="mb-4">
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="input @error('start_time') ring-red-500 @enderror" required>
            
                @error('start_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- End Time --}}
            <div class="mb-4">
                <label for="end_time">End Time</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                       class="input @error('end_time') ring-red-500 @enderror" required>
            
                @error('end_time')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Day --}}
            <div class="mb-4">
                <label for="day">Subject Day</label>
                <select name="day" class="input @error('day') ring-red-500 @enderror" required>
                    <option value="">Select a day</option>
                    <option value="Monday" {{ old('day') == 'Monday' ? 'selected' : '' }}>Monday</option>
                    <option value="Tuesday" {{ old('day') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                    <option value="Wednesday" {{ old('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                    <option value="Thursday" {{ old('day') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                    <option value="Friday" {{ old('day') == 'Friday' ? 'selected' : '' }}>Friday</option>
                    <option value="Saturday" {{ old('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Sunday" {{ old('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                </select>

                @error('day')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cover Photo --}}
            <div class="mb-4">
                <label for="image">Cover Photo</label>
                <input type="file" name="image" id="image" class="input @error('image') ring-red-500 @enderror">

                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button class="btn">Add Subject</button>

        </form>
    </div>
</x-adminlayout>
