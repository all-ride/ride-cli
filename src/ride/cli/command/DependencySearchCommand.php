<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand;
use ride\library\dependency\DependencyInjector;

/**
 * Command to show an overview of the defined dependencies
 */
class DependencySearchCommand extends AbstractCommand {

    /**
     * Instance of the dependency injector
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Constructs a new dependency search command
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        parent::__construct('dependency', 'Show an overview of the defined dependencies');

        $this->addArgument('query', 'Query to search the dependencies', false, true);

        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $container = $this->dependencyInjector->getContainer();
        $dependencies = $container->getDependencies();

        // filter the dependencies on the search query
        $query = $this->input->getArgument('query');
        if ($query) {
            foreach ($dependencies as $interface => $null) {
                if (stripos($interface, $query) !== false) {
                    continue;
                }

                unset($dependencies[$interface]);
            }

            $this->output->writeLine('Defined dependencies for "' . $query . '":');
        } else {
            $this->output->writeLine('Defined dependencies:');
        }

        ksort($dependencies);

        // write the dependencies
        $tab = '    ';
        foreach ($dependencies as $interface => $interfaceDependencies) {
            $this->output->writeLine('- ' . $interface);

            foreach ($interfaceDependencies as $dependency) {
                $id = $dependency->getId();

                $this->output->writeLine($tab . '#' . $id . ' ' . $dependency->getClassName());

                $padding = $tab . str_repeat(' ', strlen($id) + 2);
                $argumentPadding = $padding . $tab;

                $constructor = $dependency->getConstructorArguments();
                if ($constructor) {
                    $this->output->writeLine($padding . '->__construct(');
					$this->output->writeLine($argumentPadding . str_replace("\n", "\n" . $argumentPadding, implode(",\n", $constructor)));
                    $this->output->writeLine($padding . ')');
                }

                $calls = $dependency->getCalls();
                if ($calls) {
                    foreach ($calls as $call) {
                        $arguments = $call->getArguments();

                        if ($arguments) {
                            $this->output->writeLine($padding . '->' . $call->getMethodName() . '(');
                            $this->output->writeLine($argumentPadding . str_replace("\n", "\n" . $argumentPadding, implode(",\n" . $argumentPadding, $arguments)));
                            $this->output->writeLine($padding . ')');
                        } else {
                            $this->output->writeLine($padding . '->' . $call->getMethodName() . '()');
                        }
                    }
                }
            }

            $this->output->writeLine('');
        }
    }

}