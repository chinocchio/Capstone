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
                        {{-- Add hidden input for subject_id --}}
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            {{-- Form for saving attendance --}}
            <form method="POST" action="{{ route('attendance.save') }}">
                @csrf

                {{-- Hidden input for subject_id (assuming only one subject is active) --}}
                <input type="hidden" name="subject_id" value="{{ $linkedSubjects->first()->id ?? '' }}">

                <div id="scans-list">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID</th>
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
                                    <td class="border px-4 py-2">{{ $student->id }}</td> {{-- New ID column --}}
                                    <td class="border px-4 py-2">{{ $student->name }}</td>
                                    <td class="border px-4 py-2">
                                        {{ $studentScan ? \Carbon\Carbon::parse($studentScan->scanned_at)->format('h:i A') : '-' }}
                                        <input type="hidden" name="students[{{ $student->id }}][time_in]" value="{{ $studentScan ? $studentScan->scanned_at : null }}">
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $studentScan && $studentScan->verified_at ? \Carbon\Carbon::parse($studentScan->verified_at)->format('h:i A') : '-' }}
                                        <input type="hidden" name="students[{{ $student->id }}][time_out]" value="{{ $studentScan && $studentScan->verified_at ? $studentScan->verified_at : null }}">
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $studentScan && $studentScan->verified_at ? 'Present' : 'Absent' }}
                                        <input type="hidden" name="students[{{ $student->id }}][status]" value="{{ $studentScan && $studentScan->verified_at ? 'Present' : 'Absent' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="mt-4 p-2 bg-green-500 text-white rounded">Save Attendance</button>
            </form>

            {{-- <button id="export-btn" class="mt-4 p-2 bg-blue-500 text-white rounded">Export PDF</button> --}}

                        {{-- New Export Button --}}
                        @if($linkedSubjects->isNotEmpty())
                        <button class="mt-4 p-2 bg-blue-500 text-white rounded"
                            onclick="window.location.href='{{ route('attendance.export.excel', ['subjectId' => $linkedSubjects->first()->id ?? '']) }}'">
                            Export Excel
                        </button>
                    @else
                        <button class="mt-4 p-2 bg-gray-400 text-white rounded" disabled>
                            Export Excel
                        </button>
                    @endif
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
