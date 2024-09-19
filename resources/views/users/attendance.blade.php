<x-layout>
    <h1 class="title">Attendance</h1>

    <div class="grid grid-cols-2 gap-6">
        <div class="card">
            {{-- Display the QR codes for subjects that are currently active --}}
            <ul class="list-disc pl-6 mb-8">
                @foreach($linkedSubjects as $subject)
                    <li class="mb-2 text-lg">
                        {{ $subject->name }} - {{ $subject->qr }}
                        {!! DNS2D::getBarcodeHTML("$subject->qr", 'QRCODE') !!}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card">
            <div id="scans-list">
                @include('partials.scans-list', ['scans' => $scans])
            </div>
            <button id="export-btn" class="mt-4 p-2 bg-blue-500 text-white rounded">Export PDF</button>
        </div>
    </div>

    <script>
        setInterval(function() {
            // Fetch the updated scans list via AJAX
            fetchScans();
        }, 1000); // Poll every 1 second

        function fetchScans() {
            fetch('{{ route('scans.list') }}')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('scans-list').innerHTML = data;
                })
                .catch(error => console.error('Error fetching scans:', error));
        }

        document.getElementById('export-btn').addEventListener('click', function() {
            exportScans();
        });

        function exportScans() {
            // Redirect to the export PDF route to trigger the download
            window.location.href = '{{ route('scans.export.excel') }}';
        }
    </script>
</x-layout>
