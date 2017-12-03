<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Traits;

/**
 * Namespaced fetching
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
trait Ns 
{
  /**
   * Fields namepace
   * 
   * @var    string   
   */
  protected static $NS;
  
  /**
   * Set the field namespace
   * 
   * @param   string      $NS   The field namespace
   * @return   void
   */
  public static function setNamespace($NS)
  {
    self::$NS = $NS;
  }
}
