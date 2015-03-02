<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand as LibAbstractCommand;
use ride\library\dependency\DependencyInjector;

/**
 * Abstract command with dependency injection support
 */
abstract class AbstractCommand extends LibAbstractCommand {

    /**
     * Instance of the dependencyInjector
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Predefined arguments for the callback
     * @var array|null
     */
    protected $predefinedArguments;

    /**
     * Constructs a new abstract command
     * @param \ride\library\dependency\DependencyInjector $dependencyInjector
     * @param string $name Name of the command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('dummy');

        $this->dependencyInjector = $dependencyInjector;

        $this->initialize();
    }

    /**
     * Hook to initialize the command without dealing with the constructor
     * @return null
     */
    protected function initialize() {

    }

	/**
	 * Sets the name of this command
	 * @param string $name
	 * @return null
	 * @throws \ride\library\cli\exception\CliException when the name is empty
     * or invalid
	 */
	public function setName($name) {
        parent::setName($name);
    }

	/**
	 * Sets the aliases of this command
	 * @param array $aliases Array with aliases
	 * @return null
	 */
	public function setAliases(array $aliases) {
        $this->aliases = array();

        foreach ($aliases as $alias) {
            $this->aliases[$alias] = true;
        }
    }

    /**
     * Sets the predefined arguments for the callback
     * @param array $arguments
     * @return null
     */
    public function setPredefinedArguments(array $arguments = null) {
        $this->predefinedArguments = $arguments;
    }

    /**
     * Gets the predefined arguments for the action method
     * @return array Arguments for the callback
     */
    public function getPredefinedArguments() {
        if ($this->predefinedArguments === null) {
            return array();
        }

        return $this->predefinedArguments;
    }

	/**
	 * Executes the command
	 * @return null
	 */
    public function execute() {
        $callback = array($this, 'invoke');
        $arguments = $this->getPredefinedArguments();

        foreach ($this->arguments as $argument) {
            $argumentName = $argument->getName();
            $argumentValue = $this->input->getArgument($argumentName);

            if ($argumentValue !== null) {
                $arguments[$argumentName] = $argumentValue;
            }
        }

        foreach ($this->flags as $flag => $description) {
            $flagValue = $this->input->getFlag($flag);

            if ($flagValue !== null) {
                $arguments[$flag] = $flagValue;
            }
        }

        $this->dependencyInjector->invoke($callback, $arguments);
    }

}
