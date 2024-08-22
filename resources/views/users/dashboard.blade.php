<x-layout>
    {{-- Heading --}}
    <h1 class="title">Welcome {{ auth()->user()->username }}, you have {{ $posts->total() }} subjects</h1>


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