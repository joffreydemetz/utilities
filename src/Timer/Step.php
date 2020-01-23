<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Timer;

/**
 * Timer Step
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Step 
{
  /**
   * Timer name
   * 
   * @var  string
   */
  public $label = '';
  
  /**
   * Label complementary info
   * 
   * @var  string
   */
  public $info = '';
  
  /**
   * Start point
   * 
   * @var  float
   */
  public $start = 0;
  
  /**
   * End point
   * 
   * @var  float
   */
  public $end = null;
  
  /**
   * Timer instance
   * 
   * @var  Timer
   */
  public $timer = null;
  
  /**
   * List of step message
   * 
   * @var  [string]
   */
  public $messages = [];
  
  public static function create(string $label)
  {
    return new self($label);
  }
  
  public function __construct(string $label)
  {
    $this->label = $label;
    $this->start = microtime(true);
  }
  
  public function getDuration(): float
  {
    $end = isset($this->end) ? $this->end : microtime(true);
    $duration = $end - $this->start;
    return $duration;
    // return round(min($duration, 0.000001), 6);
  }
  
  public function setTimer(Timer $timer)
  {
    $this->timer = $timer;
    return $this;
  }
  
  public function setInfo(string $info)
  {
    $this->info = $info;
    return $this;
  }
  
  public function addMessage(string $message)
  {
    $this->messages[] = $message;
    return $this;
  }
  
  public function isEnded()
  {
    return null !== $this->end;
  }
  
  public function end()
  {
    if ( !$this->end ){
      if ( $this->timer ){
        // end inner timer
        $this->timer->end();
        
        // remove empty inner timer
        if ( $this->timer->isEmpty() ){
          $this->timer = null;
        }
      }
      
      $this->end = microtime(true);
    }
    
    return $this;
  }
  
  public function dump(float $rootStart, int $level)
  {
    $indent   = $level < 2 ? '' : '|'.str_repeat('-', $level-1);
    $elapsed  = number_format($this->start - $rootStart, Timer::TIME_DECIMALS);
    $duration = number_format($this->end - $this->start, Timer::TIME_DECIMALS);
    
    if ( isset($this->info) ){
      $label = str_pad($this->label, 20, ' ', STR_PAD_RIGHT).' - '.$this->info;
    }
    else {
      $label = $this->label;
    }
    
    if ( $this->timer ){
      $duration = str_repeat(' ', Timer::TIME_DECIMALS+7);
    }
    else {
      $duration = '['.str_pad($duration, Timer::TIME_DECIMALS+5, ' ', STR_PAD_LEFT).']';
    }
    
    $dump[] = '['.str_pad($elapsed, Timer::TIME_DECIMALS+5, ' ', STR_PAD_LEFT).'] '.$duration.' '.($indent?$indent.' ':'').$label;
    
    foreach($this->messages as $message){
      $dump[] = str_repeat(' ', (Timer::TIME_DECIMALS+5+3)*2).$indent.'+ '.$message;
    }
    
    if ( $this->timer ){
      $dump = array_merge($dump, $this->timer->dump($rootStart, $level+1));
    }
    
    return $dump;
  }
}
