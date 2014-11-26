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
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new cache command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('cache clear', 'Clears the cache');

        $this->addArgument('name', 'Name of the cache to clear', false);
        $this->addFlag('skip', 'Name of the caches, separated by a comma, to skip when clearing');

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

            return;
        }

        $skip = $this->input->getFlag('skip');
        $skip = explode(',', $skip);

        $controls = $this->dependencyInjector->getAll('ride\\application\\cache\\control\\CacheControl');
        foreach ($controls as $name => $control) {
            if (in_array($name, $skip)) {
                continue;
            }

            $control->clear();
        }
    }

}