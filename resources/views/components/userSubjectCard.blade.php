@props(['post', 'full' => false])

<div class="card">
    <div class="flex gap-6">
        {{-- Cover photo --}}
        <div class="h-auto w-1/5 rounded-md overflow-hidden self-start">
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="">
            @else
                <img class="object-cover object-center rounded-md" src="{{ asset('storage/posts_images/default.jpg') }}" alt="">
            @endif
        </div>

        <div class="w-4/5">
            {{-- Title --}}
            <h2 class="font-bold text-xl">{{ $post->name }}</h2>
            {{-- Post Code --}}
            <div class="text-sm mb-4">
                <span class="font-medium">Code:</span> {{ $post->code }}
            </div>

            {{-- Post day --}}
            <div class="text-sm mb-4">
                <span class="font-medium">Every:</span> {{ $post->day }}
            </div>

            {{-- Section --}}
            @if ($post->section)
                <p class="text-sm mb-2"><strong>Section:</strong> {{ $post->section }}</p>
            @endif

            {{-- Time --}}
            @if ($post->start_time && $post->end_time)
                <p class="text-sm mb-2"><strong>Time:</strong> {{ \Carbon\Carbon::parse($post->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($post->end_time)->format('g:i A') }}</p>
            @endif

            {{-- School Year and Semester --}}
            <div class="text-sm mb-2">
                <p class="mb-2"><strong>S.Y:</strong> {{ $post->school_year }}</p>
                <p class="mb-2"><strong>Semester:</strong> {{ $post->semester }}</p>
            </div>

            {{-- Description --}}
            @if ($full)
                {{-- Show full body text in single post page --}}
                <div class="text-sm">
                    <span>{{ $post->description }}</span>
                </div>
            @else
                {{-- Show limited body text in single post page --}}
                <div class="text-sm">
                    <span>{{ Str::words($post->description, 15) }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>
