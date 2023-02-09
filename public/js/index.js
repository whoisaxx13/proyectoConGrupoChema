document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: [
        {
          id: 'a',
          title: 'my event',
          start: '2023-02-02'
        }
        
      ]
    });
    calendar.render();
  });
