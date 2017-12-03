<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Traits;

use Exception;

/**
 * Error trait
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
trait Error
{
  /** 
   * An array of error messages or Exception objects
   * 
   * @var   array 
   */ 
  protected $errors = [];
  
  /**
   * Return all errors, if any, as a unique string.
   * 
   * @param   string   $separator     The separator.
   * @return   string   String containing all the errors separated by the specified sequence.
   */
  public function getErrorsAsString($separator='<br />')
  {
    $errors = $this->errors;
    
    foreach($errors as &$error){
      $error = $error->getMessage();
    }
    
    return implode($separator, $errors);
  }
  
  /**
   * Return all errors, if any.
   * 
   * @return   array  Array of error messages or Exception instances.
   */
  public function getErrors()
  {
    return $this->errors;
  }
  
  /**
   * Get an error message.
   *
   * @param   integer  $i         Option error index.
   * @param   boolean  $toString  Indicates if Exception instances should return the error message or the exception object.
   * @return   string   Error message
   */
  public function getError($i=null, $toString=true)
  {
    if ( $i === null ){
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
   * @param   mixed  $error  Error message or exception instance
   * @return   void
   */
  public function setError($error)
  {
    if ( !($error instanceof Exception) && is_string($error) ){
      $error = new Exception($error);
    }
    
    array_push($this->errors, $error);
  }
}
