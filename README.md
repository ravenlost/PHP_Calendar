# CorbeauPerdu\Calendar\Calendar Class
<p>A PHP Simple multi-date picker calendar.</p>

<p><a href="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/CalendarUsageExample.php">**See: CalendarUsageExample.php**</a></p>

<p>The reason for yet another PHP Calendar is to provide a simple calendar with just enough features, but without an overhead of too many of them:</p>

<ul>
	<li>Start the week on Sundays or Mondays (default: Sunday)</li>
	<li>Show other month dates (default: true)</li>
	<li>Can received an array of dates to circle (i.e. holidays, meetings, etc.) *</li>
	<li>Can received an array of dates to pre-select for processing *</li>
	<li>Provides a customizable legend</li>
	<li>Allows for i18n translations using <a href="https://github.com/ravenlost/PHP_Locale">\CorbeauPerdu\i18n\Locale</a></li>
	<li>Finally, because it uses <a href="https://getbootstrap.com/">Boostrap</a>, it is responsive!</li>
</ul>

 *\* Dates must be in 'YYYY-mm-dd' string format.*

<p>**Requirements**</p>
<ul>
	<li><a href="https://getbootstrap.com/">Boostrap</a></li>
	<li><a href="https://jquery.com/">jQuery</a>: only if you use the javascripts from the <a href="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/CalendarUsageExample.php">CalendarUsageExample.php</a></li>
	<li><a href="https://github.com/ravenlost/PHP_Locale">\CorbeauPerdu\i18n\Locale</a></li>
</ul>

<p>**Other important requirements and informations**</p>
<ul>
	<li>All dates are returned and processed in 'YYYY-mm-dd' format!</li>
	<li>Expects to find a <i>loadCalendar(month,year)</i> Javascript function: use it to either reload the page passing along month and year to querystring, load the calendar using an Ajax call... As you wish!
	<li>Expects to find a <i>setSelectedDate(date)</i> Javascript function: this is where you'll toggle date cell ON/OFF and where you process the clicked date!
</ul>
