<?php

$bootstrap = __DIR__ . '/../vendor/autoload.php';
$parameters = __DIR__ . '/../application/config/parameters.php';

try {
    $isDebug = in_array('--debug', $_SERVER['argv']);

    // include the bootstrap
    include_once $bootstrap;

    // read the parameters
    if (file_exists($parameters)) {
        include_once $parameters;
    }

    if (!is_array($parameters)) {
        $parameters = null;
    }

    // service the cli
    $system = new ride\application\system\System($parameters);
    $system->setTimeZone();
    $system->service('cli');

    exit(0);
} catch (Exception $exception) {
    // error occured
    if ($isDebug !== false) {
        $output = "Fatal error:\n\n" . get_class($exception) . ': ' . $exception->getMessage() . "\n";
        $output .= "\n" . $exception->getTraceAsString() . "\n";

        do {
            $exception = $exception->getPrevious();
            if ($exception) {
                $output .= "\nCaused by:\n\n" . get_class($exception) . ': ' . $exception->getMessage() . "\n";
                $output .= "\n" . $exception->getTraceAsString() . "\n";
            }
        } while ($exception);
    } else {
        $output = "Fatal error: " . $exception->getMessage() . "\n";
    }

    if (defined('STDERR')) {
        fwrite(STDERR, $output);
    } else {
        echo $output;
    }

    exit(1);
}
