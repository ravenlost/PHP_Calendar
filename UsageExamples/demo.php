<?php

require_once ( 'CorbeauPerdu/Calendar/Calendar.php' );
require_once ( 'CorbeauPerdu/i18n/Locale.php' );


use CorbeauPerdu\Calendar\Calendar;

// init locale: see https://github.com/ravenlost/PHP_Locale
// this'll be required for translations inside the calendar!
// the calendar requires translations to be in its own 'calendar' domain! 
$locale = new Locale(...);


$startWeekOnMonday = false; // start week on monday, else starts on sunday
$showOtherMonths = true;    // show dates in other month cells (previous and next month)
$forceSixRows = true;       // if showing other month cells, always show 6 rows, even if last row belongs entirely to the next month
$legendCircledDate = 'Canadian holiday'; // set a specific legend for circled dates

$mycal = new Calendar($locale, $startWeekOnMonday, $showOtherMonths, $forceSixRows, null, $legendCircledDate);

?>

<html>
<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="prestadesk-calendar.css" rel="stylesheet" type="text/css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">
/**
 * loadCalendar()
 * If no params passed, then TODAY's month and year will be used
 * @param int month
 * @param int year
 */
function loadCalendar(month, year) {
	window.location.href = location.protocol + '//' + location.host + location.pathname+ "?m=" + month + "&y=" + year + '#calendar';
}

/**
 * setSelectedDate()
 * Toggles selecting / unselecting a date
 * @param string date cell to toggle with and get date from
 */
function setSelectedDate(date) {
	
  // highlight or unhighlight date cell
  $('#' + date).toggleClass('calendar_selected');

  // set proper aria describedby
  if( $('#' + date).hasClass('calendar_selected') ){
    var preAriaDescBy = $('#' + date).attr("aria-describedby");
    $('#' + date).attr("aria-describedby", ( (preAriaDescBy) ? preAriaDescBy + ' ' : '' ) + "dateSelectedDesc");
  }
  else {    
    $('#' + date).attr("aria-describedby", ( $('#' + date).hasClass('calendar_today') ) ? 'dateTodayDesc' : ''); // clear the selectedDesc aria, and make sure to set back the today aria if cell is today!
  }

  // do something with date... 
  // i.e. push or remove selected date from listing
  if( $('#seldate-' + date).length ) {
    $('#seldate-' + date).remove();
  }
  else {
    $('#selectedDates').append( "<li id=\"seldate-" + date + "\">" + date + "</li>" );
  }  
}

</script>

</head>
<body>

<div class="container">
	
	<h1>My Calendar</h1>
	
	<?php 
  // set some test data

	// dates circled could be dates which has have a special event attached to them (i.e. holidays, meetings, dates of working days, etc.)
  $circled_dates = array('2020-11-08', '2020-11-09', '2020-11-17', '2020-12-01', '2020-12-11', '2020-12-12');
  
  // dates selected would be dates we want to process by clicking/unclicking them
  $selected_dates = array('2020-11-08', '2020-12-11');
  ?>
	
  <!-- print the first selected dates -->
	<h4 class="mt-5">Demo Selected Dates:</h4>
	<ul id="selectedDates" class="mb-5">
  	<?php 
  	foreach($selected_dates as $dt){
  	  echo '<li id="seldate-' . $dt . '">' . $dt . '</li>';
  	}
  	?>
	</ul>
	
  <!-- show the calendar -->
  <?=$mycal->show( ($_GET['m'] ?? null), ($_GET['y'] ?? null), $circled_dates, $selected_dates)?>
  
</div>

</body>
</html>
