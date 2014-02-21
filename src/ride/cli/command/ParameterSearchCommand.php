<?php

namespace ride\cli\command;

/**
 * Command to search for parameters in the configuration
 */
class ParameterSearchCommand extends AbstractConfigCommand {

    /**
     * Constructs a new parameter search command
     * @return null
     */
    public function __construct() {
        parent::__construct('parameter', 'Show an overview of the defined parameters');

        $this->addArgument('query', 'Query to search the parameters', false, true);
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $this->validateInstance();

    	$values = $this->config->getAll();
    	$values = $this->configHelper->flattenConfig($values);

    	$query = $this->input->getArgument('query');
    	if ($query) {
    		foreach ($values as $key => $value) {
    			if (stripos($key, $query) !== false) {
    				continue;
    			}

    			if (stripos($value, $query) !== false) {
    				continue;
    			}

    			unset($values[$key]);
    		}
    	}

    	ksort($values);

    	foreach ($values as $key => $value) {
    		$this->output->writeLine($key . ' = ' . $value);
    	}
    }

}