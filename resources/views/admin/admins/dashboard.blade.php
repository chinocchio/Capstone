<x-adminlayout>
    {{-- Heading --}}
    <h1 class="title text-2xl mb-6">ADMIN DASHBOARD</h1>

    <div class="grid grid-cols-12 gap-1">
        <div class="card mb-4 p-4 text-sm col-span-2 h-32">
            @if ($latestTemperature)
                <h2>Temperature: {{ $latestTemperature->temperature }}°C</h2>
                <h2>Humidity: {{ $latestTemperature->humidity }}%</h2>
            @else
                <h2>No temperature data available.</h2>
            @endif
        </div>
        <div class="card mb-4 p-6 col-span-10">
            <div class="flex justify-between items-center">
                <h1 class="font-bold text-lg">({{ $currentDate }})</h1>

                {{-- Clickable Lock Icon --}}
                <span id="lock-status" class="cursor-pointer" title="Door is Locked" onclick="toggleLockStatus()" style="font-size: 1.5rem;">
                    🔒
                </span>
            </div>

            @forelse($subjects as $subject)
                <div class="subject-card mb-4 p-4 rounded-lg">
                    <h2 class="font-bold text-xl">{{ $subject->name }}</h2>
                    <p class="text-md mb-2"><strong>Code:</strong> {{ $subject->code }}</p>
                    <p class="text-md mb-2"><strong>Description:</strong> {{ $subject->description }}</p>
                    <p class="text-md mb-2">
                        <strong>Time:</strong> 
                        {{ \Carbon\Carbon::parse($subject->start_time)->format('g:i a') }} - 
                        {{ \Carbon\Carbon::parse($subject->end_time)->format('g:i a') }}
                    </p>
                    <p class="text-md">
                        <strong>Occupied By:</strong> {{ $subject->username ?? 'No Instructor Assigned' }}
                    </p>
                </div>
            @empty
                <p>No subjects are currently available.</p>
            @endforelse 
        </div>
    </div>

    {{-- User Posts --}}
    <h2 class="font-bold mb-4 text-lg">MAC LABORATORY INSTRUCTORS</h2>

    <div class="grid grid-cols-2 gap-6">
        @foreach ($instructors as $instructor)
            {{-- Instructor card component --}}
            <x-userCard :instructor="$instructor">
                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="{{ route('users.edit', $instructor) }}"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                    {{-- Delete post --}}
                    <form action="{{ route('users.destroy' , $instructor->id )}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-2 py-1 text-xs rounded-md">Delete</button>
                    </form>
                </div>
            </x-userCard>
        @endforeach
    </div>

    {{-- Pagination links --}}
    <div>
        {{ $instructors->links() }}
    </div>

    {{-- Lock Toggle Functionality --}}
    <script>
        function toggleLockStatus() {
            const lockStatus = document.getElementById('lock-status');
            const isLocked = lockStatus.innerText === '🔒';  // Check if locked

            let status = isLocked ? 'unlocked' : 'locked';  // Determine the status to send
            lockStatus.innerText = isLocked ? '🔓' : '🔒';  // Toggle icon
            lockStatus.title = isLocked ? 'Door is Unlocked' : 'Door is Locked';

            // Get the current date and time in Asia/Manila timezone
            let now = new Date();
            let options = { timeZone: 'Asia/Manila', hour12: false, hour: '2-digit', minute: '2-digit' };
            let time = now.toLocaleTimeString('en-US', options);
            let day = now.toLocaleDateString('en-US', { timeZone: 'Asia/Manila', weekday: 'long' });

            // Send the data via AJAX
            sendLockStatus(status, time, day);
            }

            function sendLockStatus(status, time, day) {
                const data = {
                    user_id: null,  // User ID is null as per requirement
                    status: status,
                    time: time,
                    day: day,
                };

                fetch('/api/logs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',  // Include CSRF token for security
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();  // Parse JSON if the response is okay
                })
                .then(data => {
                    console.log('Log entry created successfully:', data);
                })
                .catch(error => {
                    console.error('Error creating log entry:', error);
                });
            }

    </script>

</x-adminlayout>
