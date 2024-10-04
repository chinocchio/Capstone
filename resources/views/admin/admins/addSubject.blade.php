<x-adminlayout>
    {{-- Heading --}}
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin_dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        {{-- <a href="{{ route('subjects.create') }}" class="bg-blue-500 text-white px-2 py-1 text-xs rounded-md">Manually Add Subject</a> --}}
    </div>

    @if (session('duplicate_subjects'))
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
                @foreach (session('duplicate_subjects') as $duplicate)
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

    <div class="card mb-4">
        {{-- Import Excel Form --}}
        <div class="mt-8">
            <h2 class="font-bold mb-4">Import Schedules using Excel</h2>
            <form action="{{ route('importSubsFromExcel') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block mb-2">Select Excel File</label>
                    <input type="file" name="file" id="file" accept=".xls,.xlsx" class="input @error('file') ring-red-500 @enderror">
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

    {{-- Session Messages --}}
    @if (session('success'))
        <x-flashMsg msg="{{ session('success') }}" />
    @elseif (session('delete'))
        <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
    @endif

    {{-- Current Semester and Year --}}
    <h2 class="font-bold mb-4">Subjects for {{ $currentSemester }} - {{ $currentYear }}</h2>

    {{-- Search and Day Filter --}}
    <div class="mb-4">
        <form action="{{ route('subjects.index') }}" method="GET" class="flex items-center gap-4">
            <input type="text" name="search" value="{{ request()->query('search') }}" placeholder="Enter Course Code or Course Name"
                class="input rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 flex-grow">

            {{-- Day Filter Dropdown --}}
            <select name="day" id="day" class="input">
                <option value="">All Days</option>
                <option value="Monday" {{ request('day') == 'Monday' ? 'selected' : '' }}>Monday</option>
                <option value="Tuesday" {{ request('day') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                <option value="Thursday" {{ request('day') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                <option value="Friday" {{ request('day') == 'Friday' ? 'selected' : '' }}>Friday</option>
                <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filter</button>
        </form>
    </div>

    {{-- Table displaying subjects --}}
    <form action="{{ route('subjects.deleteAll') }}" method="post" id="deleteAllForm">
        @csrf
        @method('DELETE')

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center">
                <input type="checkbox" id="selectAll" class="mr-2">
                <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                <span id="selectedCount" class="ml-2 text-sm text-gray-500">(0 selected)</span>
            </div>
            <button type="submit" id="deleteSelectedBtn" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 disabled:opacity-50" disabled>Delete Selected</button>
        </div>

        {{-- Table displaying subjects --}}
        <table class="min-w-full bg-white border border-gray-300 rounded-md">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 border">
                        <input type="checkbox" id="selectAllCheckbox" class="mr-2">
                    </th>
                    <th class="px-4 py-2 border">Code</th>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Day</th>
                    <th class="px-4 py-2 border">Section</th>
                    <th class="px-4 py-2 border">Start Time</th>
                    <th class="px-4 py-2 border">End Time</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                    <tr>
                        <td class="px-4 py-2 border">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="subject-checkbox">
                        </td>
                        <td class="px-4 py-2 border">{{ $subject->code }}</td>
                        <td class="px-4 py-2 border">{{ $subject->name }}</td>
                        <td class="px-4 py-2 border">{{ $subject->day }}</td>
                        <td class="px-4 py-2 border">{{ $subject->section }}</td>
                        <td class="px-4 py-2 border">{{ $subject->start_time }}</td>
                        <td class="px-4 py-2 border">{{ $subject->end_time }}</td>
                        <td class="px-4 py-2 border">
                            <div class="flex space-x-2">
                                {{-- Update --}}
                                {{-- <a href="{{ route('subjects.edit', $subject->id) }}" class="bg-green-500 text-white px-3 py-1 text-xs rounded-md">Update</a> --}}
                                {{-- Set Makeup Class --}}
                                <a href="{{ route('makeupClass', $subject->id) }}" class="bg-purple-500 text-white px-3 py-1 text-xs rounded-md">Set Makeup</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </form>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $subjects->links() }}
    </div>

    <script>
        // JavaScript to handle "Select All" functionality and dynamic updates
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.subject-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.subject-checkbox:checked').length;
                selectedCount.textContent = `(${selected} selected)`;
                deleteSelectedBtn.disabled = selected === 0;
            }

            selectAllCheckbox.addEventListener('click', function(event) {
                checkboxes.forEach(checkbox => checkbox.checked = event.target.checked);
                updateSelectedCount();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('click', updateSelectedCount);
            });
        });
    </script>
</x-adminlayout>
