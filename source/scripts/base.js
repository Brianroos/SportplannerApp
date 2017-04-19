function init() {
  // Init Foundation
  $(document).foundation();

  // Init MagnificPopup
  $('.open-popup-link').magnificPopup({
    type:'inline',
    midClick: true
  });

  getCurrentDate();
  setActivityNonActive();
}
init();

// FUNC: Get current date
function getCurrentDate() {
  var currentDateDiv = $('header .current-date');
  var months = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];

  var currentDate = new Date();
  var dayValue = currentDate.getDate();
  var monthValue = currentDate.getMonth();
  var yearValue = currentDate.getFullYear();

  currentDateDiv.html('<p><span>Vandaag:</span> '+ dayValue +' '+ months[monthValue] +' '+ yearValue +'</p>');
}

// FUNC: Set activity non-active
function setActivityNonActive() {
  var activities = $('li.event');

  if(activities.length > 0) {
    $.each(activities, function(key, value) {
      var inside = $(this).find($('.event-inside'));
      var weather = $(this).find($('.weather span')).html();

      // If under 10 degrees
      if(weather < 10) {
        inside.addClass('not');
      }
    });
  }
}
