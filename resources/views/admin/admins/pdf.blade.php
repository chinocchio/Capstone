<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 15px;
        }
        .details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h2 class="header">Incident Report</h2>
    <div class="details">
        <p><strong>From:</strong> {{ $report->from_email }}</p>
        <p><strong>To:</strong> {{ $report->to_email }}</p>
        <p><strong>Subject:</strong> {{ $report->subject ?? 'No Subject' }}</p>
        <p><strong>Status:</strong> {{ $report->status }}</p>
        <p><strong>Message:</strong></p>
        <p>{{ $report->message }}</p>

        @if ($report->attachment_path)
            <p><strong>Attachment:</strong></p>
            <p><a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank">View Attachment</a></p>
        @else
            <p><em>No attachment available</em></p>
        @endif
    </div>
</body>
</html>
