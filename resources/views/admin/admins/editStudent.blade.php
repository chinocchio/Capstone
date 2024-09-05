<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('student_view') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Edit Student</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Edit Student Form --}}
        <form action="{{ route('students.update', $student->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Student Number --}}
            <div class="mb-4">
                <label for="student_number">Student Number</label>
                <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}"
                    class="input @error('student_number') ring-red-500 @enderror" placeholder="Enter the student number" required>

                @error('student_number')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}"
                    class="input @error('name') ring-red-500 @enderror" placeholder="Enter the student's name" required>

                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ old('email', $student->email) }}"
                    class="input @error('email') ring-red-500 @enderror" placeholder="Enter the student's email" required>

                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section --}}
            <div class="mb-4">
                <label for="section">Section</label>
                <input type="text" name="section" value="{{ old('section', $student->section) }}"
                    class="input @error('section') ring-red-500 @enderror" placeholder="Enter the section" required>

                @error('section')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Biometric Data --}}
            <div class="mb-4">
                <label for="biometric_data">Biometric Data (optional)</label>
                <input type="file" name="biometric_data" id="biometric_data" class="input @error('biometric_data') ring-red-500 @enderror">

                @error('biometric_data')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button class="btn">Update Student</button>

        </form>
    </div>
</x-adminlayout>

