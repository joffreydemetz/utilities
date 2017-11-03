<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Traits;

/**
 * Set trait
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
trait Set
{
	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param 	mixed  $properties  Either an associative array or another object.
	 * @return 	boolean
	 */
	public function setProperties($properties)
	{
		if ( is_array($properties) || is_object($properties) ){
			foreach((array)$properties as $k => $v){
				$this->set($k, $v);
			}
			return true;
		}

		return false;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param 	string  $property  The name of the property.
	 * @param 	mixed   $value     The value of the property to set.
	 * @return 	mixed  Previous value of the property.
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}

	/**
	 * Clears a property.
	 *
	 * @param 	string  $property  The name of the property.
	 * @return 	void
	 */
	public function erase($property)
	{
    if ( isset($this->$property) ){
      unset($this->$property);
    }
  }
}
