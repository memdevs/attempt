<?php

use memdevs\Attempt\Attempt;


include '../vendor/autoload.php';

/**
 * Simple example
 * Fail twice, then return a value on the last attempt
 */

$result = null;
try {
  // Function will be executed 3 times
  $result = Attempt::execute(3,
    function ($attempt, $tries_left) {
      if ($tries_left == 0) {
        return 'Success!';
      }
      throw new \InvalidArgumentException('Failed attempt ' . $attempt);
    });
} catch (Exception $e) {
  print "<p><strong>" . $e->getMessage() . "</strong></p>" . PHP_EOL;
}

// "false" - because in the end, we did succeed
var_dump(Attempt::failed());
print "<hr>" . PHP_EOL;

// "true" - see above
var_dump(Attempt::succeeded());
print "<hr>" . PHP_EOL;

// 2 (as we had 2 failures; the third attempt was successful)
var_dump(Attempt::failureCount());
print "<hr>" . PHP_EOL;

// 'Success!' as the function never executes correctly
var_dump($result);
print "<hr>" . PHP_EOL;

// 2 InvalidArgumentExceptions
var_dump(Attempt::getExceptions());
