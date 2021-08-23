# CorbeauPerdu\Calendar\Calendar Class
<p>A PHP Simple multi-date picker calendar.</p>

<a href="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/CalendarUsageExample.php">**See: CalendarUsageExample.php**</a>

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

**Requirements**
<ul>
	<li><a href="https://getbootstrap.com/">Boostrap</a></li>
	<li><a href="https://jquery.com/">jQuery</a>: only if you use the javascript from the <a href="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/CalendarUsageExample.php">CalendarUsageExample.php</a></li>
	<li><a href="https://github.com/ravenlost/PHP_Locale">\CorbeauPerdu\i18n\Locale</a></li>
</ul>

**Other important requirements and informations**
<ul>
	<li>All dates are returned and processed in 'YYYY-mm-dd' format!</li>
	<li>Expects to find a <i>loadCalendar(month,year)</i> Javascript function: use it to either reload the page passing along month and year to querystring, load the calendar using an Ajax call... As you wish!
	<li>Expects to find a <i>setSelectedDate(date)</i> Javascript function: this is where you'll toggle date cell ON/OFF (by toggling class _calendar_selected_ on the cell IDs) and where you process the clicked date!
</ul>

**Methods**
<ul>
	<li>Object's constructor : 
		<ul>
			<li>\CorbeauPerdu\i18n\Locale $locale CorbeauPerdu's locale instance for translations</li>
			<li>bool $startWeekOnMonday Whether to start weeks on Mondays (default: start on sunday)</li>
			<li>bool $showOtherMonths Whether to display dates in other months at the start or end of the current month (default: true)</li>
			<li>bool $forceSixRows Whether to show a 6th row in the calendar, EVEN if that hole row belongs to the next month (default: false)</li>
			<li>string $anchor calendar href anchor to reposition page upon calendar reload (default: 'calendar')</li>
			<li>string $legendDateCircled Legend text for circled dates: pass along the text to load from the locale 'calendar' domain</li>
			<li>string $legendDateSelected Legend text for Selected dates: pass along the text to load from the locale 'calendar' domain</li>
		</ul>
	</li>
	<li>$calendar->show()
		<ul>
			<li>string $month for which month?</li>
			<li>string $year for which year?</li>
			<li>array $circledDates dates that are to be circled, in 'YYYY-mm-dd' string format</li>
			<li>array $selectedDates dates that are flagged as selected, in to be in 'YYYY-mm-dd' string format</li>
		</ul>		
	</li>
</ul>

**Demos:**

<p>The calendar is loaded from getting month and year in the URL querystring:</p>
<img src="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/demo1-querystring.png"/>

<p>The calendar is loaded from an Ajax call that passes along the desired month and year, hidding other month, week starts on Mondays and finally, in french:</p>
<img src="https://github.com/ravenlost/PHP_Calendar/blob/master/UsageExamples/demo2-ajax-french-hideothermonth.png"/>
