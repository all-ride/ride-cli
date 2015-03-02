<?php

$bootstrap = __DIR__ . '/../vendor/autoload.php';
$parameters = __DIR__ . '/../application/config/parameters.php';

try {
    // parse global flags
    $isDebug = false;
    $env = null;
    foreach ($_SERVER['argv'] as $index => $value) {
        if ($value == '--debug') {
            $isDebug = true;
        } elseif (strpos($value, '--env=') === 0) {
            unset($_SERVER['argv'][$index]);

            $env = substr($value, 6);
        }
    }

    // include the bootstrap
    include_once $bootstrap;

    // read the parameters
    if (file_exists($parameters)) {
        include_once $parameters;
    }

    if (!is_array($parameters)) {
        $parameters = null;
    }

    // override environment
    if ($env) {
        $parameters['environment'] = $env;
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
