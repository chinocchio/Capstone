<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('user.show') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Add a New Instructor</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Create Instructor Form --}}
        <form action="{{ route('store_instructors') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- Instructor Number --}}
            <div class="mb-4">
                <label for="instructor_number">Instructor Number</label>
                <input type="text" name="instructor_number" value="{{ old('instructor_number') }}"
                    class="input @error('instructor_number') ring-red-500 @enderror" placeholder="Enter instructor number" required>

                @error('instructor_number')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="input @error('name') ring-red-500 @enderror" placeholder="Enter instructor name" required>

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="input @error('email') ring-red-500 @enderror" placeholder="Enter instructor email" required>

                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- School Year --}}
            <div class="mb-4">
                <label for="school_year">School Year</label>
                <select name="school_year" class="input @error('school_year') ring-red-500 @enderror" required>
                    @php
                        $currentYear = now()->year;
                    @endphp
                    @for ($i = 0; $i <= 5; $i++)
                        @php
                            $startYear = $currentYear + $i;
                            $endYear = $startYear + 1;
                        @endphp
                        <option value="{{ $startYear }}-{{ $endYear }}" {{ old('school_year') == "$startYear-$endYear" ? 'selected' : '' }}>
                            {{ $startYear }}-{{ $endYear }}
                        </option>
                    @endfor
                </select>

                @error('school_year')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Semester --}}
            <div class="mb-4">
                <label for="semester">Semester</label>
                <select name="semester" class="input @error('semester') ring-red-500 @enderror" required>
                    <option value="1st Semester" {{ old('semester') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('semester') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                </select>

                @error('semester')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Profile Photo --}}
            <div class="mb-4">
                <label for="image">Profile Photo</label>
                <input type="file" name="image" id="image" class="input @error('image') ring-red-500 @enderror">

                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button class="btn">Add Instructor</button>

        </form>
    </div>
</x-adminlayout>
