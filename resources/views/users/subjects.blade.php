<x-layout>
    <h1>Add Subject</h1>
    <form method="POST" action="{{ route('users.addSubject', $user->id) }}">
        @csrf
        <div>
            <label for="subject_id">Select Subject:</label>
            <select name="subject_id" id="subject_id">
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Add Subject</button>
    </form>
</x-layout>