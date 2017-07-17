<?php

namespace memdevs\Attempt;


/**
 * Class Attempt
 *
 * Features:
 * Stores all exceptions and have the ability to retrieve them
 * Can delay for x milliseconds between each retry
 * Alternatively include function to be called after each exception, and enable breaking out of retry loop if function returns a "true" value
 * This means we can intercept the last failure and return gracefully with a default value rather than producing an exception, if desired.
 *
 * If function completed successfully, regardless of how many attempts, Attempt::failed() = false and Attempt::succeeded() = true
 * Whether or not function completed successfully, Attempt::hasExceptions() indicates whether function failed at least once.
 * Attempt::failureCount returns how many times the function failed, and 0 if no failures
 *
 * Include a delay timer or a callable function that allows you to break off the processing.
 *
 * @package memdevs\Attempt
 */
class Attempt
{

  static protected $exceptions = [];
  static protected $failed = false;
  /** @var mixed */
  static protected $failed_result = null;


  /** @noinspection PhpInconsistentReturnPointsInspection */
  /**
   * Attempts a callable function at least once, with $attempts number of actual attempts.
   *
   * @param integer $attempts
   * @param callable $fn
   * @param callable|int $on_error_action Callable called every time an exception occurs, OR how long to sleep between tries, in milliseconds. So 1000 = 1 second
   * @return mixed
   * @throws FailedAttemptsException
   */
  public static function execute($attempts, callable $fn, $on_error_action = 0)
  {
    self::clearExceptions();
    self::$failed = true; // Let's assume the result is failure
    self::$failed_result = null; // If a failed result, set null as the default return value

    if ($attempts < 1) {
      throw new FailedAttemptsException('Must attempt the function at least once');
    }

    $attempt = 0;
    while (true) {
      try {
        $attempt++;
        $return_value = $fn($attempt, $attempts - $attempt);
        self::$failed = false; // SUCCESS - so clear the failure flag

        return $return_value;
      } catch (\Exception $e) {

        self::addException($e);

        if (is_callable($on_error_action)) {
          if ($on_error_action($e, $attempt, $attempts - $attempt)) { // Stop processing if function returns a "true" value
            // Assume the callable has handled error reporting back to the code, or use
            // Attempt::failed() to detect a never-successful result
            // Note that this callable can fake a result by calling Attempt::setFailedResult()
            // This is handy to set an empty array as the failed result, for instance
            return self::getFailedResult(); // Return null by default to signify an "undefined" result
          };
        }

        if ($attempts == $attempt) {
          throw new FailedAttemptsException('Failed attempts', $attempts, $e); // Include LAST function exception for chaining
        }

        if (is_int($on_error_action) && $on_error_action) {
          usleep($on_error_action * 1000);
        }
      }
    }
  }


  /**
   * @return array
   */
  public static function getExceptions()
  {
    return self::$exceptions;
  }


  public static function failureCount()
  {
    return count(self::$exceptions);
  }


  public static function failed()
  {
    return self::$failed;
  }


  public static function succeeded()
  {
    return !self::failed();
  }


  /**
   * @param \Exception $exceptions
   */
  protected static function addException(\Exception $exceptions)
  {
    self::$exceptions[] = $exceptions;
  }


  protected static function clearExceptions()
  {
    self::$exceptions = [];
  }


  /**
   * @return mixed
   */
  protected static function getFailedResult()
  {
    return self::$failed_result;
  }


  /**
   * @param mixed $failed_result
   */
  public static function setFailedResult($failed_result)
  {
    self::$failed_result = $failed_result;
  }


}