<?php

use memdevs\Attempt\Attempt;


include '../vendor/autoload.php';

/**
 * Very simple example
 * Simply fail the function every time
 */

$result = null;
try {
  // Function will be executed 3 times
  $result = Attempt::execute(3,
    function ($attempt) {
      // Let's fail this every time
      throw new \InvalidArgumentException('Failed attempt ' . $attempt);
    });
} catch (Exception $e) {
  print "<p><strong>" . $e->getMessage() . "</strong><br>" . PHP_EOL;
}

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
