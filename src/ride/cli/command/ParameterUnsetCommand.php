<?php

namespace ride\cli\command;

/**
 * Command to unset a configuration parameter
 */
class ParameterUnsetCommand extends AbstractConfigCommand {

    /**
     * Constructs a new parameter unset command
     * @return null
     */
    public function __construct() {
        parent::__construct('parameter unset', 'Unsets a parameter');

        $this->addArgument('key', 'Key of the parameter');
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $this->validateInstance();

    	$key = $this->input->getArgument('key');

    	$this->config->set($key, null);
    }

}