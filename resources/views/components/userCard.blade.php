@props(['instructor', 'full' => false])

<div class="card">
    <div class="flex items-center gap-6">
        {{-- Title and email --}}
        <div class="flex-1">
            <h2 class="font-bold text-xl">{{ $instructor->username }}</h2>
            <p class="font-bold text-xl">{{ $instructor->email }}</p>
        </div>

        {{-- User Image --}}
        <div class="flex-shrink-0">
            <img src="{{ asset('storage/posts_images/user.jpg') }}" alt="{{ $instructor->username }}" class="w-16 h-16 object-cover rounded-full">
        </div>
    </div>

    {{-- Placeholder for extra elements used in user dashboard --}}
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>

<style>
    .card {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
