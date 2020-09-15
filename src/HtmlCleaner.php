<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

/**
 * HTML cleaner
 * 
 * Clean bad styles and classes from html content
 * For example for ms office copy paste
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class HtmlCleaner
{
  public $content;
  
  protected $wordsOrKeywords = [];
   
  protected $classAttributeValues = [
    'MsoNormal',
    'MsoBodyText',
    'MsoListParagraph'
  ];
   
  protected $styleAttributeProperties = [
    'font-family',
    'font-size',
    'font-variant',
    'color',
  ];
   
  public function __construct(string $content)
  {
    $this->content = $content;
  }
  
  public function setWordsOrKeywords(array $wordsOrKeywords=[])
  {
    $this->wordsOrKeywords = $wordsOrKeywords;
    return $this;
  }
  
  public function setClassAttributeValues(array $classAttributeValues=[])
  {
    $this->classAttributeValues = $classAttributeValues;
    return $this;
  }
  
  public function setStyleAttributeProperties(array $styleAttributeProperties=[])
  {
    $this->styleAttributeProperties = $styleAttributeProperties;
    return $this;
  }
  
  public function addWordsOrKeywords(array $wordsOrKeywords=[])
  {
    $this->wordsOrKeywords = array_merge($this->wordsOrKeywords, $wordsOrKeywords);
    return $this;
  }
  
  public function addClassAttributeValues(array $classAttributeValues=[])
  {
    $this->classAttributeValues = array_merge($this->classAttributeValues, $classAttributeValues);
    return $this;
  }
  
  public function addStyleAttributeProperties(array $styleAttributeProperties=[])
  {
    $this->styleAttributeProperties = array_merge($this->styleAttributeProperties, $styleAttributeProperties);
    return $this;
  }
  
  public function clean()
  {
    if ( $this->classAttributeValues ){
      $this->content = str_replace($this->classAttributeValues, '', $this->content);
    }
    
    if ( $this->wordsOrKeywords ){
      $this->content = str_replace($this->wordsOrKeywords, '', $this->content);
    }
    
    $this->content = mb_ereg_replace('('.implode('|', $this->styleAttributeProperties).')\s*:\s*[^;"]+;?\s*', '', $this->content);
    $this->content = mb_ereg_replace(' style="\s*"', '', $this->content);
    
    return $this->content;
  }
}
