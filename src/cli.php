<?php

$bootstrap = __DIR__ . '/../vendor/autoload.php';
$parameters = __DIR__ . '/../application/config/parameters.php';

try {
    // include the bootstrap
    include_once $bootstrap;

    // read the parameters
    if (file_exists($parameters)) {
        include_once $parameters;
    }

    if (!isset($parameters)) {
        $parameters = null;
    }

    // service the cli
    $system = new pallo\application\system\System($parameters);
    $system->service('cli');

    exit(0);
} catch (Exception $exception) {
    // error occured
    $output = "Fatal error: " . $exception->getMessage() . "\n";

    if (defined('STDERR')) {
        fwrite(STDERR, $output);
    } else {
        echo $output;
    }

    exit(1);
}