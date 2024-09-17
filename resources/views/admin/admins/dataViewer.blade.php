<x-adminlayout>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Data Overview</h1>

        <!-- Student-Subject Data Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Student - Subject Enrollments</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Subject Name</th>
                            <th>Enrolled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentSubjects as $studentSubject)
                        <tr>
                            <td>{{ $studentSubject->id }}</td>
                            <td>{{ $studentSubject->student_name }}</td>
                            <td>{{ $studentSubject->subject_name }}</td>
                            <td>{{ $studentSubject->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MAC-Student Data Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>MAC - Student Linkages</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>MAC ID</th>
                            <th>Linked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($macStudents as $macStudent)
                        <tr>
                            <td>{{ $macStudent->id }}</td>
                            <td>{{ $macStudent->student_name }}</td>
                            <td>{{ $macStudent->mac_id }}</td>
                            <td>{{ $macStudent->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logs Data Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>System Logs</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User (Admin/Instructor)</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th>Day</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user_name ?? 'Admin' }}</td>
                            <td>{{ $log->status }}</td>
                            <td>{{ $log->time }}</td>
                            <td>{{ $log->day }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-adminlayout>