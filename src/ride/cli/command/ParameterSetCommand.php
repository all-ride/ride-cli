<?php

namespace ride\cli\command;

/**
 * Command to set a configuration parameter
 */
class ParameterSetCommand extends AbstractConfigCommand {

    /**
     * Constructs a new parameter set command
     * @return null
     */
    public function __construct() {
        parent::__construct('parameter set', 'Sets a parameter');

        $this->addArgument('key', 'Key of the parameter');
        $this->addArgument('value', 'Value for the parameter', true, true);
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $this->validateInstance();

    	$key = $this->input->getArgument('key');
    	$value = $this->input->getArgument('value');

    	$this->config->set($key, $value);
    }

}