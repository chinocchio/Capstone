<x-adminlayout>
    {{-- Heading --}}
    <h1 class="title text-2xl mb-6">ADMIN DASHBOARD</h1>

    <div class="grid grid-cols-12 gap-1">
        <div class="card mb-4 p-4 text-sm col-span-2 h-32">
            Temperature display
        </div>
        <div class="card mb-4 p-6 col-span-10">
            <h2 class="font-bold mb-4 text-lg">MAC LABORATORY OCCUPIED BY:</h2>

            <h2 class="font-bold text-xl">Chino Lawrence Noble</h2>
            <h2 class="text-xl mb-4">chnoble@my.cspc.edu.ph</h2>

            <h2 class="text-xl">1pm - 4pm Wednesday</h2>

            <h2 class="text-xl">Mobile Technology 1 </h2>
        </div>
    </div>

    {{-- User Posts --}}
    <h2 class="font-bold mb-4 text-lg">MAC LABORATORY INSTRUCTORS</h2>

    <div class="grid grid-cols-2 gap-6">
        @foreach ($instructors as $instructor)
            {{-- Instructor card component --}}
            <x-userCard :instructor="$instructor">
                <div class="flex items-center justify-end gap-4 mt-6">
                    {{-- Update post --}}
                    <a href="{{ route('users.edit', $instructor) }}"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded-md">Update</a>

                    {{-- Delete post --}}
                    <form action="{{ route('users.destroy' , $instructor->id )}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-2 py-1 text-xs rounded-md">Delete</button>
                    </form>
                </div>
            </x-userCard>
        @endforeach
    </div>

    {{-- Pagination links --}}
    <div>
        {{ $instructors->links() }}
    </div>

</x-adminlayout>
