<x-layout>
    <h1 class="title">Attendance</h1>

    <div class="grid grid-cols-2 gap-6">
        <div class="card">
            <img src="{{ asset('storage/posts_images/1024px-QR_Code_Example.svg.png') }}" alt="Qr code here ">

            <ul class="list-disc pl-6 mb-8">
                @foreach($linkedSubjects as $subject)
                    <li class="mb-2 text-lg">
                        {{ $subject->name }} - 
                        {!! DNS2D::getBarcodeHTML("$subject->qr", 'QRCODE') !!}
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card">
            <span class="mb-4">Present Students:</span>
            @forelse($scans as $scan)
                <p>{{ $scan->scanned_by }} | {{ $scan->subject->name }} | {{ $scan->scanned_at->format('h:i a') }}</p>
            @empty
                <p>No scans recorded.</p>
            @endforelse
        </div>
    </div>
</x-layout>