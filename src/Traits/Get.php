<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Traits;

/**
 * Get trait
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
trait Get
{
  /**
   * Returns a property of the object or the default value if the property is not set.
   *
   * @param   string  $property  The name of the property.
   * @param   mixed   $default   The default value.
   * @return   mixed    The value of the property.
   */
  public function get($property, $default = null)
  {
    if ( isset($this->$property) ){
      return $this->$property;
    }
    return $default;
  }
  
  /**
   * Test if the property exists
   *
   * @param  string  $property  The name of the property.
   * @return bool
   */
  public function has($property)
  {
    return ( isset($this->{$property}) );
  }
  
  /**
   * Returns an associative array of object properties.
   *
   * @param   boolean  $public  If true, returns only the public properties.
   * @return   array
   */
  public function export()
  {
    return $this->getProperties();
  }
  
  /**
   * Returns an associative array of object properties.
   *
   * @param   boolean  $public  If true, returns only the public properties.
   * @return   array
   */
  public function getProperties($public=true)
  {
    $vars = get_object_vars($this);
    
    foreach($vars as $key => $value){
      if ( '_' === substr($key, 0, 1) ){
        unset($vars[$key]);
        continue;
      }
      
      if ( in_array($key, $this->filterGetProperties(['errors','db'])) ){
        unset($vars[$key]);
        continue;
      }
    }
    
    return $vars;
  }
  
  /**
   * Filter properties in getProperties()
   * 
   * @param   array  $properties  Array of properties to ignore during export
   * @return   array
   */
  public function filterGetProperties(array $properties=[])
  {
    return $properties;
  }
}
