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

                {{-- Dropdown for selecting school_year and semester --}}
                <div class="mb-4">
                    <label for="school_year" class="block mb-2">Select School Year</label>
                    <select name="school_year" id="school_year" class="input">
                        @php
                            $currentYear = now()->year;  // Or use Carbon\Carbon::now()->year
                            $yearsInFuture = 5; // Set how many years ahead you want to show
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

    {{-- User Posts --}}
    <h2 class="font-bold mb-4">Mac Laboratory Schedules</h2>

    {{-- Search Filter --}}
    <div class="mb-4">
        <form action="{{ route('subjects.index') }}" method="GET" class="flex">
            <input type="text" name="search" value="{{ request()->query('search') }}" placeholder="Enter Subject Code or Subject Name"
                class="input rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 flex-grow">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Search</button>
        </form>
    </div>

    {{-- Delete All Button and Subject List --}}
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

        <div class="grid grid-cols-2 gap-6">
            @foreach ($subjects as $subject)
                {{-- Subject card component --}}
                <label for="subject-checkbox-{{ $subject->id }}" class="cursor-pointer relative">
                    <x-subjectCard :subject="$subject">
                        {{-- Display "Makeup Class" label if the subject type is 'makeup' --}}
                        @if($subject->type === 'makeup')
                            <span class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl-md">Makeup Class</span>
                        @endif
                        <div class="flex items-center justify-between mt-6">
                            <input type="checkbox" id="subject-checkbox-{{ $subject->id }}" name="subject_ids[]" value="{{ $subject->id }}" class="subject-checkbox mr-2">
                            <div class="flex items-center justify-end gap-4">
                                {{-- Update post --}}
                                <a href="{{ route('subjects.edit', $subject->id) }}" class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                                {{-- Makeup Class button --}}
                                <a href="{{ route('makeupClass', $subject->id) }}" class="bg-purple-500 text-white px-2 py-1 text-xs rounded-md">Set Makeup Class</a>
                            </div>
                        </div>
                    </x-subjectCard>
                </label>
            @endforeach
        </div>
    </form>

    <div>
        {{ $subjects->links() }}
    </div>

    <script>
        // JavaScript to handle "Select All" functionality and dynamic updates
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
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
