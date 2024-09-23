<x-adminlayout>
    <div class="container">
        <h1 class="mb-4">Incident Reports</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->from_email }}</td>
                        <td>{{ $report->to_email }}</td>
                        <td>{{ $report->subject }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ $report->status }}</span>
                        </td>
                        <td class="d-flex gap-2">
                            <!-- Confirm Button -->
                            @if($report->status === 'Pending')
                            <a href="{{ route('reports.confirm', $report->id) }}" class="btn btn-success btn-sm">
                                Confirm
                            </a>
                            @endif
    
                            <!-- View Button -->
                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-info btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Additional CSS for Enhancements -->
    <style>
        .table th, .table td {
            vertical-align: middle; /* Center content vertically */
        }
    
        .btn {
            transition: all 0.3s ease; /* Smooth transition for hover effects */
        }
    
        .btn-success:hover {
            background-color: #218838; /* Slightly darker green on hover */
        }
    
        .btn-info:hover {
            background-color: #138496; /* Slightly darker blue on hover */
        }
    
        .badge {
            font-size: 0.9em; /* Slightly increase badge size */
            padding: 0.5em 0.75em; /* Add padding to badges */
        }
    </style>
</x-adminlayout>
