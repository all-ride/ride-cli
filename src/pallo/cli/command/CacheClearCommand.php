<?php

namespace pallo\cli\command;

use pallo\library\cli\command\AbstractCommand;
use pallo\library\dependency\DependencyInjector;

/**
 * Command to clear the cache
 */
class CacheClearCommand extends AbstractCommand {

    /**
     * Instance of the dependency injector
     * @var pallo\library\dependency\DependencyInjector
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
        $name = $input->getArgument('name');

        if ($name) {
            $control = $this->dependencyInjector->get('pallo\\application\\cache\\control\\CacheControl', $name);
            $control->clear();
        } else {
            $controls = $this->dependencyInjector->getAll('pallo\\application\\cache\\control\\CacheControl');
            foreach ($controls as $control) {
                $control->clear();
            }
        }
    }

}