<?php

namespace ride\cli\command\io;

use ride\library\cli\command\CommandContainer;

/**
 * Interface to read commands from a source
 */
interface CommandIO {

    /**
     * Reads the commands from a source
     * @param \ride\library\cli\command\CommandContainer $commandContainer
     * @return null
     */
    public function readCommands(CommandContainer $commandContainer);

}
