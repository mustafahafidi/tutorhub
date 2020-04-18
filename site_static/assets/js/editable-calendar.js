$(document).ready(function() {

    // initialize the calendar...

    $('#editable-calendar').fullCalendar({
        // options and callbacks here
            locale: 'it',
            header: {
                right:  'prev next',
                center: '',
                left: 'title'
            },
            footer: {
                right: 'agendaWeek,month'
            },
            height: 540,
            view: 'basicDay',
            defaultDate: '2018-02-24',
            defaultView: 'month',
			events: [
				{
					title: 'Matematica',
					start: '2018-02-24'
				},
				{
					title: 'Fisica',
					start: '2018-02-25',
					end: '2018-02-27'
				},
				{
					id: 999,
					title: 'Storia',
					start: '2018-02-29T16:00:00'
				},
				{
					id: 999,
					title: 'Inglese',
					start: '2018-02-21T16:00:00'
				},
				{
					title: 'Religione',
					start: '2018-02-22T10:30:00',
					end: '2018-02-22T12:30:00'
				},
				{
					title: 'Geografia',
					start: '2018-02-20T12:00:00'
				},
				{
					title: 'Matematica',
					start: '2018-02-19T07:00:00'
				}
			],
            eventRender: function (event, element) { 
                element.append("<button class='visuallyhidden' onclick='handleEventClick(" + event + ")' aria-label=' What you want it to say'></button>"); 
            }
        });
});