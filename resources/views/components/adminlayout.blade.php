<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <title>LockUp</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <header class="bg-slate-800 shadow-lg">
        <nav>
            <a href="{{ route('subjects.calendar') }}" class="nav-link">
                <img src="{{ asset('storage/posts_images/1jeEWgOagO3eBPcjAJT4eDAgunLMKawi9kwGlYaN.png') }}"
                alt="LockUp Logo"
                class="h-1/auto w-1/2">
            </a>

            {{-- ADMIN --}}
            @auth('admin')
                <div class="relative grid place-items-center" x-data="{ open: false }">

                    {{-- Dropdown menu button --}}
                    <button @click="open = !open" type="button" class="round-btn">
                        <img src="https://picsum.photos/200" alt="">
                    </button>

                    {{-- Dropdown menu--}}
                    <div x-show="open" @click.outside="open =false" 
                    class="bg-white shadow-lg absolute top-10 right-0 rounded-lg overflow-hidden font-light">

                        <p class="username">{{ auth('admin')->user()->username }}</p>

                        <a href="{{ route('admin_dashboard') }}" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">Dashboard</a>
                        <a href="{{ route('subjects.index') }}" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">Subject</a>
                        <a href="{{ route('mac.index') }}" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">MAC PCs</a>
                        <a href="{{ route('student_view') }}" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">Students</a>
                        <a href="#" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">Reports</a>
                        <a href="#" class="block hover:bg-slate-100 pl-4 pr-8 py-2 mb-1">Door Lock</a>
                        

                        <form action="{{ route('admin_logout') }}" method="post">
                            @csrf
                            <button class="block w-full text-left hover:bg-slate-100 pl-4 pr-8 py-2">Logout</button>
                        </form>

                    </div>
                </div>
            @endauth
        </nav>
    </header>

    <main class="py-8 px-4 mx-auto max-w-screen-lg">
        {{ $slot }}
    </main>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('myTable');
    </script>


    {{--for mac qr--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/daygrid.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid.min.js"></script>

</body>
</html>