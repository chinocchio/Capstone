<x-adminlayout>
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
            <form action="{{ route('importUsersFromExcel') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block mb-2">Select Excel File</label>
                    <input type="file" name="file" id="file" accept=".xls,.xlsx" class="input @error('file') ring-red-500 @enderror">
                    @error('file')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            
                <div class="mb-4">
                    <label for="school_year" class="block mb-2">Select School Year</label>
                    <select name="school_year" id="school_year" class="input">
                        @php
                            $currentYear = now()->year;
                            $yearsInFuture = 5;
                        @endphp
                        @for ($i = 0; $i <= $yearsInFuture; $i++)
                            @php
                                $startYear = $currentYear + $i;
                                $endYear = $startYear + 1;
                            @endphp
                            <option value="{{ $startYear }}-{{ $endYear }}">{{ $startYear }}-{{ $endYear }}</option>
                        @endfor
                    </select>
                </div>
            
                <div class="mb-4">
                    <label for="semester" class="block mb-2">Select Semester</label>
                    <select name="semester" id="semester" class="input">
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>
            
                <button type="submit" class="btn">Import</button>
            </form>
                        
        </div>
    </div>

    {{-- Search and Filter Form --}}
    <form action="{{ route('user.show') }}" method="GET" class="flex space-x-4 mb-4">
        {{-- Search Bar --}}
        <input type="text" name="search" value="{{ request()->query('search') }}" placeholder="Search by Name, Email or Instructor Number"
            class="input flex-grow border border-gray-300 rounded-md">

        {{-- School Year Filter --}}
        <select name="school_year" id="school_year" class="input border border-gray-300 rounded-md">
            <option value="">All School Years</option>
            @php
                $currentYear = now()->year;
                $yearsInFuture = 5;
            @endphp
            @for ($i = 0; $i <= $yearsInFuture; $i++)
                @php
                    $startYear = $currentYear + $i;
                    $endYear = $startYear + 1;
                @endphp
                <option value="{{ $startYear }}-{{ $endYear }}" {{ request()->query('school_year') == "$startYear-$endYear" ? 'selected' : '' }}>
                    {{ $startYear }}-{{ $endYear }}
                </option>
            @endfor
        </select>

        {{-- Semester Filter --}}
        <select name="semester" id="semester" class="input border border-gray-300 rounded-md">
            <option value="">All Semesters</option>
            <option value="1st Semester" {{ request()->query('semester') == "1st Semester" ? 'selected' : '' }}>1st Semester</option>
            <option value="2nd Semester" {{ request()->query('semester') == "2nd Semester" ? 'selected' : '' }}>2nd Semester</option>
        </select>

        {{-- Search Button --}}
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Search</button>
    </form>

    <form action="{{ route('instructors.deleteSelected') }}" method="POST" id="deleteSelectedForm">
        @csrf
        @method('DELETE')
        {{-- User List Table --}}
        <table class="min-w-full bg-white border border-gray-300 rounded-md">
            <thead class="bg-gray-300">
                <tr>
                    <th class="px-4 py-2 border">
                        <input type="checkbox" id="selectAll"> Select All
                    </th>
                    <th class="px-4 py-2 border">Instructor Number</th>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">School Year</th>
                    <th class="px-4 py-2 border">Semester</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-2 border">
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}">
                        </td>
                        <td class="px-4 py-2 border">{{ $user->instructor_number }}</td>
                        <td class="px-4 py-2 border">{{ $user->username }}</td>
                        <td class="px-4 py-2 border">{{ $user->email }}</td>
                        <td class="px-4 py-2 border">{{ $user->school_year }}</td>
                        <td class="px-4 py-2 border">{{ $user->semester }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500">Edit</a> |
                            <a href="{{ route('users.destroy', $user->id) }}" class="text-red-500" onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('delete-user-{{ $user->id }}').submit(); }">Delete</a>
                            <form id="delete-user-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-2 border text-center">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    
        {{-- Bulk Delete Button --}}
        <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 disabled:opacity-50" disabled id="deleteSelectedBtn">
            Delete Selected
        </button>
    </form>
    
    

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>

    {{-- JavaScript for Bulk Selection --}}
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateSelectedCount();
        });

        document.querySelectorAll('input[name="selected_users[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('input[name="selected_users[]"]:checked').length;
            document.getElementById('deleteSelectedBtn').disabled = selectedCount === 0;
        }
    </script>
</x-adminlayout>
