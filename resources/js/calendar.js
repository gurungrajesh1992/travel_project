import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('departure-calendar');

    if (!el) {
        return;
    }

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth',
        },
        firstDay: 0,
        height: 'auto',
        dayMaxEvents: 3,
        events: el.dataset.eventsUrl,
        eventDidMount(info) {
            info.el.title = `${info.event.title} (${info.event.extendedProps.status})`;
        },
    });

    calendar.render();
});
