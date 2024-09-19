<table class="table-auto w-full border-collapse border border-gray-300" id="myTable">
    <thead>
        <tr>
            <th class="border border-gray-300 px-4 py-2">Name</th>
            <th class="border border-gray-300 px-4 py-2">Subject</th>
            <th class="border border-gray-300 px-4 py-2">Time In</th>
            <th class="border border-gray-300 px-4 py-2">Time Out</th>
        </tr>
    </thead>
    <tbody>
        @forelse($scans as $scan)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ $scan->scanned_by }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $scan->subject->name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $scan->scanned_at->format('h:i a') }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    @if($scan->verified_at)
                        {{ \Carbon\Carbon::parse($scan->verified_at)->format('h:i a') }}
                    @else
                        Not yet verified
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">No scans recorded.</td>
            </tr>
        @endforelse
    </tbody>
</table>
