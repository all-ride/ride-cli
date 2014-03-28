<?php

namespace ride\cli;

use ride\application\system\System;
use ride\application\Application;

use ride\library\cli\exception\CliException;
use ride\library\cli\output\Output;
use ride\library\cli\Cli;

use \Exception;

/**
 * CLI application
 */
class CliApplication implements Application {

    /**
     * Instance of the CLI
     * @var \ride\library\cli\Cli
     */
    protected $cli;

    /**
     * Instance of the system
     * @var \ride\library\system\System
     */
    protected $system;

    /**
     * Constructs a new CLI application
     * @param \ride\library\system\System $system
     * @param \ride\library\cli\Cli $cli
     * @return null
     */
    public function __construct(System $system, Cli $cli) {
        $this->system = $system;
        $this->cli = $cli;
    }

    /**
     * Service the application
     * @return null
     */
    public function service() {
        // check the system
        if (!$this->system->isCli()) {
            throw new CliException('Could not service the CLI: CLI should be run from a shell');
        }

        $dependencyInjector = $this->system->getDependencyInjector();

        // load commands
        $commandContainer = $this->cli->getCommandInterpreter()->getCommandContainer();

        $commands = $dependencyInjector->getAll('ride\\library\\cli\\command\\Command');
        foreach ($commands as $command) {
            $commandContainer->addCommand($command);
        }

        // remove the script from the arguments
        $script = $_SERVER['argv'][0];
        unset($_SERVER['argv'][0]);

        // check for the debug flag
        if (in_array('--debug', $_SERVER['argv']) !== false) {
            // remove flag from script arguments
            foreach ($_SERVER['argv'] as $index => $value) {
                if ($value == '--debug') {
                    unset($_SERVER['argv'][$index]);
                }
            }

            // set CLI to debug
            $this->cli->setIsDebug(true);

            // add echo log listener to the log
            $echoLogListener = $dependencyInjector->get('ride\\library\\log\\listener\\LogListener', 'echo');

            $log = $dependencyInjector->get('ride\\library\\log\\Log');
            $log->addLogListener($echoLogListener);
        }

        // set the input and output to the CLI
        try {
            $input = $dependencyInjector->get('ride\\library\\cli\\input\\Input', 'readline');
        } catch (Exception $exception) {
            $input = $dependencyInjector->get('ride\\library\\cli\\input\\Input', 'php');
        }

        $output = $dependencyInjector->get('ride\\library\\cli\\output\\Output');

        $this->cli->setInput($input);
        $this->cli->setOutput($output);

        if (in_array('--shell', $_SERVER['argv']) != false) {
            // input through a interactive shell

            // remove flag from script arguments
            foreach ($_SERVER['argv'] as $index => $value) {
                if ($value == '--shell') {
                    unset($_SERVER['argv'][$index]);
                }
            }

            // no other arguments are allowed
            if ($_SERVER['argv']) {
                throw new CliException("Could not start the interactive shell: invalid arguments or flags provided (" . implode(' ', $_SERVER['argv']) . ")");
            }

            // write the intro
            $this->writeSystemHeader($output);

            $output->writeLine('Type \'help\' to get you started.');
        } else {
            $input = $dependencyInjector->get('ride\\library\\cli\\input\\Input', 'argument');

            // check for the batch flag
            if (in_array('--batch', $_SERVER['argv']) !== false) {
                // remove flag from script arguments
                foreach ($_SERVER['argv'] as $index => $value) {
                    if ($value == '--batch') {
                        unset($_SERVER['argv'][$index]);
                    }
                }

                // set a non interactive input
                $this->cli->setInput($input);
            }

            // input from command arguments
            if (!$_SERVER['argv']) {
                $this->writeSystemHeader($output);

                $output->writeLine('');
                $output->writeLine('Usage:');
                $output->writeLine($script . ' [options] [<command>]');
                $output->writeLine('');
                $output->writeLine('Available options:');
                $output->writeLine('- --batch  Use a non-interactive input.');
                $output->writeLine('- --debug  Show the full stack trace of runtime exceptions.');
                $output->writeLine('- --shell  Run as a (interactive) shell.');
                $output->writeLine('');
                $output->writeLine('If you are in a interactive shell, you can use tab for command auto completion and the up and down arrows for command history.');
                $output->writeLine('');
            }
        }

        // run the CLI
        $this->cli->run($input);

        exit($this->cli->getExitCode());
    }

    /**
     * Writes the system name and environment to the provided output
     * @param \ride\library\cli\output\Output $output
     * @return null
     */
    protected function writeSystemHeader(Output $output) {
        $environment = $this->system->getEnvironment();
        if ($environment != 'prod') {
            $environment = ' (' . $environment . ')';
        } else {
            $environment = '';
        }

        $output->writeLine($this->system->getName() . $environment);
    }

}