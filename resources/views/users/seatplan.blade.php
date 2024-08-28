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
            <x-students :post="$post" />  
        @endforeach
    </div>
</x-layout>