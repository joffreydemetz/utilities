<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

use \SimpleXMLElement;
use \Exception as XmlException;

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
	 * @param 	string            $data      XML path or XML string
	 * @return 	SimpleXMLElement  Xml element
	 * @throws 	XmlException
	 * @see     SimpleXMLElement
	 */
	public static function populateXml($data)
	{
    $isFile = ( is_file($data) );
		
    libxml_use_internal_errors(true);
    
		if ( $isFile === true ){
			$xml = simplexml_load_file($data, '\SimpleXMLElement');
		}
		else {
			$xml = simplexml_load_string($data, '\SimpleXMLElement');
		}
    
		if ( empty($xml) ){
      $errors=[];
			if ( $isFile === true ){
        $errors[] = $data;
			}
      
			foreach(libxml_get_errors() as $error){
        $errors[] = 'XML: '.$error->message;
			}
      
      throw new XmlException('XML file could not be loaded : '."\n".implode("\n", $errors));
		}
    
		return $xml;
	}
}
  
