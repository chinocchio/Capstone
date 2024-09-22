<x-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="title">Students</h1>
    </div>
        {{-- Session Messages --}}
        @if (session('success'))
        <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
        <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @elseif (session('warning'))
        <x-flashMsg msg="{{ session('warning') }}" bg="bg-yellow-500" />
        @endif
        
    <div class="grid grid-cols-2 gap-6">
        @foreach ($posts as $post)
        <div class="relative">
            @if($post->type === 'makeup')
            <span class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs rounded-bl-md">Makeup Class</span>
            @endif
                <x-students :post="$post" />  
            @endforeach
        </div>
    </div>
</x-layout>