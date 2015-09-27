<?php

namespace ride\cli\command\io;

use ride\library\cli\command\CommandContainer;
use ride\library\dependency\DependencyInjector;

/**
 * Implementation of CommandIO to read commands from the dependencyInjector
 */
class DependencyCommandIO implements CommandIO {

    /**
     * Instance of the dependency injector
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new command IO
     * @param \ride\library\dependency\DependencyInjector $dependencyInjector
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Reads the commands from a source
     * @param \ride\library\cli\command\CommandContainer $commandContainer
     * @return null
     */
    public function readCommands(CommandContainer $commandContainer) {
        $commands = $this->dependencyInjector->getByTag('ride\\library\\cli\\command\\Command', null, 'exclude');
        foreach ($commands as $command) {
            $commandContainer->addCommand($command);
        }
    }

}
