@props(['post', 'full' => false])

<div class="card p-4 bg-white shadow-lg rounded-lg mb-4">
    <div class="flex gap-6">
        {{-- Cover photo --}}
        <div class="h-auto w-1/5 rounded-md overflow-hidden self-start">
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="object-cover w-full h-full">
            @else
                <img class="object-cover object-center rounded-md w-full h-full" src="{{ asset('storage/posts_images/default.jpg') }}" alt="Default Image">
            @endif
        </div>

        <div class="w-4/5">
            {{-- Post Name --}}
            <h2 class="font-bold text-xl mb-2">{{ $post->name }}</h2>

            {{-- Post Code --}}
            <div class="text-sm mb-2">
                <span class="font-medium">Code:</span> {{ $post->code }}
            </div>

            {{-- Post day --}}
            <div class="text-sm mb-2">
                <span class="font-medium">Every:</span> {{ $post->day }}
            </div>

            {{-- Time and Section --}}
            <div class="text-xs font-light mb-4">
                <span class="font-medium">Time:</span> 
                <span class="font-bold">{{ \Carbon\Carbon::parse($post->start_time)->format('g:i A') }}</span> to 
                <span class="font-bold">{{ \Carbon\Carbon::parse($post->end_time)->format('g:i A') }}</span>
                <br>
                <span class="font-medium">Section:</span> 
                <span class="font-bold">{{ $post->section }}</span>
            </div>
        </div>
    </div>

    <div class="mt-auto flex gap-4 justify-center">
        <form action="{{ route('import.students') }}" method="POST" id="import-form">
            @csrf
            <input type="hidden" name="section" value="{{ $post->section }}"> <!-- Replace with actual section value or logic to determine it -->
            <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-4 py-2 rounded-md shadow-md transform hover:scale-105 transition-transform duration-300 ease-in-out text-sm">
                Import Students
            </button>
        </form>
        <form action="{{ route('check.students') }}" method="GET" id="check-form">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $post->id }}"> <!-- Replace with actual section value or logic to determine it -->
            <button type="submit" class="bg-gradient-to-r from-green-500 to-teal-500 text-white px-4 py-2 rounded-md shadow-md transform hover:scale-105 transition-transform duration-300 ease-in-out text-sm">
                Check Students
            </button>
        </form>
    </div>

    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>
