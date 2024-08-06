@props(['subject', 'full' => false])

<div class="card">
    <div class="flex gap-6">
        {{-- Cover photo --}}
        <div class="h-auto w-1/5 rounded-md overflow-hidden self-start">
            @if ($subject->image)
                <img src="{{ asset('storage/' . $subject->image) }}" alt="">
            @else
                <img class="object-cover object-center rounded-md" src="{{ asset('storage/posts_images/default.jpg') }}" alt="">
            @endif
        </div>

        <div class="w-4/5">
            {{-- Title --}}
            <h2 class="font-bold text-xl mb-5" >{{ $subject->name }}</h2>

            {{-- Body --}}
            @if ($full)
                {{-- Show full body text in single post page --}}
                <div class="text-sm">
                    <span>{{ $subject->description }}</span>
                </div>
            @else
                {{-- Show limited body text in single post page --}}
                <div class="text-sm">
                    <span>{{ Str::words($subject->description, 15) }}</span>
                    <a href="#" class="text-blue-500 ml-2">Read more &rarr;</a>
                </div>
            @endif
        </div>

    </div>


    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>