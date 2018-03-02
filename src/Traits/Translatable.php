<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Traits;

/**
 * Translatable trait
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 * @deprecated
 */
trait Translatable 
{
  /**
   * Holds an array of translations
   * 
   * @var   array
   */
  protected static $translations;
  
  /**
   * The default value when translation is not available
   * 
   * [**KEY**] will return the original key
   * 
   * @var   mixed
   */
  protected static $translationDefaultValue = false;
  
  /**
   * Set the default value 
   *
   * @param   mixed   $translationDefaultValue     false, [**KEY**], '', ..
   * @return   void
   */
  public static function setTranslationDefaultValue($translationDefaultValue='Unknown Error')
  {
    self::$translationDefaultValue = $translationDefaultValue;
  }
  
  /**
   * Set the translations
   *
   * @param   array   $translations     Key/value pairs of translations
   * @return   void
   */
  public static function setTranslations(array $translations=[])
  {
    if ( !isset(self::$translations) ){
      self::$translations = [];
    }
    
    if ( count($translations) ){
      self::$translations = array_merge(self::$translations, $translations);
    }
  }
  
  /**
   * Get a translation
   * 
   * @param   string        $key      The translation key to look for
   * @return   string|false  Translated string or false if not found
   */
  public static function getTranslation($key)
  {
    // ensure the translations were loaded
    self::setTranslations();
    
    $key = strtoupper($key);
    if ( isset(self::$translations[$key]) ){
      return self::$translations[$key];
    }
    
    // return original key (could not be already translated)
    // @toto maybe check if a key exists for that translated string
    if ( self::$translationDefaultValue === '[**KEY**]' ){
      return $key;
    }
    
    // if the default value is another translation
    if ( is_string(self::$translationDefaultValue) && isset(self::$translations[self::$translationDefaultValue]) ){
      return self::$translations[self::$translationDefaultValue];
    }
    
    return self::$translationDefaultValue;
  }  
}
