<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

/**
 * Xml object
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Xml 
{
  /**
   * Reads a XML file
   *
   * @param  string  $data  XML path or XML string
   * @return \SimpleXMLElement  Xml element
   * @throws \Exception
   * @see    \SimpleXMLElement
   */
  public static function populateXml($data)
  {
    $isFile = strlen($data) <= PHP_MAXPATHLEN && is_file($data);
    
    libxml_use_internal_errors(true);
    
    if ( true === $isFile ){
      $xml = simplexml_load_file($data, '\SimpleXMLElement');
    }
    else {
      $xml = simplexml_load_string($data, '\SimpleXMLElement');
    }
    
    if ( empty($xml) ){
      $errors=[];
      if ( true === $isFile ){
        $errors[] = $data;
      }
      
      foreach(libxml_get_errors() as $error){
        $errors[] = 'XML: '.$error->message;
      }
      
      throw new \Exception('XML file could not be loaded : '."\n".implode("\n", $errors));
    }
    
    return $xml;
  }
}
  
