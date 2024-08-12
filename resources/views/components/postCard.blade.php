@props(['post', 'full' => false])

<div class="card">
    <div class="flex gap-6">
        {{-- Cover photo --}}
        <div class="h-auto w-1/5 rounded-md overflow-hidden self-start">
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image">
            @else
                <img class="object-cover object-center rounded-md" src="{{ asset('storage/posts_images/default.jpg') }}" alt="Default Image">
            @endif
        </div>

        <div class="w-4/5">
            {{-- Post Name --}}
            <h2 class="font-bold text-xl">{{ $post->name }}</h2>

            {{-- Post Code --}}
            <div class="text-sm mb-4">
                <span class="font-medium">Code:</span> {{ $post->code }}
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

            {{-- Description --}}
            @if ($full)
                {{-- Show full description text in single post page --}}
                <div class="text-sm">
                    <span>{{ $post->description }}</span>
                </div>
            @else
                {{-- Show limited description text in single post page --}}
                <div class="text-sm">
                    <span>{{ Str::words($post->description, 15) }}</span>
                    <a href="{{ route('posts.show', $post) }}" class="text-blue-500 ml-2">Read more &rarr;</a>
                </div>
            @endif

            {{-- Linked Users --}}
            <div class="text-xs font-light mt-4">
                <span class="font-medium">Intructor In Charge:</span>
                <ul class="list-disc pl-4">
                    @forelse ($post->users as $user)
                        <li class="font-bold">{{ $user->username }}</li>
                    @empty
                        <li class="text-gray-500">No Instructors Available</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>
