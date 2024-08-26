<x-layout>
    {{-- Heading --}}
    <h1 class="title">Welcome {{ auth()->user()->username }}, you have {{ $posts->total() }} subjects</h1>

    <div class="grid grid-cols-12 gap-1">
        <div class="card mb-4 p-4 text-sm col-span-2 h-32">
            Temperature display
        </div>
        <div class="card mb-4 p-6 col-span-10">
            {{-- <h2 class="font-bold mb-4 text-lg">MAC LABORATORY OCCUPIED BY:</h2>

            <h2 class="font-bold text-xl">Chino Lawrence Noble</h2>
            <h2 class="text-xl mb-4">chnoble@my.cspc.edu.ph</h2>

            <h2 class="text-xl">1pm - 4pm Wednesday</h2>

            <h2 class="text-xl">Mobile Technology 1 </h2> --}}

            <h1 class="font-bold mb-4 text-lg">({{ $currentDate }})</h1>

            @forelse($subjects as $subject)
                <div class="subject-card mb-4 p-4  rounded-lg">
                    <h2 class="font-bold text-xl">{{ $subject->name }}</h2>
                    <p class="text-md mb-2"><strong>Code:</strong> {{ $subject->code }}</p>
                    <p class="text-md mb-2"><strong>Day:</strong> {{ $subject->day }}</p>
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

        {{-- <h1>Subjects for Today ({{ $currentDate }})</h1>
        @if(count($subjects) > 0)
            <table class="table-auto">
                <thead>
                    <tr>
                        <th class="border-b px-4 py-2">Subject Name</th>
                        <th class="border-b px-4 py-2">Code</th>
                        <th class="border-b px-4 py-2">Description</th>
                        <th class="border-b px-4 py-2">Time</th>
                        <th class="border-b px-4 py-2">Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        <tr>
                            <td class="border-b px-4 py-2">{{ $subject->name }}</td>
                            <td class="border-b px-4 py-2">{{ $subject->code }}</td>
                            <td class="border-b px-4 py-2">{{ $subject->description }}</td>
                            <td class="border-b px-4 py-2">
                                {{ \Carbon\Carbon::parse($subject->start_time)->format('g:i a') }} - {{ \Carbon\Carbon::parse($subject->end_time)->format('g:i a') }}
                            </td>
                            <td class="border-b px-4 py-2">{{ $subject->username ?? 'No Instructor Assigned' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No subjects are currently available.</p>
        @endif --}}
        </div>
    </div>

    {{-- User Posts --}}
    <h2 class="font-bold mb-4">Your Subjects</h2>


    <div class="grid grid-cols-2 gap-6">
        @foreach ($posts as $post)
            {{-- Post card component --}}
            <x-postCard :post="$post">

            </x-postCard>
        @endforeach
    </div>

    {{-- Pagination links --}}
    <div>
        {{ $posts->links() }}
    </div>

</x-layout>