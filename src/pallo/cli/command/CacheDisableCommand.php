<?php

namespace pallo\cli\command;

use pallo\library\cli\command\AbstractCommand;
use pallo\library\dependency\DependencyInjector;

/**
 * Command to disable the cache
 */
class CacheDisableCommand extends AbstractCommand {

    /**
     * Instance of the dependency injector
     * @var pallo\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new cache disable command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('cache disable', 'Disables the cache');

        $this->addArgument('name', 'Name of the cache to disable', false);

        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $name = $this->input->getArgument('name');

        if ($name) {
            $control = $this->dependencyInjector->get('pallo\\application\\cache\\control\\CacheControl', $name);
            if ($control->canToggle()) {
                $control->disable();
            }
        } else {
            $controls = $this->dependencyInjector->getAll('pallo\\application\\cache\\control\\CacheControl');
            foreach ($controls as $control) {
                if ($control->canToggle()) {
                    $control->disable();
                }
            }
        }
    }

}