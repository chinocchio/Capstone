<x-adminlayout>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Laboratory Logbook</h1>

        <!-- Attendance Data Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Attendance Records</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>PC #</th>
                            <th>Student ID #</th>
                            <th>Year Course & Section</th>
                            <th>Instructor</th>
                            <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentAttendance as $attendance)
                        <tr>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->student_name }}</td>
                            <td>{{ $attendance->pc_number ?? 'N/A' }}</td>
                            <td>{{ $attendance->student_number }}</td>
                            <td>{{ $attendance->year_course }}</td>
                            <td>{{ $attendance->instructor_name }}</td>
                            <td>{{ $attendance->time_in }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('export.logs') }}" class="btn btn-success">Export Excel</a>
    </div>

</x-adminlayout>
