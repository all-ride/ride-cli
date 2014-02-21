<?php

namespace ride\cli\command;

/**
 * Command to get a configuration parameter
 */
class ParameterGetCommand extends AbstractConfigCommand {

    /**
     * Constructs a new parameter get command
     * @return null
     */
    public function __construct() {
        parent::__construct('parameter get', 'Gets the value of a parameter');

        $this->addArgument('key', 'Key of the parameter');
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $this->validateInstance();

    	$key = $this->input->getArgument('key');

    	$value = $this->config->get($key);

    	$this->output->writeLine(var_export($value, true));
    }

}