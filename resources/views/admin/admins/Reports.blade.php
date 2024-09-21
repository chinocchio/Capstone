<x-adminlayout>
    <div class="container">
        <h1>Incident Reports</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->from_email }}</td>
                        <td>{{ $report->to_email }}</td>
                        <td>{{ $report->subject }}</td>
                        <td>{{ $report->status }}</td>
                        <td>
                            @if($report->status === 'Pending')
                                <a href="{{ route('reports.confirm', $report->id) }}" class="btn btn-success">Confirm</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-adminlayout>
