<?php

namespace ride\cli\command\io;

use ride\cli\command\AbstractCommand;

use ride\library\cli\command\CommandContainer;
use ride\library\cli\exception\CliException;
use ride\library\config\io\AbstractIO;
use ride\library\config\parser\Parser;
use ride\library\dependency\DependencyCallArgument;
use ride\library\dependency\DependencyInjector;
use ride\library\system\file\browser\FileBrowser;
use ride\library\system\file\File;

use \Exception;

/**
 * Parser implementation of the CommandIO interface
 */
class ParserCommandIO extends AbstractIO implements CommandIO {

    /**
     * Instance of the dependency injector
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Parser for the configuration files
     * @var \ride\library\config\parser\Parser
     */
    protected $parser;

    /**
     * Constructs a new command IO
     * @param \ride\library\dependency\DependencyInjector $dependencyInjector
     * @param \ride\library\system\file\browser\FileBrowser $fileBrowser
     * @param \ride\library\config\parser\Parser $parser
     * @param string $file
     * @param string $path
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector, FileBrowser $fileBrowser, Parser $parser, $file, $path = null) {
        parent::__construct($fileBrowser, $file, $path);

        $this->dependencyInjector = $dependencyInjector;
        $this->parser = $parser;
    }

    /**
     * Reads the commands from a parser file
     * @param \ride\library\cli\command\CommandContainer $commandContainer
     * @return null
     */
    public function readCommands(CommandContainer $commandContainer) {
        $path = null;
        if ($this->path) {
            $path = $this->path . File::DIRECTORY_SEPARATOR;
        }

        $files = array_reverse($this->fileBrowser->getFiles($path . $this->file));
        foreach ($files as $file) {
            $this->readCommandsFromFile($commandContainer, $file);
        }

        if ($this->environment) {
            $path .= $this->environment . File::DIRECTORY_SEPARATOR;

            $files = array_reverse($this->fileBrowser->getFiles($path . $this->file));
            foreach ($files as $file) {
                $this->readCommandsFromFile($commandContainer, $file);
            }
        }
    }

    /**
     * Reads the commands from the provided file
     * @param \ride\library\cli\command\CommandContainer $commandContainer
     * @param \ride\library\system\file\File $file
     * @param string $prefix Path prefix
     * @return null
     */
    protected function readCommandsFromFile(CommandContainer $commandContainer, File $file, $prefix = null) {
        try {
            $content = $file->read();
            $content = $this->parser->parseToPhp($content);

            if (!isset($content['commands'])) {
                return;
            }

            foreach ($content['commands'] as $struct) {
                $this->readCommandsFromStruct($commandContainer, $struct, $prefix);
            }
        } catch (Exception $exception) {
            throw new CliException('Could not read commands from ' . $file, 0, $exception);
        }
    }

    /**
     * Adds the commands from the provided command struct to the provided
     * container
     * @param \ride\library\cli\command\CommandContainer $commandContainer
     * @param array $struct Structure with the command definition
     * @param string $prefix Command prefix
     * @return null
     */
    protected function readCommandsFromStruct(CommandContainer $commandContainer, array $struct, $prefix) {
        if (isset($struct['file'])) {
            $file = $this->fileBrowser->getFile($struct['file']);
            if (!$file) {
                throw new CliException('Could not parse command structure: ' . $struct['file'] . ' not found');
            }

            if (isset($struct['prefix'])) {
                $prefix .= $this->processParameter($struct['prefix']);
            }

            $this->readCommandsFromFile($commandContainer, $file, $prefix);

            return;
        }

        if (!isset($struct['name'])) {
            throw new CliException('Could not parse command structure: no name set');
        }
        if (!isset($struct['class'])) {
            throw new CliException('Could not parse command structure: no name set');
        }

        $class = $struct['class'];
        if (strpos($class, '#')) {
            list($class, $id) = explode('#', $class, 2);
        } else {
            $id = null;
        }

        $command = $this->dependencyInjector->get($class, $id);
        if ($command instanceof AbstractCommand) {
            $command->setName($struct['name']);

            if (isset($struct['aliases'])) {
                $aliases = $struct['aliases'];
                if (!is_array($aliases)) {
                    $aliases = array($aliases);
                }

                if ($aliases) {
                    $command->setAliases($aliases);
                }
            }
        }

        $arguments = $this->parseArgumentsFromStruct($struct);
        if ($arguments) {
            $command->setPredefinedArguments($arguments);
        }

        $commandContainer->addCommand($command);
    }

    /**
     * Gets the arguments from a route structure
     * @param array $struct Structure with the command definition
     * @return null|array
     */
    protected function parseArgumentsFromStruct(array $struct) {
        if (!isset($struct['arguments'])) {
            return null;
        }

        $arguments = array();

        foreach ($struct['arguments'] as $argumentStruct) {
            if (!isset($argumentStruct['name'])) {
                throw new CliException('Could not parse route argument: no name set');
            } else {
                $name = $argumentStruct['name'];
            }

            if (!isset($argumentStruct['type'])) {
                throw new CliException('Could not parse route argument: no type set');
            } else {
                $type = $argumentStruct['type'];
            }

            if (isset($argumentStruct['properties'])) {
                $properties = $argumentStruct['properties'];
            } else {
                $properties = array();
            }

            $arguments[$name] = new DependencyCallArgument($name, $type, $properties);
        }

        return $arguments;
    }

}
