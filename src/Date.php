<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

use \DateTime;
use \DateTimeZone;
use \Exception as DateException;

/**
 * Date object
 * 
 * Based on the Joomla Date object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Date extends DateTime
{
  const DAY_ABBR   = "\x021\x03";
  const DAY_NAME   = "\x022\x03";
  const MONTH_ABBR = "\x023\x03";
  const MONTH_NAME = "\x024\x03";
  const DAY_SUFFIX = "\x025\x03";
  
  /**
   * Holds an array of translations
   * 
   * Defaults are in french
   * 
   * @var   array
   */
  protected static $translations = [
      'DAY_MON' => 'Lun', 
      'DAY_TUE' => 'Mar',
      'DAY_WED' => 'Mer',
      'DAY_THU' => 'Jeu',
      'DAY_FRI' => 'Ven',
      'DAY_SAT' => 'Sam',
      'DAY_SUN' => 'Dim',
      'DAY_MONDAY' => 'lundi',
      'DAY_TUESDAY' => 'mardi',
      'DAY_WEDNESDAY' => 'mercredi',
      'DAY_THURSDAY' => 'jeudi',
      'DAY_FRIDAY' => 'vendredi',
      'DAY_SATURDAY' => 'samedi',
      'DAY_SUNDAY' => 'dimanche',
      'MONTH_JANUARY' => 'janvier',
      'MONTH_JANUARY_SHORT' => 'Jan',
      'MONTH_FEBRUARY' => 'février',
      'MONTH_FEBRUARY_SHORT' => 'Fév',
      'MONTH_MARCH' => 'mars',
      'MONTH_MARCH_SHORT' => 'Mar',
      'MONTH_APRIL' => 'avril',
      'MONTH_APRIL_SHORT' => 'Avr',
      'MONTH_MAY' => 'mai',
      'MONTH_MAY_SHORT' => 'Mai',
      'MONTH_JUNE' => 'juin',
      'MONTH_JUNE_SHORT' => 'Jui',
      'MONTH_JULY' => 'juillet',
      'MONTH_JULY_SHORT' => 'Juil',
      'MONTH_AUGUST' => 'août',
      'MONTH_AUGUST_SHORT' => 'Aoû',
      'MONTH_SEPTEMBER' => 'septembre',
      'MONTH_SEPTEMBER_SHORT' => 'Sep',
      'MONTH_OCTOBER' => 'octobre',
      'MONTH_OCTOBER_SHORT' => 'Oct',
      'MONTH_NOVEMBER' => 'novembre',
      'MONTH_NOVEMBER_SHORT' => 'Nov',
      'MONTH_DECEMBER' => 'décembre',
      'MONTH_DECEMBER_SHORT' => 'Déc',
  ];
  
  /**
   * The format string to be applied when using the __toString() magic method.
   *
   * @var    string
   */
  public static $format = 'Y-m-d H:i:s';
  
  /**
   * Placeholder for a DateTimeZone object with GMT as the time zone.
   *
   * @var    object
   */
  protected static $gmt;
  
  /**
   * Placeholder for a DateTimeZone object with the default server
   * time zone as the time zone.
   *
   * @var    object
   */
  protected static $stz;
  
  /**
   * An array of offsets and time zone strings
   *
   * @var    array
   */
  protected static $offsets = [
    '-12'   => 'Etc/GMT-12', 
    '-11'   => 'Pacific/Midway', 
    '-10'   => 'Pacific/Honolulu', 
    '-9.5'  => 'Pacific/Marquesas',
    '-9'    => 'US/Alaska', 
    '-8'    => 'US/Pacific', 
    '-7'    => 'US/Mountain', 
    '-6'    => 'US/Central', 
    '-5'    => 'US/Eastern', 
    '-4.5'  => 'America/Caracas',
    '-4'    => 'America/Barbados', 
    '-3.5'  => 'Canada/Newfoundland', 
    '-3'    => 'America/Buenos_Aires', 
    '-2'    => 'Atlantic/South_Georgia',
    '-1'    => 'Atlantic/Azores', 
    '0'     => 'Europe/London', 
    '1'     => 'Europe/Amsterdam', 
    '2'     => 'Europe/Istanbul', 
    '3'     => 'Asia/Riyadh',
    '3.5'   => 'Asia/Tehran', 
    '4'     => 'Asia/Muscat', 
    '4.5'   => 'Asia/Kabul', 
    '5'     => 'Asia/Karachi', 
    '5.5'   => 'Asia/Calcutta',
    '5.75'  => 'Asia/Katmandu', 
    '6'     => 'Asia/Dhaka', 
    '6.5'   => 'Indian/Cocos', 
    '7'     => 'Asia/Bangkok', 
    '8'     => 'Australia/Perth',
    '8.75'  => 'Australia/West', 
    '9'     => 'Asia/Tokyo', 
    '9.5'   => 'Australia/Adelaide', 
    '10'    => 'Australia/Brisbane',
    '10.5'  => 'Australia/Lord_Howe', 
    '11'    => 'Pacific/Kosrae', 
    '11.5'  => 'Pacific/Norfolk', 
    '12'    => 'Pacific/Auckland',
    '12.75' => 'Pacific/Chatham', 
    '13'    => 'Pacific/Tongatapu', 
    '14'    => 'Pacific/Kiritimati',
  ];
  
  /**
   * The DateTimeZone object for usage in rending dates as strings.
   *
   * @var    object
   */
  protected $_tz;
  
  /**
   * Set the translations
   *
   * @param   array   $translations     Key/value pairs of translations
   * @return  void
   */
  public static function setTranslations(array $translations=[])
  {
    self::$translations = array_merge(self::$translations, $translations);
  }
  
  /**
   * Proxy for new Date()
   * 
   * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
   * @param   mixed   $tz    Time zone to be used for the date.
   * @return   Date
   */
  public static function getInstance($date='now', $tz=null)
  {
    return new Date($date, $tz);
  }

  /**
   * Constructor
   *
   * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
   * @param   mixed   $tz    Time zone to be used for the date.
   */
  public function __construct($date='now', $tz=null)
  {
    // Create the base GMT and server time zone objects.
    if ( !isset(self::$gmt) || !isset(self::$stz) ){
      self::$gmt = new DateTimeZone('GMT');
      self::$stz = new DateTimeZone(@date_default_timezone_get());
    }
    
    // If the time zone object is not set, attempt to build it.
    if ( !($tz instanceof DateTimeZone) ){
      if ( $tz === null ){
        $tz = self::$gmt;
      }
      elseif ( is_numeric($tz) ){
        // Translate from offset.
        $tz = new DateTimeZone(self::$offsets[(string) $tz]);
      }
      elseif ( is_string($tz) ){
        $tz = new DateTimeZone($tz);
      }
    }
    
    $_TZ = date_default_timezone_get();
    
    // If the date is numeric assume a unix timestamp and convert it.
    if ( is_numeric($date) ){
      date_default_timezone_set('UTC');
      $date = date('c', $date);
      date_default_timezone_set($_TZ);
    }
    
    // Call the DateTime constructor.
    parent::__construct($date, $tz);

    // reset the timezone for 3rd party libraries/extension that does not use Date
    date_default_timezone_set(self::$stz->getName());
    
    // Set the timezone object for access later.
    $this->_tz = $tz;
  }

  /**
   * Magic method to return some protected property values
   *
   * @param   string  $name  The name of the property to return
   * @return   mixed
   */
  public function __get($name)
  {
    $value = null;

    switch($name){
      case 'daysinmonth':
        $value = $this->format('t', true);
        break;

      case 'dayofweek':
        $value = $this->format('N', true);
        break;

      case 'dayofyear':
        $value = $this->format('z', true);
        break;

      case 'isleapyear':
        $value = (boolean) $this->format('L', true);
        break;

      case 'day':
        $value = $this->format('d', true);
        break;

      case 'hour':
        $value = $this->format('H', true);
        break;

      case 'minute':
        $value = $this->format('i', true);
        break;

      case 'second':
        $value = $this->format('s', true);
        break;

      case 'month':
        $value = $this->format('m', true);
        break;

      case 'ordinal':
        $value = $this->format('S', true);
        break;

      case 'week':
        $value = $this->format('W', true);
        break;

      case 'year':
        $value = $this->format('Y', true);
        break;
      
      default:
        throw new DateException('Cannot access/get property ' . __CLASS__ . '::' . $name);
    }
    
    return $value;
  }

  /**
   * Magic method to get the string representation of this object
   * 
   * @return   string
   */
  public function __toString()
  {
    return (string) parent::format(self::$format);
  }

  /**
   * Gets the date as a formatted string in a local calendar.
   *
   * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
   * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
   * @param   boolean  $translate  True to translate localised strings
   * @return   string   The date string in the specified format format.
   */
  public function calendar($format, $local=false, $translate=true)
  {
    return $this->format($format, $local, $translate);
  }
  
  /**
   * Gets the date as a formatted string.
   *
   * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
   * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
   * @param   boolean  $translate  True to translate localised strings
   * @return   string   The date string in the specified format format.
   */
  public function format($format, $local=false, $translate=true)
  {
    if ( $translate === true ){
      // Do string replacements for date format options that can be translated.
      $format = preg_replace('/^(.*)D/', "$1".self::DAY_ABBR, $format);
      $format = preg_replace('/^(.*)l/', "$1".self::DAY_NAME, $format);
      $format = preg_replace('/^(.*)M/', "$1".self::MONTH_ABBR, $format);
      $format = preg_replace('/^(.*)F/', "$1".self::MONTH_NAME, $format);
      $format = preg_replace('/^(.*)Z/', "$1".self::DAY_SUFFIX, $format);
    }
    
    // If the returned time should not be local use GMT.
    if ( $local === false ){
      parent::setTimezone(self::$gmt);
    }
    else {
      parent::setTimezone(self::$stz);
    }

    // Format the date.
    $return = parent::format($format);

    if ( $translate === true ){
      // Manually modify the month and day strings in the formatted time.
      if ( strpos($return, self::DAY_ABBR) !== false ){
        $return = str_replace(self::DAY_ABBR, $this->dayToString(parent::format('w'), true), $return);
      }

      if ( strpos($return, self::DAY_NAME) !== false ){
        $return = str_replace(self::DAY_NAME, $this->dayToString(parent::format('w')), $return);
      }

      if ( strpos($return, self::MONTH_ABBR) !== false ){
        $return = str_replace(self::MONTH_ABBR, $this->monthToString(parent::format('n'), true), $return);
      }

      if ( strpos($return, self::MONTH_NAME) !== false ){
        $return = str_replace(self::MONTH_NAME, $this->monthToString(parent::format('n')), $return);
      }

      if ( strpos($return, self::DAY_SUFFIX) !== false ){
        $return = str_replace(self::DAY_SUFFIX, $this->daySuffix(parent::format('j')), $return);
      }
    }

    if ( $local === false ){
      parent::setTimezone($this->_tz);
    }

    return $return;
  }

  /**
   * Get the time offset from GMT in hours or seconds.
   *
   * @param   boolean  $hours  True to return the value in hours.
   * @return   float  The time offset from GMT either in hours or in seconds.
   */
  public function getOffsetFromGMT($hours=false)
  {
    return (float) $hours ? ($this->_tz->getOffset($this) / 3600) : $this->_tz->getOffset($this);
  }

  /**
   * Translates day of week number to a string.
   *
   * @param   integer  $day   The numeric day of the week.
   * @param   boolean  $abbr  Return the abbreviated day string?
   * @return   string  The day of the week.
   */
  public function dayToString($day, $abbr=false)
  {
    switch ($day)
    {
      case 0:
        return $abbr ? $this->translate('DAY_SUN') : $this->translate('DAY_SUNDAY');
      case 1:
        return $abbr ? $this->translate('DAY_MON') : $this->translate('DAY_MONDAY');
      case 2:
        return $abbr ? $this->translate('DAY_TUE') : $this->translate('DAY_TUESDAY');
      case 3:
        return $abbr ? $this->translate('DAY_WED') : $this->translate('DAY_WEDNESDAY');
      case 4:
        return $abbr ? $this->translate('DAY_THU') : $this->translate('DAY_THURSDAY');
      case 5:
        return $abbr ? $this->translate('DAY_FRI') : $this->translate('DAY_FRIDAY');
      case 6:
        return $abbr ? $this->translate('DAY_SAT') : $this->translate('DAY_SATURDAY');
    }
  }

  /**
   * Translates month number to a string.
   *
   * @param   integer  $month  The numeric month of the year.
   * @param   boolean  $abbr   If true, return the abbreviated month string
   * @return   string  The month of the year.
   */
  public function monthToString($month, $abbr=false)
  {
    switch($month){
      case 1:
        return $abbr ? $this->translate('MONTH_JANUARY_SHORT') : $this->translate('MONTH_JANUARY');
      case 2:
        return $abbr ? $this->translate('MONTH_FEBRUARY_SHORT') : $this->translate('MONTH_FEBRUARY');
      case 3:
        return $abbr ? $this->translate('MONTH_MARCH_SHORT') : $this->translate('MONTH_MARCH');
      case 4:
        return $abbr ? $this->translate('MONTH_APRIL_SHORT') : $this->translate('MONTH_APRIL');
      case 5:
        return $abbr ? $this->translate('MONTH_MAY_SHORT') : $this->translate('MONTH_MAY');
      case 6:
        return $abbr ? $this->translate('MONTH_JUNE_SHORT') : $this->translate('MONTH_JUNE');
      case 7:
        return $abbr ? $this->translate('MONTH_JULY_SHORT') : $this->translate('MONTH_JULY');
      case 8:
        return $abbr ? $this->translate('MONTH_AUGUST_SHORT') : $this->translate('MONTH_AUGUST');
      case 9:
        return $abbr ? $this->translate('MONTH_SEPTEMBER_SHORT') : $this->translate('MONTH_SEPTEMBER');
      case 10:
        return $abbr ? $this->translate('MONTH_OCTOBER_SHORT') : $this->translate('MONTH_OCTOBER');
      case 11:
        return $abbr ? $this->translate('MONTH_NOVEMBER_SHORT') : $this->translate('MONTH_NOVEMBER');
      case 12:
        return $abbr ? $this->translate('MONTH_DECEMBER_SHORT') : $this->translate('MONTH_DECEMBER');
    }
  }
  
  /**
   * Adds day suffix.
   *
   * @param   int     $day    The numeric day
   * @return   string  The suffixed day.
   */
  public function daySuffix($day)
  {
    $locale = \Locale::getDefault();
    
    switch($day){
      case 1:
        $nf  = new \NumberFormatter($locale, \NumberFormatter::ORDINAL);
        $day = $nf->format($day);
    }
    
    return $day;
  }
  
  /**
   * Wrap the setTimezone() function and set the internal
   * time zone object.
   *
   * @param   object  $tz  The new DateTimeZone object.
   * @return   DateTimeZone  The old DateTimeZone object.
   */
  public function setTimezone($tz)
  {
    $this->_tz = $tz;
    return parent::setTimezone($tz);
  }

  /**
   * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
   * and it can be found at the IETF Web site.
   *
   * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
   * @return   string  The date string in ISO 8601 format.
   *
   * @link    http://www.ietf.org/rfc/rfc3339.txt
   */
  public function toISO8601($local=false)
  {
    return $this->format(DateTime::RFC3339, $local, false);
  }

  /**
   * Gets the date as an SQL datetime string.
   *
   * @param   boolean     $local  True to return the date string in the local time zone, false to return it in GMT.
   * @return   string The date string in SQL datetime format.
   *
   * @link http://dev.mysql.com/doc/refman/5.0/en/datetime.html
   */
  public function toSql($local=false)
  {
    return $this->format(Dbo()->dateFormat, $local, false);
  }
  
  /**
   * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
   * can be found at the IETF Web site.
   *
   * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
   * @return   string   The date string in RFC 822 format.
   *
   * @link    http://www.ietf.org/rfc/rfc2822.txt
   */
  public function toRFC822($local=false)
  {
    return $this->format(DateTime::RFC2822, $local, false);
  }
  
  /**
   * Gets the date as UNIX time stamp.
   * @return   integer  The date as a UNIX timestamp.
   */
  public function toUnix()
  {
    return (int) parent::format('U');
  }
  
  /**
   * Translation
   * 
   * @param   string  $key  The translation key
   * @return  string  Translated string or $key if not found
   */
  protected function translate($key)
  {
    $key = strtoupper($key);
    if ( isset(self::$translations[$key]) ){
      return self::$translations[$key];
    }
    
    return $key;
  }  
}
