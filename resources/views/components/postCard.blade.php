@props(['post', 'full' => false])

<div class="card" style="display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #fff;">
    {{-- Cover photo --}}
    <div style="display: flex; justify-content: center; width: 100%;">
        @if ($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" style="max-width: 100%; height: auto; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        @else
            <img src="{{ asset('storage/posts_images/default.jpg') }}" alt="Default Image" style="max-width: 100%; height: auto; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        @endif
    </div>

    <div style="width: 100%; text-align: left;">
        {{-- Post Name --}}
        <h2 style="font-weight: bold; font-size: 1.25rem; text-align: center;">{{ $post->title }}</h2>

        {{-- Description --}}
        @if ($full)
            {{-- Show full description text in single post page --}}
            <div style="font-size: 0.875rem; line-height: 1.6; margin-top: 8px;">
                <span>{!! nl2br(e($post->body)) !!}</span>
            </div>
        @else
            {{-- Show limited description text in single post page --}}
            <div style="font-size: 0.875rem; line-height: 1.6; margin-top: 8px;">
                <span>{!! nl2br(e(Str::words($post->body, 15))) !!}</span>
                <a href="{{ route('posts.show', $post) }}" style="color: #3b82f6; margin-left: 0.5rem;">Read more &rarr;</a>
            </div>
        @endif
    </div>

    {{-- Placeholder for extra elements used in user dashboard --}}
    <div>
        {{ $slot }}
    </div>
</div>
