<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand;
use ride\library\dependency\DependencyInjector;

/**
 * Command to enable the cache
 */
class CacheEnableCommand extends AbstractCommand {

        /**
     * Instance of the dependency injector
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new cache enable command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('cache enable', 'Enables the cache');

        $this->addArgument('name', 'Name of the cache to enable', false);

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
            if ($control->canToggle()) {
                $control->enable();
            }
        } else {
            $controls = $this->dependencyInjector->getAll('ride\\application\\cache\\control\\CacheControl');
            foreach ($controls as $control) {
                if ($control->canToggle()) {
                    $control->enable();
                }
            }
        }
    }

}