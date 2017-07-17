<?php

use memdevs\Attempt\Attempt;


include '../vendor/autoload.php';

/**
 * More advanced example
 * 1. Fail the function every time
 * 2. On each error, call a closure
 * 3. On the third attempt, set a new return value and return gracefully
 */

$now = time();

$result = null;
try {
  // Function will be executed 3 times
  $result = Attempt::execute(3,
    function ($attempt) {
      // Let's fail this every time
      throw new \InvalidArgumentException('Failed attempt ' . $attempt);
    },
    // This function is executed every time an attempt fails
    function (\Exception $e, $attempt, $tries_left) {
      print "<p>Attempt {$attempt} failed with error <strong>" . $e->getMessage() . "</strong> and {$tries_left} attempt(s) left</p>" . PHP_EOL;
      if ($tries_left == 0) { // This is only executed after the LAST failed attempt
        Attempt::setFailedResult('This is a graceful return of a value');

        return true; // Make sure the function exits without futher error processing
      }

      return false;
    }
  );
} catch (Exception $e) {
  print "<p><strong>" . $e->getMessage() . "</strong></p>" . PHP_EOL;
}

// "true" - yes, the function still FAILED, we just overrode it
var_dump(Attempt::failed());
print "<hr>" . PHP_EOL;

// "false" - see above
var_dump(Attempt::succeeded());
print "<hr>" . PHP_EOL;

// 3 (as we had 3 failures)
var_dump(Attempt::failureCount());
print "<hr>" . PHP_EOL;

// 'This is a graceful return of a value' as the function was set in line 29 above
var_dump($result);
print "<hr>" . PHP_EOL;

// 3 InvalidArgumentExceptions
var_dump(Attempt::getExceptions());
