<x-layout>
    <h1 class="title">Seat Plan</h1>
        <div class="grid grid-cols-2 gap-6">
            @foreach ($posts as $post)
                <x-students :post="$post" />  
            @endforeach
        </div>
</x-layout>