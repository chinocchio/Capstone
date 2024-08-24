<x-adminlayout>
    {{-- Heading --}}
    {{-- Heading --}}
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin_dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        <a href="{{ route('subjects.create') }}" class="bg-blue-500 text-white px-2 py-1 text-xs rounded-md">Manually Add Subject</a>
    </div>
        
        {{-- Session Messages --}}
        @if (session('success'))
        <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

    <div class="card mb-4">
        {{-- Import Excel Form --}}
        <div class="mt-8">
            <h2 class="font-bold mb-4">Import Schedules using Excel</h2>
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
    <h2 class="font-bold mb-4">Mac Laboratory Schedules</h2>
            {{-- Search Filter --}}
            <div class="mb-4">
                <form action="{{ route('subjects.index') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request()->query('search') }}" placeholder=" Enter Subject Code or Subject Name"
                        class="input rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 flex-grow">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Search</button>
                </form>
            </div>

    <div class="grid grid-cols-2 gap-6">
        @foreach ($subjects as $subject)
            {{-- Subject card component --}}
            <x-subjectCard :subject="$subject">

                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="{{ route('subjects.edit', $subject->id) }}" class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                    {{-- Delete post --}}
                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-2 py-1 text-xs rounded-md">Delete</button>
                    </form>
                </div>
            </x-subjectCard>
        @endforeach
    </div>

    <div>
        {{ $subjects->links() }}
    </div>
</x-adminlayout>
