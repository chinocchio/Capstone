@forelse($scans as $scan)
    <p>{{ $scan->scanned_by }} | {{ $scan->subject->name }} | {{ $scan->scanned_at->format('h:i a') }}</p>
@empty
    <p>No scans recorded.</p>
@endforelse
