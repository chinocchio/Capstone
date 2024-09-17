<x-layout>
    <div class="container mx-auto px-4">
        <a href="{{ route('dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        <div class="flex justify-between items-center mb-4">
            <h1 id="myTable" class="text-2xl font-bold">Student List</h1>
        </div>

                {{-- Session Messages --}}
                @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" />
                @elseif (session('delete'))
                <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
                @elseif (session('warning'))
                <x-flashMsg msg="{{ session('warning') }}" bg="bg-yellow-500" />
                @endif
        
        @if($students->isEmpty())
            <p class="text-gray-500">No students enrolled in this subject.</p>
        @else
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Student Number</th>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $student->student_number }}</td>
                            <td class="py-2 px-4 border-b">{{ $student->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $student->email }}</td>
                            <td class="py-2 px-4 border-b">
                            <!-- Unenroll Action -->
                            <form action="{{ route('students.unenroll', ['id' => $student->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Unenroll</button>
                            </form>

                            <!-- Get Finger Action -->
                            @if(is_null($student->biometric_data))
                                <a href="{{ route('biometrics.runApp') }}" class="text-green-500 hover:text-green-700">Get Finger</a>
                                {{-- {{ route('biometrics.getFinger', ['id' => $student->id]) }} --}}
                            @else
                                <span class="text-gray-500 cursor-not-allowed">Get Finger</span>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layout>

