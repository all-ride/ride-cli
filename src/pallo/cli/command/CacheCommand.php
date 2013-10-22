<?php

namespace pallo\cli\command;

use pallo\library\cli\command\AbstractCommand;
use pallo\library\dependency\DependencyInjector;

/**
 * Command to get an overview of the caches
 */
class CacheCommand extends AbstractCommand {

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
        parent::__construct('cache', 'Gets an overview of the caches');

        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $controls = $this->dependencyInjector->getAll('pallo\\application\\cache\\control\\CacheControl');

        ksort($controls);

        foreach ($controls as $name => $control) {
            $this->output->writeLine('[' . ($control->isEnabled() ? 'X' : ' ') . '] ' . $name . (!$control->canToggle() ? ' (locked)' : ''));
        }
    }

}