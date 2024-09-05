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
            <h1 class="font-bold mb-4 text-lg">({{ $currentDate }})</h1>

            @forelse($subjects as $subject)
                <div class="subject-card mb-4 p-4  rounded-lg">
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

    <script>
        setInterval(function() {
            location.reload(); // Reloads the page every 15 seconds
        }, 15000); // 15 seconds
    </script>

</x-adminlayout>
