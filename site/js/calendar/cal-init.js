var calendarObj = $('#calendar').fullCalendar({
  events: 'api/availability.php',
  customButtons: {
      deleteAll: {
          text: 'Elimina tutte le disponibilità',
          click: function() {
             calendarObj.fullCalendar('removeEvents');
          }
      },
      save: {
          text: 'Salva',
          click: function() {

            var dataObj = calendarObj.fullCalendar('clientEvents');
            var dataPost = "save=1";
            for(var i=0; i<dataObj.length; i++) {
              event=dataObj[i];
              dataPost+="&ev["+i+"]['start']="+JSON.stringify(event.start)+"&ev["+i+"]['end']="+JSON.stringify(event.end);
            }
            if(dataObj.length>0)
              $.post("api/availability.php", dataPost, function(data) {
                alert("Calendario salvato : "+data);
              });
          }
      }
  },
  header: {
    left: 'deleteAll',
    center: 'title',
    right: 'save,prev,next'
  },
  defaultDate: '2018-02-12',
  defaultView: 'agendaWeek',
  editable: true,
  selectable: true,
  select: function (start, end, allDay) { 
    calendarObj.fullCalendar('renderEvent', {
                        title: "Disponibile",
                        start: start,
                        end: end,
                        allDay: false
                    }, true);  

  },
  eventClick: function(calEvent, jsEvent, view) {
      if(confirm("Eliminare la disponibilità dalle "+calEvent.start.format("HH:mm")+" alle: "+calEvent.end.format("HH:mm")+"?")) 
        calendarObj.fullCalendar('removeEvents', function (ev) { return (ev._id == calEvent._id); });
  }
});

var calendarObj1 = $('#calendar-pren').fullCalendar({
  events: 'api/appointments.php',
  header: {
    left: 'deleteAll',
    center: 'title',
    right: 'save,prev,next'
  },
  defaultDate: '2018-02-12',
  defaultView: 'month'

  
});
