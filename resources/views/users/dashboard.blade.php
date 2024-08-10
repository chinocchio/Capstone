<x-layout>
    {{-- Heading --}}
    <h1 class="title">Welcome {{ auth()->user()->username }}, you have {{ $posts->total() }} subjects</h1>


    {{-- User Posts --}}
    <h2 class="font-bold mb-4">Your Subjects</h2>


    <div class="grid grid-cols-2 gap-6">
        @foreach ($posts as $post)
            {{-- Post card component --}}
            <x-postCard :post="$post">

                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="{{ route('posts.edit', $post) }}"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                    {{-- Delete post --}}
                    <form action="{{ route('posts.destroy', $post) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-2 py-1 text-xs rounded-md">Delete</button>
                    </form>
                </div>
            </x-postCard>
        @endforeach
    </div>

    {{-- Pagination links --}}
    <div>
        {{ $posts->links() }}
    </div>

</x-layout>