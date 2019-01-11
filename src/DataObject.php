<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

/**
 * Base Data Object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DataObject
{
  /** 
   * An array of error messages or Exception objects
   * 
   * @var   array 
   */ 
  protected $errors = [];
  
  /**
   * Constructor 
   * 
   * @param  array  $properties  Key/Value pairs.
   */
  public function __construct(array $properties=[])
  {
    if ( $properties ){
      $this->setProperties($properties);
    }
  }
  
  /**
   * Set the object properties
   * 
   * @param   mixed  $properties  Either an associative array or another object
   * @return  $this
   * @deprecated 
   */
  public function setProperties($properties)
  {
    $this->sets((array)$properties);
    return $this;
  }
  
  /**
   * Set some object properties
   * 
   * @param   array  $properties  Key / Value pairs
   * @return  $this
   */
  public function sets(array $properties)
  {
    foreach($properties as $k => $v){
      $this->set($k, $v);
    }
    return $this;
  }
  
  /**
   * Modifies a property of the object, creating it if it does not already exist.
   *
   * @param  string  $property  The name of the property.
   * @param  mixed   $value     The value of the property to set.
   * @return $this
   */
  public function set(string $property, $value=null)
  {
    $this->{$property} = $value;
    return $this;
  }
  
  /**
   * Clears a property.
   *
   * @param  string  $property  The name of the property.
   * @return $this
   */
  public function erase(string $property)
  {
    if ( isset($this->{$property}) ){
      unset($this->{$property});
    }
    return $this;
  }
  
  /**
   * Returns a property of the object or the default value if the property is not set
   *
   * @param  string  $property  The name of the property
   * @param  mixed   $default   The default value
   * @return mixed The value of the property
   */
  public function get(string $property, $default=null)
  {
    if ( isset($this->{$property}) ){
      return $this->{$property};
    }
    return $default;
  }
  
  /**
   * Test if the property exists
   *
   * @param  string  $property  The name of the property.
   * @return bool
   */
  public function has($property): bool
  {
    return ( isset($this->{$property}) );
  }
  
  public function all(bool $object=true)
  {
    $data = $this->getProperties();
    if ( $object ){
      $data = (object)$data;
    }
    return $data;
  }
  
  /**
   * Returns an associative array of object properties.
   *
   * @return array
   */
  public function export(): array
  {
    return $this->all(false);
  }
  
  /**
   * Returns an associative array of object properties.
   *
   * @param   boolean  $public  If true, returns only the public properties.
   * @return   array
   */
  public function getProperties(bool $public=true): array
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
   * @param  array  $properties  Array of properties to ignore during export
   * @return array
   */
  public function filterGetProperties(array $properties=[]): array
  {
    return $properties;
  }
  
  /**
   * Return all errors, if any, as a unique string
   * 
   * @param  string  $separator  The separator
   * @return string  String containing all the errors separated by the specified sequence
   */
  public function getErrorsAsString(string $separator='<br />'): string
  {
    $errors = $this->errors;
    
    foreach($errors as &$error){
      $error = $error->getMessage();
    }
    
    return implode($separator, $errors);
  }
  
  /**
   * Return all errors, if any
   * 
   * @return array  Array of error messages or Exception instances
   */
  public function getErrors(): array
  {
    return $this->errors;
  }
  
  /**
   * Get an error message
   *
   * @param  integer  $i         Option error index
   * @return string   Error message
   */
  public function getError(?int $i=null): string
  {
    if ( null === $i ){
      $error = end($this->errors);
    }
    else {
      if ( !array_key_exists($i, $this->errors) ){
        return false;
      }
      
      $error = $this->errors[$i];
    }
    
    if ( $error instanceof Exception ){
      return $error->getMessage();
    }
    
    return $error;
  }
  
  /**
   * Add an error message.
   *
   * @param  mixed  $error  Error message or exception instance
   * @return $this
   */
  public function setError($error)
  {
    if ( !($error instanceof Exception) && is_string($error) ){
      $error = new Exception($error);
    }
    
    array_push($this->errors, $error);
    return $this;
  }
}
