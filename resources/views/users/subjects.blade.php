<x-layout>
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">Your Subjects</h1>
        
        <ul class="list-disc pl-6 mb-8">
            @foreach($linkedSubjects as $subject)
                <li class="mb-2 text-lg">
                    {{ $subject->name }} - 
                    <span class="text-gray-600">{{ $subject->code }}</span> - 
                    <span class="font-bold">{{ $subject->start_time->format('g:i A') }}</span> to 
                    <span class="font-bold">{{ $subject->end_time->format('g:i A') }}</span> - 
                    <span class="font-bold">Section: {{ $subject->section }}</span>- 
                    <span class="font-bold">Every: {{ $subject->day }}</span>- 
                    <span class="font-bold">S.Y: {{ $subject->school_year }}</span>- 
                    <span class="font-bold">Semester: {{ $subject->semester }}</span>
                </li>
            @endforeach
        </ul>

        <div class="border-t pt-6">
            <h2 class="text-2xl font-semibold mb-4">Link New Subject</h2>
            <form action="{{ route('user.linkSubject') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Select Subject:</label>
                    <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-lg py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($availableSubjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->name }} - 
                                {{ $subject->code }} - 
                                {{ $subject->start_time->format('g:i A') }} to 
                                {{ $subject->end_time->format('g:i A') }} - 
                                <strong>Section: {{ $subject->section }}</strong> - 
                                {{ $subject->day }} - 
                                {{ $subject->school_year }} - 
                                {{ $subject->semester }} - 
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-1 px-3 rounded-lg hover:bg-blue-600 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Link Subject
                </button>
            </form>
        </div>

        <div class="border-t pt-6 mt-6">
            <h2 class="text-2xl font-semibold mb-4">Unlink Subject</h2>
            <form action="{{ route('user.unlinkSubject') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Select Subject:</label>
                    <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-lg py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($linkedSubjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->name }} - 
                                {{ $subject->code }} - 
                                {{ $subject->start_time->format('g:i A') }} to 
                                {{ $subject->end_time->format('g:i A') }} - 
                                <strong>Section: {{ $subject->section }}</strong> - 
                                {{ $subject->day }} - 
                                {{ $subject->school_year }} - 
                                {{ $subject->semester }} - 
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-lg hover:bg-red-600 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Unlink Subject
                </button>
            </form>
        </div>
    </div>
</x-layout>
