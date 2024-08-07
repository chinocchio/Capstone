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
                <input type="text" id="id" name="id" value="{{ $instructor->id }}"
                    class="input w-full @error('id') ring-red-500 @enderror" readonly>
    
                @error('id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username and Email Fields in a Flex Container --}}
            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <label for="username" class="block">Username</label>
                    <input type="text" id="username" name="username" value="{{ $instructor->username }}"
                        class="input w-full @error('username') ring-red-500 @enderror">
    
                    @error('username')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="email" class="block">Email</label>
                    <input type="text" id="email" name="email" value="{{ $instructor->email }}"
                        class="input w-full @error('email') ring-red-500 @enderror">
    
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Post Body --}}
            <div class="mb-4">
                <h2 class="font-bold mb-4">Linked Subjects</h2>
                    <ul class="list-disc pl-6 mb-8">
                        @foreach($linkedSubjects as $subject)
                            <li class="mb-2 text-lg">{{ $subject->name }} - <span class="text-gray-600">{{ $subject->code }}</span></li>
                        @endforeach
                    </ul>
            {{-- Submit Button --}}
            <button type="submit" class="btn">Update</button>
        </form>
    </div>
</x-adminlayout>
