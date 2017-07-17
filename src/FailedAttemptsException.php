<?php

namespace memdevs\Attempt;


class FailedAttemptsException extends \Exception
{

  protected $attempts = 0;


  public function __construct($message = "", $attempts = 0, \Exception $previous = null)
  {
    $this->attempts = $attempts;
    parent::__construct($message, 0, $previous);
  }


  /**
   * @return int
   */
  public function getAttempts()
  {
    return $this->attempts;
  }


}