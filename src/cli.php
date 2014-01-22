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
    $system = new pallo\application\system\System($parameters);
    $system->service('cli');

    exit(0);
} catch (Exception $exception) {
    // error occured
    $output = "Fatal error:\n\n" . get_class($exception) . ': ' . $exception->getMessage() . "\n";
    if ($isDebug !== false) {
        $output .= "\n" . $exception->getTraceAsString() . "\n";
    }

    do {
        $exception = $exception->getPrevious();
        if ($exception) {
            $output .= "\nCaused by:\n\n" . get_class($exception) . ': ' . $exception->getMessage() . "\n";
            if ($isDebug !== false) {
                $output .= "\n" . $exception->getTraceAsString() . "\n";
            }
        }
    } while ($exception);

    if (defined('STDERR')) {
        fwrite(STDERR, $output);
    } else {
        echo $output;
    }

    exit(1);
}