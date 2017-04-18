function init() {
  // Init Foundation
  $(document).foundation();

  getCurrentDate();
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
