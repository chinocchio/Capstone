@props(['instructor', 'full' => false])

<div class="card">
    <div class="flex gap-6">
        {{-- Cover photo --}}
        <div class="w-4/5">
            {{-- Title --}}
            <h2 class="font-bold text-xl">{{ $instructor->username }}</h2>
            <p class="font-bold text-xl">{{ $instructor->email }}</p>
        </div>

    </div>


    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>