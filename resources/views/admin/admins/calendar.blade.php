<x-adminlayout>
    <div class="container">
        <h1 class="title">All Scheduled Subjects for the Week</h1>
        <div id="calendar"></div>
    </div>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.11.0/main.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.11.0/main.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.11.0/main.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.11.0/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var schoolYear = @json($schoolYear); // Get the school year from the settings

            var startYear = schoolYear.split('-')[0]; // Extract the start year
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek', // Set initial view as week
                initialDate: startYear + '-08-01', // Automatically set the calendar to the correct academic year (e.g., August of that year)
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek' // Only show week view
                },
                events: @json($events), // Load events from Laravel
                editable: false, // Prevent editing of events
                droppable: false, // Disable dragging external events
                allDaySlot: false, // Disable all-day events
                nowIndicator: true, // Show current time indicator
                timeZone: 'local', // Use local timezone
                height: 'auto', // Auto height
                slotMinTime: '07:00:00', // Show only from 7 AM
                slotMaxTime: '19:00:00', // Show until 7 PM (adjust based on latest class)
            });

            calendar.render();
        });
    </script>

    <style>
        #calendar {
            max-width: 1100px;
            margin: 0 auto;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</x-adminlayout>
