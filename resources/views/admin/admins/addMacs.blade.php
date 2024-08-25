<x-adminlayout>
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin_dashboard') }}" class="text-xs text-blue-500">&larr; Go back to your dashboard</a>
        <a href="{{ route('subjects.create') }}" class="bg-blue-500 text-white px-2 py-1 text-xs rounded-md">Manually Add Mac</a>
    </div>

    {{-- Session Messages --}}
    @if (session('success'))
        <x-flashMsg msg="{{ session('success') }}" />
    @elseif (session('delete'))
        <x-flashMsg msg="{{ session('delete') }}" bg="bg-red-500" />
    @endif

    <div class="card mb-4">
        {{-- Import Excel Form --}}
        <div class="mt-8">
            <h2 class="font-bold mb-4">Import Macs from Excel</h2>
            <form action="{{ route('importMacsFromExcel') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block mb-2">Select Excel File</label>
                    <input type="file" name="file" id="file" accept=".xls,.xlsx"
                    class="input @error('file') ring-red-500 @enderror">

                    @error('file')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Import</button>
            </form>
        </div>
    </div>

    {{-- Display Added MAC PCs --}}
    <div class="card bg-white shadow rounded-md mt-4 p-4">
        <div class="flex">
            {{-- Table --}}
            <div class="flex-1">
                <div class="mb-4">
                    {{-- Search Filter --}}
                    <form action="{{ route('mac.index') }}" method="GET" class="flex">
                        <input type="text" name="search" value="{{ request()->query('search') }}" placeholder="Search by MAC ID or Number"
                            class="form-input rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 flex-grow">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600">Search</button>
                    </form>
                </div>
                <div class="p-4">
                    <h2 class="font-bold text-lg mb-4">Added MAC PCs</h2>
                    <table id="myTable" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MAC Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($macs as $mac)
                            <tr class="cursor-pointer" data-id="{{ $mac->id }}" data-mac="{{ $mac->mac_number }}" data-qr="{{ $mac->qr }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mac->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mac->mac_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mac->qr }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('mac.edit', $mac->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <form action="{{ route('mac.destroy', $mac->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 ml-4">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Details Section --}}
            <div class="w-1/3 pl-4">
                <div id="details" class="bg-white shadow rounded-md p-4 h-full">
                    <h2 class="font-bold text-lg mb-4">MAC Details</h2>
                    <p><strong>ID:</strong> <span id="detail-id">Select a MAC PC</span></p>
                    <p><strong>MAC Number:</strong> <span id="detail-mac">---</span></p>
                    <p><strong>QR Code:</strong> <span id="detail-qr">---</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination Controls --}}
    <div class="mt-4">
        {{ $macs->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#myTable tbody tr');
            const idEl = document.getElementById('detail-id');
            const macEl = document.getElementById('detail-mac');
            const qrEl = document.getElementById('detail-qr');

            rows.forEach(row => {
                row.addEventListener('click', () => {
                    const id = row.getAttribute('data-id');
                    const mac = row.getAttribute('data-mac');
                    const qr = row.getAttribute('data-qr');

                    idEl.textContent = id;
                    macEl.textContent = mac;
                    qrEl.innerHTML = `{!! DNS2D::getBarcodeHTML('__placeholder__', 'QRCODE') !!}`.replace('__placeholder__', qr); // I WANT THE RQ TO APPEAR IN THIS PART!!
                });
            });
        });
    </script>
</x-adminlayout>
