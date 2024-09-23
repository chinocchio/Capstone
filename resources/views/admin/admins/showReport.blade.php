<x-adminlayout>
    <div class="container">
        <h1>Incident Report Details</h1>
        <div class="card">
            <!-- Correct path to the logo -->
            <div class="card-header text-center">
                <img src="{{ asset('storage/posts_images/cEODQPn9vLGe6GLqz8b96hb2Qd43ujYb5SLWmAz9.png') }}" 
                     alt="College Logo" style="width: 100px; margin-bottom: 10px;">
                <h3>{{ $report->subject ?? 'No Subject' }}</h3>
            </div>
            <div class="card-body">
                <p><strong>From:</strong> {{ $report->from_email }}</p>
                <p><strong>To:</strong> {{ $report->to_email }}</p>
                <p><strong>Status:</strong> {{ $report->status }}</p>
                <p><strong>Message:</strong></p>
                <p>{{ $report->message }}</p>

                @if ($report->attachment_path)
                    <p><strong>Attachment:</strong></p>
                    <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank">View Attachment</a>
                @else
                    <p><em>No attachment available</em></p>
                @endif
            </div>
            <div class="card-footer">
                <a href="#" onclick="window.print();" class="btn btn-primary">Print View</a>
            </div>
        </div>
    </div>

    <!-- Custom print styles -->
    <style>
        @media print {
            .btn {
                display: none; /* Hide buttons during print */
            }
            .card-footer {
                display: none; /* Hide footer during print */
            }
            .card {
                border: none;
                box-shadow: none;
            }
            img {
                max-width: 100px;
                margin: 10px auto;
                display: block;
            }
        }
    </style>
</x-adminlayout>
