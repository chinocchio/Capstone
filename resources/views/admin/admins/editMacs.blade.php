<x-adminlayout>
    {{-- Heading --}}
    <a href="{{ route('mac.index') }}" class="block mb-2 text-xs text-blue-500">&larr; Go back</a>
    <div class="card mb-4">
        <h2 class="font-bold mb-4">Edit Mac</h2>

        {{-- Session Messages --}}
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" />
        @elseif (session('delete'))
            <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
        @endif

        {{-- Create Post Form --}}
        <form action="{{ route('mac.update', $mac->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Display Students Table --}}
            <div class="mb-4">
                <h3 class="font-semibold mb-2">Students Linked to This Mac</h3>
                <table id="myTable" class="w-full">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Section</th>
                            <th class="border px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mac->students as $student)
                            <tr>
                                <td class="border px-4 py-2">{{ $student->name }}</td>
                                <td class="border px-4 py-2">{{ $student->section }}</td>
                                <td class="border px-4 py-2">
                                    <form action="{{ route('mac.update', $mac->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="unlink_student" value="{{ $student->id }}">
                                        <button type="submit" class="btn btn-danger">Unlink</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</x-adminlayout>
