<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand;
use ride\library\dependency\DependencyInjector;

/**
 * Command to get an overview of the caches
 */
class CacheCommand extends AbstractCommand {

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
        parent::__construct('cache', 'Gets an overview of the caches');

        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $controls = $this->dependencyInjector->getAll('ride\\application\\cache\\control\\CacheControl');

        ksort($controls);

        foreach ($controls as $name => $control) {
            $this->output->writeLine('[' . ($control->isEnabled() ? 'X' : ' ') . '] ' . $name . (!$control->canToggle() ? ' (locked)' : ''));
        }
    }

}