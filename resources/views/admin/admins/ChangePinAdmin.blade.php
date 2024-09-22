<x-adminlayout>
    <div class="card">
        <h2>Change Your PIN</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('admin.changePin') }}" method="POST">
            @csrf
            @method('PUT')
    
            <div class="mb-4">
                <label for="old_pin">Old PIN</label>
                <input type="password" class="input" id="old_pin" name="old_pin" required>
                @error('old_pin')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="new_pin">New PIN</label>
                <input type="password" class="input" id="new_pin" name="new_pin" required>
                @error('new_pin')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
    
            <div class="mb-4">
                <label for="new_pin_confirmation">Confirm New PIN</label>
                <input type="password" class="input" id="new_pin_confirmation" name="new_pin_confirmation" required>
            </div>
    
            <button type="submit" class="btn btn-primary">Change PIN</button>
        </form>
    </div>
</x-adminlayout>

