<?php

try {
    include_once __DIR__ . '/../vendor/autoload.php';

    $system = new pallo\app\system\System();
    $system->service('cli');

    exit(0);
} catch (Exception $exception) {
    $output = "Fatal error: " . $exception->getMessage() . "\n";

    if (defined('STDERR')) {
        fwrite(STDERR, $output);
    } else {
        echo $output;
    }

    exit(1);
}