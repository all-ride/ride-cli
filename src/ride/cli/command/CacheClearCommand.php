<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand;
use ride\library\dependency\DependencyInjector;

/**
 * Command to clear the cache
 */
class CacheClearCommand extends AbstractCommand {

    /**
     * Instance of the dependency injector
     * @var ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new cache command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('cache clear', 'Clears the cache');

        $this->addArgument('name', 'Name of the cache to clear', false);

        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $name = $this->input->getArgument('name');

        if ($name) {
            $control = $this->dependencyInjector->get('ride\\application\\cache\\control\\CacheControl', $name);
            $control->clear();
        } else {
            $controls = $this->dependencyInjector->getAll('ride\\application\\cache\\control\\CacheControl');
            foreach ($controls as $control) {
                $control->clear();
            }
        }
    }

}