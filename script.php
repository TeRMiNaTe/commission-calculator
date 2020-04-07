<?php

require __DIR__ . '/index.php';

// Read file argument from CLI and proccess the commission file
if (isset($argv[1])) {
    $input_file = $argv[1];

    $app['managers']['transaction']->processCommissionFile($input_file);
} else {
    fwrite(STDERR, 'Error! Missing name of input file.');
}
