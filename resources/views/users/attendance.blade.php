<x-layout>
    <h1 class="title">Attendance for {{ $currentDate }}</h1>

    <div class="grid grid-cols-2 gap-6">
        <div class="card">
            {{-- Display the QR codes for subjects that are currently active --}}
            @if ($linkedSubjects->isEmpty())
                <p>No subjects are currently ongoing.</p>
            @else
                <ul class="list-disc pl-6 mb-8">
                    @foreach($linkedSubjects as $subject)
                        <li class="mb-2 text-lg">
                            {{ $subject->name }} - {{ $subject->qr }}
                            {!! DNS2D::getBarcodeHTML("$subject->qr", 'QRCODE') !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <div id="scans-list">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Time In</th>
                            <th class="px-4 py-2">Time Out</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php
                                // Check if the student has a scan record
                                $studentScan = $scans->where('scanned_by', $student->name)->first();
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $student->name }}</td>
                                <td class="border px-4 py-2">
                                    {{ $studentScan ? \Carbon\Carbon::parse($studentScan->scanned_at)->format('h:i A') : '-' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $studentScan && $studentScan->verified_at ? \Carbon\Carbon::parse($studentScan->verified_at)->format('h:i A') : '-' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $studentScan && $studentScan->verified_at ? 'Present' : 'Absent' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button id="export-btn" class="mt-4 p-2 bg-blue-500 text-white rounded">Export PDF</button>
        </div>
    </div>

    <script>
        document.getElementById('export-btn').addEventListener('click', function() {
            exportScans();
        });

        function exportScans() {
            // Redirect to the export PDF route to trigger the download
            window.location.href = '{{ route('scans.export.excel') }}';
        }
    </script>
</x-layout>
