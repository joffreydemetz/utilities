<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities;

/**
 * Timer
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Timer 
{
  public $parent;
  
  /**
   * Timer name
   * 
   * @var  string
   */
  public $name;
  
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
  public $end = 0;
  
  /**
   * Timer duration
   * 
   * @var  float
   */
  public $duration = 0;
  
  /**
   * List of step instances
   * 
   * @var  Step[]
   */
  public $steps = [];
  
  /**
   * Timer level
   * 
   * @var  int
   */
  public $level = 1;
  
  /**
   * Children timers
   * 
   * @var  Timer[]
   */
  public $timers = [];
  
  /**
   * Timer instances
   * 
   * @var  Timer[]
   */
  public static $instances;
  
  public static function create(string $timer)
  {
    if ( !isset(self::$instances) ){
      self::$instances = [];
    }
    
    if ( !isset(self::$instances[$timer]) ){
      self::$instances[$timer] = new self();
      self::$instances[$timer]->setName($timer);
      self::$instances[$timer]->start();
    }
    
    return self::$instances[$timer];
  }
  
  public function setParent(Timer $parent)
  {
    $this->parent = $parent;
    return $this;
  }
  
  public function setName(string $name)
  {
    $this->name = $name;
    return $this;
  }
  
  public function setLevel(int $level)
  {
    $this->level = $level;
    return $this;
  }
  
  public function addTimer(Timer $timer, ?string $step=null)
  {
    if ( !$step ){
      $step = \array_key_last($this->steps);
    }
    
    if ( !$step ){
      $this->addStep('Entry point');
    }
    elseif ( empty($this->steps[$step]) ){
      $this->addStep($step);
    }
    
    $timer->setLevel($this->level+1);
    $this->steps[$step]->timers[] = $timer;
    
    return $this;
  }
  
  public function getFullDuration(): float
  {
    $duration = $this->end - $this->getRootStart();
    foreach($this->timers as $timer){
      $duration = $timer->getFullDuration();
    }
    return $duration;
  }
  
  public function getRootStart(): float
  {
    if ( isset($this->parent) ){
      return $this->parent->getRootStart();
    }
    
    return $this->start;
  }
  
  public function isEnded(): bool
  {
    return isset($this->end);
  }
  
  public function start()
  {
    $this->start = microtime(true);
    return true;
  }
  
  /**
   * @param  string       $label  Step title
   * @param  string|null  $key    Optionnal step alias (used to append timers)
   * @return string $key  The generated $key
   */
  public function step(string $label, ?string $key=null): string
  {
    $rootStart = $this->getRootStart();
    
    // end previous step
    if ( count($this->steps) ){
      foreach($this->steps as $step){
        if ( empty($step->end) ){
          $step->end = microtime(true);
        }
      }
    }
    
    if ( !$key ){
      $key = md5(uniqid($label.'-', true));
    }
    
    $this->steps[$key] = (object)[
      'start' => microtime(true),
      'end' => 0,
      // 'duration' => 0,
      'label' => $label,
      'messages' => [],
      'timers' => [],
    ];
    
    return $key;
  }
  
  public function addMessageToCurrentStep(string $message)
  {
    if ( count($this->steps) ){
      $key = \array_key_last($this->steps);
      $this->steps[$key]->messages[] = $message;
    }
    
    return $this;
  }
  
  public function end()
  {
    if ( count($this->steps) ){
      $key = \array_key_last($this->steps);
      if ( 0 === $this->steps[$key]->end ){
        $this->steps[$key]->end = microtime(true);
      }
    }
    
    foreach($this->steps as $step){
      foreach($step->timers as $timer){
        $timer->end();
      }
    }
    
    if ( !isset($this->end) ){
      $this->end = microtime(true);
      // $this->duration = $this->end - $this->start;
    }
    
    return $this;
  }
  
  public function dump()
  {
    $this->decimals  = 3;
    $this->colSize   = $this->decimals + 3 + 2; // hundreds, spaces
    
    $this->end();
    
    if ( $this->level > 2 ){
      $indent = '| '.str_repeat('--', $this->level);
    }
    else {
      $indent = '';
    }
    
    $rootStart = $this->getRootStart();
    
    $dump = [];
    
    if ( $this->level === 1 ){
      $dump[] = '';
      $dump[] = '<info>Total: '.number_format($this->getFullDuration(), $this->decimals, '.', '').'s</>';
      $dump[] = '';
      $dump[] = str_pad('S', $this->colSize+2, ' ', STR_PAD_BOTH).' '.str_pad('D', $this->colSize+2, ' ', STR_PAD_BOTH);
      $dump[] = str_repeat('-', $this->colSize+2).' '.str_repeat('-', $this->colSize+2).' '.str_repeat('-', 30);
    }
    else {
      $dump[] = strtoupper($this->name).': '.number_format($this->getFullDuration(), $this->decimals, '.', '').'s';
    }
    
    foreach($this->steps as $key => $step){
      $step->elapsed   = $step->start - $rootStart;
      $step->duration  = $step->end - $step->start;
      
      $elapsed  = number_format($step->elapsed, $this->decimals, '.', '');
      $duration = number_format($step->duration, $this->decimals, '.', '');
      
      $dump[] = '['.str_pad($elapsed, $this->colSize, ' ', STR_PAD_LEFT).'] ['.str_pad($duration, $this->colSize, ' ', STR_PAD_LEFT).'] '.$indent.' '.$step->label;
      
      foreach($step->messages as $message){
        $dump[] = str_repeat(' ', 24).$indent.' * '.$message;
      }
      
      foreach($step->timers as $timer){
        $timer->end();
        $dump[] = '';
        $dump = array_merge($dump, $timer->dump());
        $dump[] = '';
      }
    }
    
    return $dump;
  }
}
