<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

use JDZ\Registry\Registry;

/**
 * Base Data Object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DataObject
{
  use \JDZ\Utilities\Traits\Get,
      \JDZ\Utilities\Traits\Set,
      \JDZ\Utilities\Traits\Error;
  
  /**
   * Constructor 
   * 
   * @param   array  $properties  Key/Value pairs.
   */
  public function __construct(array $properties=[])
  {
    if ( $properties !== null ){
      $this->setProperties($properties);
    }
    
    $this->clean();
  }
  
  /**
   * Clean object
   * 
   * @return   void
   */
  protected function clean()
  {
    if ( $params = $this->get('params') ){
      $this->setParams($params);
    }
  }
  
  /**
   * Convert json params to array
   * 
   * @param   string    $params   Json encoded data
   * @return   void
   */
  protected function setParams($params)
  {
    $registry = new Registry();
    $registry->loadString($params);
    $this->set('params', $registry->toArray());
  }
}
