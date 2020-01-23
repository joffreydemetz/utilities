<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Utilities\Timer;

/**
 * Timer
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Timer 
{
  const TIME_DECIMALS  = 3;
  
  /**
   * Timer name
   * 
   * @var  string
   */
  public $name = '';
  
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
  // public $duration = 0;
  
  /**
   * List of step instances
   * 
   * @var  [Step]
   */
  public $steps = [];
  
  /**
   * Last marker
   * 
   * @var  float
   */
  public $markerSince = 0;
  
  public static function create(string $name='ROOT')
  {
    return new self($name);
  }
  
  public function __construct(string $name)
  {
    $this->name  = $name;
    $this->markerSince = $this->start = microtime(true);
  }
  
  public function reset()
  {
    $this->start = microtime(true);
    $this->end = null;
    // $this->duration = null;
    $this->markerSince = null;
    $this->steps = [];
    return $this;
  }
  
  public function setMarker(): string
  {
    $elapsed = number_format(microtime(true) - $this->markerSince, 4, '.', '');
    $this->markerSince = microtime(true);
    return str_pad($elapsed, 9, ' ', STR_PAD_LEFT);
  }
  
  public function getDuration(): float
  {
    $end = isset($this->end) ? $this->end : microtime(true);
    $duration = $end - $this->start;
    return $duration;
  }
  
  public function getTimeToLaunch(): float
  {
    if ( count($this->steps) ){
      return $this->steps[\array_key_first($this->steps)]->start - $this->start;
    }
    return 0;
  }
  
  public function getStepAverage(): float
  {
    $this->end();
    
    if ( count($this->steps) < 2 ){
      return $this->getDuration();
    }
    
    $duration = 0;
    foreach($this->steps as $step){
      $duration += $step->getDuration();
    }
    
    return $duration / count($this->steps);
  }
  
  public function isEmpty(): bool
  {
    return 0 === count($this->steps);
  }
  
  /**
   * @param  string  $label  Step title
   */
  public function addStep(string $label)
  {
    if ( count($this->steps) && false !== ($step=$this->currentStep()) ){
      $step->end();
    }
    
    $this->steps[md5(uniqid($label.'-', true))] = Step::create($label);
    return $this;
  }
  
  /**
   * Add timer to current step
   */
  public function addTimer(Timer $timer)
  {
    if ( false === ($step=$this->currentStep()) ){
      throw new \Exception('No current step');
    }
    
    $step->setTimer($timer);
    return $this;
  }
  
  /**
   * Add message to current step
   */
  public function addMessage(string $message)
  {
    if ( false === ($step=$this->currentStep()) ){
      throw new \Exception('No current step');
    }
    
    $step->addMessage($message);
    return $this;
  }
  
  /**
   * Add info to current step (single message)
   */
  public function setInfo(string $info)
  {
    if ( false === ($step=$this->currentStep()) ){
      throw new \Exception('No current step');
    }
    
    $step->setInfo($info);
    return $this;
  }
  
  /**
   * End current step
   */
  public function endStep()
  {
    if ( false === ($step=$this->currentStep()) ){
      throw new \Exception('No current step');
    }
    
    $step->end();
    return $this;
  }
  
  public function stepsToMessage()
  {
    $this->end();
    
    $dump = [];
    
    if ( !$this->isEmpty() ){
      foreach($this->steps as $step){
        $dump[] = trim($step->info.' '.$step->label);
      }
    }
    
    return $dump;
  }
  
  protected function currentStep()
  {
    if ( count($this->steps) ){
      return $this->steps[\array_key_last($this->steps)];
    }
    
    return false;
  }
  
  public function end()
  {
    if ( !$this->end ){
      if ( count($this->steps) && false !== ($step=$this->currentStep()) ){
        $step->end();
      }
      
      $this->end = microtime(true);
    }
    
    return $this;
  }
  
  public function dump()
  {
    $this->end();
    
    $dump = [];
    
    if ( $this->isEmpty() ){
      return $dump;
    }
    
    $duration = $this->end - $this->start;
    
    // ROOT dump
    if ( !\func_num_args() ){
      $rootStart = $this->start;
      $level = 1;
      
      $dump[] = '';
      $dump[] = '<info>Total: '.number_format($duration, self::TIME_DECIMALS, '.', '').'s</>';
      $dump[] = '';
      $dump[] = '<comment>'.str_pad('S', self::TIME_DECIMALS+5+2, ' ', STR_PAD_BOTH).' '.str_pad('D', self::TIME_DECIMALS+5+2, ' ', STR_PAD_BOTH).'</>';
      $dump[] = str_repeat('-', self::TIME_DECIMALS+5+2).' '.str_repeat('-', self::TIME_DECIMALS+5+2).' '.str_repeat('-', 30);
    }
    else {
      list($rootStart, $level) = \func_get_args();
      $indent = str_repeat(' ', (Timer::TIME_DECIMALS+5+3)*2).'| ';
      
      // $dump[] = '';
      $dump[] = '<info>'.$indent.strtoupper($this->name).' ('.number_format($duration, self::TIME_DECIMALS, '.', '').'s)</>';
    }
    
    foreach($this->steps as $step){
      $dump = array_merge($dump, $step->dump($rootStart, $level));
    }
    
    return $dump;
  }
}
