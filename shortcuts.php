<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Set date i18n
 * 
 * @param   array   $strings   Key/value pairs of translations
 * @param   bool    $default   Default translation if not set
 * @return  void
 * @author  Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function DateTranslate($strings, $default=false)
{
  \JDZ\Utilities\Date::setTranslations($strings);
  \JDZ\Utilities\Date::setTranslationDefaultValue('[**KEY**]');
}
