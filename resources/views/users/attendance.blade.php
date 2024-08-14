<x-layout>
    <h1 class="title">Attendance</h1>

    {{--Do your thing here--}}
    <div class="grid grid-cols-2 gap-6">
        <div class="card">
            <img src="{{ asset('storage/posts_images/1024px-QR_Code_Example.svg.png') }}" alt="Qr code here ">

            <ul class="list-disc pl-6 mb-8">
                @foreach($linkedSubjects as $subject)
                    <li class="mb-2 text-lg">
                        {{ $subject->name }} - 
                        {!! DNS2D::getBarcodeHTML("$subject->qr", 'QRCODE' )!!}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card">
            <span>List</span>
            <p>Juan Dela Cruz| 10:15 am</p>
            <p>Juan Dela Cruz| 10:15 am</p>
            <p>Juan Dela Cruz| 10:15 am</p>
            <p>Juan Dela Cruz| 10:15 am</p>
        </div>
    </div>
</x-layout>