<!DOCTYPE html>
<html>
<head>
    <title>Scans Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Scans Report</h1>
    <table>
        <thead>
            <tr>
                <th>Scanned By</th>
                <th>Subject Name</th>
                <th>Scanned At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scans as $scan)
                <tr>
                    <td>{{ $scan->scanned_by }}</td>
                    <td>{{ $scan->subject->name }}</td>
                    <td>{{ $scan->scanned_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
