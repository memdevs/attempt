<?php

use memdevs\Attempt\Attempt;


include '../vendor/autoload.php';

/**
 * Simple example
 * 1. Fail the function every time
 * 2. Wait 1 second between each attempt (1000 milliseconds)
 */

$now = time();

$result = null;
try {
  // Function will be executed 3 times
  $result = Attempt::execute(3,
    function ($attempt) {
      // Let's fail this every time
      throw new \InvalidArgumentException('Failed attempt ' . $attempt);
    }, 1000);
} catch (Exception $e) {
  print "<p><strong>" . $e->getMessage() . "</strong></p>" . PHP_EOL;
}

// 2 seconds will have elapsed. Why not 3 seconds? 3 attempts with 2 time intervals of 1 second each, hence 2 seconds
print "<p>" . (time() - $now) . ' seconds have elapsed</p>' . PHP_EOL;

// "true"
var_dump(Attempt::failed());
print "<hr>" . PHP_EOL;

// "false"
var_dump(Attempt::succeeded());
print "<hr>" . PHP_EOL;

// 3 (as we had 3 failures)
var_dump(Attempt::failureCount());
print "<hr>" . PHP_EOL;

// 'null' as the function never executes correctly
var_dump($result);
print "<hr>" . PHP_EOL;

// 3 InvalidArgumentExceptions
var_dump(Attempt::getExceptions());
