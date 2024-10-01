<x-adminlayout>
    <a href="{{ route('admin_dashboard') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back to your dashboard</a>

    {{-- Update form card --}}
    <div class="card">
        <h2 class="font-bold mb-4">Update Instructor</h2>

        <form action="{{ route('users.update', $instructor->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ID Field --}}
            <div class="mb-4">
                <label for="id" class="block">ID</label>
                <input type="text" id="id" name="id" value="{{ $instructor->id }}" class="input w-full @error('id') ring-red-500 @enderror" readonly>
                @error('id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Instructor Number Field --}}
            <div class="mb-4">
                <label for="instructor_number" class="block">Instructor Number</label>
                <input type="text" id="instructor_number" name="instructor_number" value="{{ $instructor->instructor_number }}" class="input w-full @error('instructor_number') ring-red-500 @enderror">
                @error('instructor_number')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username and Email Fields --}}
            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <label for="username" class="block">Username</label>
                    <input type="text" id="username" name="username" value="{{ $instructor->username }}" class="input w-full @error('username') ring-red-500 @enderror">
                    @error('username')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="email" class="block">Email</label>
                    <input type="email" id="email" name="email" value="{{ $instructor->email }}" class="input w-full @error('email') ring-red-500 @enderror">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- PIN and Avatar Fields --}}
            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <label for="pin" class="block">PIN</label>
                    <input type="number" id="pin" name="pin" value="{{ $instructor->pin }}" class="input w-full @error('pin') ring-red-500 @enderror">
                    @error('pin')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="avatar" class="block">Avatar</label>
                    <input type="file" id="avatar" name="avatar" class="input w-full @error('avatar') ring-red-500 @enderror">
                    @error('avatar')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- School Year and Semester Fields --}}
            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <label for="school_year" class="block">School Year</label>
                    <select id="school_year" name="school_year" class="input w-full @error('school_year') ring-red-500 @enderror">
                        <option value="2023-2024" {{ $instructor->school_year == '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                        <option value="2024-2025" {{ $instructor->school_year == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                        <option value="2025-2026" {{ $instructor->school_year == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                        <!-- Add more options as needed -->
                    </select>
                    @error('school_year')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="semester" class="block">Semester</label>
                    <select id="semester" name="semester" class="input w-full @error('semester') ring-red-500 @enderror">
                        <option value="1st Semester" {{ $instructor->semester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ $instructor->semester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="Summer" {{ $instructor->semester == 'Summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                    @error('semester')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linked Subjects --}}
            <div class="mb-4">
                <h2 class="font-bold mb-4">Linked Subjects</h2>
                <ul class="list-disc pl-6 mb-8">
                    @foreach($linkedSubjects as $subject)
                        <li class="mb-2 text-lg">{{ $subject->name }} - <span class="text-gray-600">{{ $subject->code }}</span></li>
                    @endforeach
                </ul>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn">Update</button>
        </form>
    </div>
</x-adminlayout>
