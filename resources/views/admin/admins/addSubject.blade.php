<x-adminlayout>
    {{-- Heading --}}
    {{-- Heading --}}
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin_dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        <a href="{{ route('subjects.create') }}" class="btn">Manually Add Subject</a>
    </div>

    <div class="card mb-4">
        {{-- Import Excel Form --}}
        <div class="mt-8">
            <h2 class="font-bold mb-4">Import Subjects from Excel</h2>
            <form action="{{ route('importSubsFromExcel') }}" method="post" enctype="multipart/form-data">
                {{-- {{ route('subjects.import') }} --}}
                @csrf
                <div class="mb-4">
                    <label for="file" class="block mb-2">Select Excel File</label>
                    <input type="file" name="file" id="file" accept=".xls,.xlsx"
                        class="input @error('file') ring-red-500 @enderror">

                    @error('file')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Import</button>
            </form>
        </div>
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
