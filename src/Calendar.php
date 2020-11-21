<?php

namespace CorbeauPerdu\Calendar;

use DateTime;

/******************************************************************************
 * Calendar Class
 * 
 * MIT License
 * 
 * Copyright (c) 2020 Patrick Roy
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * @author      Patrick Roy (ravenlost2@gmail.com)
 * @version     1.0
 *
 * @uses \DateTime
 * @uses \CorbeauPerdu\i18n\Locale
 * @uses Bootstrap
 ******************************************************************************/
class Calendar
{
  private $dayLabels;
  private $monthLabels;
  private $startWeekOnMonday;
  private $showOtherMonths;
  private $forceSixRows;
  private $calendarData;
  private $locale;
  private $anchor;
  private $legendDateCircled;
  private $legendDateSelected;

  /**
   * Constructor
   * @param \CorbeauPerdu\i18n\Locale $locale CorbeauPerdu's locale instance for translations
   * @param bool $startWeekOnMonday Whether to start weeks on Mondays (default: start on sunday)
   * @param bool $showOtherMonths Whether to display dates in other months at the start or end of the current month (default: true)
   * @param bool $forceSixRows Whether to show a 6th row in the calendar, EVEN if that hole row belongs to the next month (default: false)
   *                           Only applicable with $showOtherMonths = true   
   * @param string $anchor calendar href anchor to reposition page upon calendar reload (default: 'calendar')
   *                       this'll be mainly useful if the loadCalendar() JS loads the calendar from an Ajax call;
   *                       if loadCalendar() simply reloads the page with querystring params for instance with window.location.href + '?m=month&y=year'
   *                       then need to set the '#anchor' in that js url href
   * @param string $legendDateCircled Legend text for Circled dates: pass along the text to load from the locale 'calendar' domain
   * @param string $legendDateSelected Legend text for Selected dates: pass along the text to load from the locale 'calendar' domain
   * 
   */
  public function __construct(&$locale, bool $startWeekOnMonday = false, bool $showOtherMonths = true, bool $forceSixRows = false, $anchor = "calendar", $legendDateCircled = 'Date already declared', $legendDateSelected = 'Date selected')
  {
    $this->startWeekOnMonday = $startWeekOnMonday;
    $this->showOtherMonths = $showOtherMonths;
    $this->forceSixRows = $forceSixRows;
    $this->anchor = $anchor;
    $this->legendDateCircled = $legendDateCircled;
    $this->legendDateSelected = $legendDateSelected;

    // get the translation object
    $this->locale = $locale;

    // set the day labels
    if ( $startWeekOnMonday )
    {
      $this->dayLabels = [  'Mon' => 'Monday', 'Tue' => "Tuesday", 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday' ];
    }
    else
    {
      $this->dayLabels = [  'Sun' => 'Sunday', 'Mon' => 'Monday', 'Tue' => "Tuesday", 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday' ];
    }

    // set the month labels
    $this->monthLabels = array ( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
  }

  /**
   * show()
   * Show the calendar
   * @param string $month for which month?
   * @param string $year for which year?
   * @param array $circledDates dates that are to be circled, in 'YYYY-mm-dd' string format
   * @param array $selectedDates dates that are flagged as selected, in to be in 'YYYY-mm-dd' string format
   * @return string full calendar html
   */
  public function show($month = null, $year = null, $circledDates = null, $selectedDates = null)
  {
    // default month and year to current date if not specified
    $month = $month ?? date("m", time());
    $year = $year ?? date("Y", time());

    // append a leading 0 to month lower than 10
    $month = sprintf("%02d", $month);

    // if we received an invalid year/month, default to this month!
    if ( $this->_validDateTime("$year-$month-01", 'Y-m-d') == false )
    {
      $year = date("Y", time());
      $month = date("m", time());
    }

    // set previous and next month/year
    // also used in buildCalendarData(), thus why scope is object
    $this->preMonth = $month == 1 ? 12 : intval($month) - 1;
    $this->preYear = $month == 1 ? intval($year) - 1 : $year;
    $this->nextMonth = $month == 12 ? 1 : intval($month) + 1;
    $this->nextYear = $month == 12 ? intval($year) + 1 : $year;

    // build the calendar data
    $this->_buildCalendarData($month, $year);

    // @formatter:off
    $content = ''
    . '<div id="calendar" class="container-fluid">' // start calendar container

    // ----------------------------------
    // header: top nav
    . '<div class="calendar_topnav">'
    . '  <div class="row">'
    . '    <div class="col text-left"><a class="" href="#' . $this->anchor . '" onclick="loadCalendar(\'' . sprintf('%02d', $this->preMonth) . '\', \'' . $this->preYear . '\');">' . $this->locale->_d('calendar','Prev') . '</a></div>'
    . '    <div class="col text-center calendar_title">' . $year . ' ' . $this->locale->_d('calendar', $this->monthLabels[intval($month) - 1]) . '</div>'
    . '    <div class="col text-right"><a class="" href="#' . $this->anchor . '" onclick="loadCalendar(\'' . sprintf('%02d', $this->nextMonth) . '\', \'' . $this->nextYear . '\');">'. $this->locale->_d('calendar','Next') .'</a></div>'
    . '  </div>'
    . '  <div class="row">'
    . '    <div class="col text-center calendar_todayTopnav">[ <a id="calendar_todaylnk" href="#' . $this->anchor . '" onclick="loadCalendar(\'' . date("m", time()) . '\', \'' . date("Y", time()) . '\');">' . strtolower($this->locale->_d('calendar','Today')) . '</a> ]</div>'
    . '  </div>'
    . '</div>';
    // @formatter:on

    // ----------------------------------
    // header: week days
    $content .= '<div class="calendar_weekdays row">';
    foreach ( $this->dayLabels as $shortLbl => $fullLbl )
    {
      $content .= '<div class="col px-0 px-md-2" title="' . $this->locale->_d('calendar', $fullLbl) . '">' . $this->locale->_d('calendar', $shortLbl) . '</div>';
    }
    $content .= '</div>';

    // ----------------------------------
    // calendar content
    $content .= '<div class="calendar_content">'; // start calendar content

    $rowOpened = false;

    $calendarSize = count($this->calendarData);

    for ( $i = 0; $i < $calendarSize; $i ++ )
    {
      $currentDate = $this->calendarData[$i]['Year'] . '-' . sprintf("%02d", $this->calendarData[$i]['Month']) . '-' . sprintf("%02d", $this->calendarData[$i]['Day']);

      $today = false;
      $day_circled = false;
      $day_selected = false;
      $day_otherMonth = false;
      $day_weekend = false;

      if ( $rowOpened == false )
      {
        $content .= '<div class="row m-0">'; // start calendar row
        $rowOpened = true;
      }

      // flag DAY is from OTHER MONTH
      if ( sprintf("%02d", $this->calendarData[$i]['Month']) != sprintf("%02d", $month) )
      {
        $day_otherMonth = true;
      }

      // flag TODAY
      if ( $currentDate == date('Y-m-d') )
      {
        $today = true;
      }

      // flag WEEKEND
      if ( $this->_dateIsWeekend($currentDate) )
      {
        $day_weekend = true;
      }

      // flag DAY IS CIRCLED
      if ( ( ! empty($circledDates) ) and ( in_array($currentDate, $circledDates) ) )
      {
        $day_circled = true;
      }

      // flag DAY PRE-SELECTED
      if ( ( ! empty($selectedDates) ) and ( in_array($currentDate, $selectedDates) ) )
      {
        $day_selected = true;
      }

      // @formatter:off
      $content .= ''  // start calendar cell
      . '<div id="' . $currentDate . '" '
      . ' class="calendar_cell col px-0 px-md-2'
      . ( ( $today ) ? ' calendar_today' : '')
      . ( ( $day_selected ) ? ' calendar_selected' : '')
      . ( ( $day_otherMonth ) ? ( $this->showOtherMonths ) ? ' calendar_otherMonth' : ' calendar_otherMonthHidden' : '')
      . ( ( $day_weekend ) ? ' calendar_weekend' : '')      
      . '" ';      
      // @formatter:on

      // enable onClick for other month only if showOtherMonth is true
      if ( ( $day_otherMonth == false ) or ( $this->showOtherMonths == true ) )
      {
        $content .= ' onclick="setSelectedDate(\'' . $currentDate . '\');" ';
      }

      // @formatter:off
      $content .= ''
      . ' aria-label="' . $currentDate . '"'
      . ' aria-describedby="'
      . ( ( $today ) ? 'dateTodayDesc ' : '')
      . ( ( $day_selected ) ? 'dateSelectedDesc' : '')
      . '"'
      . '>';
      // @formatter:on

      // hide if other month and we don't want it displayed!
      if ( ( $day_otherMonth ) and ( $this->showOtherMonths == false ) ) $content .= '<div class="d-none">';

      // circle the date? only if day is not selected!
      if ( ( $day_circled ) and ( ! $day_selected ) )
      {
        $content .= '<span class="calendar_circle" aria-label="' . $this->locale->_d('calendar', 'Date circled') . '" aria-describedby="dateCircledDesc">' . ( ( intval($this->calendarData[$i]['Day']) < 10 ) ? '&nbsp;' . $this->calendarData[$i]['Day'] . '&nbsp;' : $this->calendarData[$i]['Day'] ) . '</span>';
      }
      else
      {
        $content .= $this->calendarData[$i]['Day'];
      }

      if ( ( $day_otherMonth ) and ( $this->showOtherMonths == false ) ) $content .= '</div>';

      $content .= '</div>'; // end calendar cell

      // close calendar row if we're at the end of the week
      if ( ( $i + 1 ) % 7 == 0 )
      {
        $content .= '</div>'; // end calendar row
        $rowOpened = false;
      }
    }

    $content .= '</div>'; // end calendar content

    // ----------------------------------
    // footer
    // @formatter:off
    $content .= ''
    . '  <div class="calendar_footer row justify-content-center">'
    . '    <div id="dateTodayDesc" class="col-auto py-2 py-md-0 text-nowrap">'
    . '      <span class="calendar_today" aria-label="' . $this->locale->_d('calendar','Date marked in red') . '">X</span> = ' . $this->locale->_d('calendar','Today')
    . '    </div>'
    . '    <div id="dateCircledDesc" class="col-auto py-2 py-md-0 text-nowrap">'
    . '      <span class="calendar_circle my-1 py-1 px-2" aria-label="' . $this->locale->_d('calendar','Date circled') . '">&nbsp;&nbsp;</span> = ' . $this->locale->_d('calendar', $this->legendDateCircled)
    . '    </div>'
    . '    <div id="dateSelectedDesc" class="col-auto py-2 py-md-0 text-nowrap">'
    . '      <span class="calendar_selected py-1 px-2" aria-label="' . $this->locale->_d('calendar','Date selected') . '">&nbsp;&nbsp;</span> = ' . $this->locale->_d('calendar', $this->legendDateSelected)
    . '    </div>'
    . '  </div>';
    // @formatter:on

    $content .= '</div>'; // end calendar container

    return $content;
  }

  /**
   * _buildCalendarData()
   * Build the actual calendar data array for later printing in the show method!
   * @param string $month for which month?
   * @param string $year for which year?
   */
  private function _buildCalendarData($month, $year)
  {
    // Get the day (0-6) the first day of the month starts on
    $startPos = date('w', strtotime("$year-$month-01"));

    // alter start position based on week start day
    if ( $this->startWeekOnMonday )
    {
      if ( $startPos == 0 )
      {
        $startPos = 6;
      }
      else
      {
        $startPos --;
      }
    }

    // Get the number of days in month
    $nbrDays = $this->_daysInMonth($month, $year);

    // Get the number of days in the previous month
    $nbrDaysPrevious = $this->_daysInMonth($this->preMonth, $this->preYear);

    // Init the array
    $this->calendarData = [ ];

    // set the values for every day in the previous month
    for ( $i = 0; $i < $startPos; $i ++ )
    {
      $this->calendarData[$i] = [ ];
      $this->calendarData[$i]['Day'] = ( $nbrDaysPrevious - $startPos ) + $i + 1;
      $this->calendarData[$i]['Month'] = $this->preMonth;
      $this->calendarData[$i]['Year'] = $this->preYear;
    }

    // set the values for every day in selected month
    for ( $i = 0; $i < $nbrDays; $i ++ )
    {
      $this->calendarData[$startPos + $i] = [ ];
      $this->calendarData[$startPos + $i]['Day'] = $i + 1;
      $this->calendarData[$startPos + $i]['Month'] = $month;
      $this->calendarData[$startPos + $i]['Year'] = $year;
    }

    // set the values for every day in the next month
    for ( $i = $nbrDays; $i < ( 42 - $startPos ); $i ++ )
    {
      // don't set calendar last row if it all belongs to the next month
      if ( ( ( $this->forceSixRows == false ) or ( $this->showOtherMonths == false ) ) and ( ( $startPos + $i ) == 35 ) )
      {
        break;
      }

      $this->calendarData[$startPos + $i] = [ ];
      $this->calendarData[$startPos + $i]['Day'] = ( $i + 1 ) - $nbrDays;
      $this->calendarData[$startPos + $i]['Month'] = $this->nextMonth;
      $this->calendarData[$startPos + $i]['Year'] = $this->nextYear;
    }
  }

  /**
   * _daysInMonth()
   * Calculate number of days in a particular month
   * @param string $month
   * @param string $year
   * @return string
   */
  private function _daysInMonth($month = null, $year = null)
  {
    return date('t', strtotime($year . '-' . $month . '-01'));
  }

  /**
   * _validDateTime()
   * Check if valid datetime string
   * @param string $date
   * @param string $format default 'Y-m-d H:i:s'
   * @return boolean
   */
  private function _validDateTime(string $date, string $format = 'Y-m-d H:i:s')
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }

  /**
   * _dateIsWeekend()
   * Checks if given date is on weekend
   * @param string $date
   * @return boolean
   */
  private function _dateIsWeekend(string $date)
  {
    $dt = strtolower(date('l', strtotime($date)));
    return ( ( $dt == "saturday" ) || ( $dt == "sunday" ) );
  }
}

?>
