<x-adminlayout>
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin_dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        <a href="{{ route('subjects.create') }}" class="bg-blue-500 text-white px-2 py-1 text-xs rounded-md">Manually Add Mac</a>
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
            <h2 class="font-bold mb-4">Import Students from Excel</h2>
            <form action="{{ route('importStudentsFromExcel') }}" method="post" enctype="multipart/form-data">
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

    
</x-adminlayout>
