<x-layout>
    <h1 class="title">Attendance</h1>

    <div class="grid grid-cols-2 gap-6">
        <div class="card">
            {{-- Display the QR codes for subjects that are currently active --}}
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
            <span>List</span>
            <div id="scans-list">
                @include('partials.scans-list', ['scans' => $scans])
            </div>
        </div>
    </div>

    <script>
        setInterval(function() {
            // Fetch the updated scans list via AJAX
            fetchScans();
        }, 5000); // Poll every 5 seconds

        function fetchScans() {
            fetch('{{ route('scans.list') }}')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('scans-list').innerHTML = data;
                })
                .catch(error => console.error('Error fetching scans:', error));
        }
    </script>
</x-layout>
