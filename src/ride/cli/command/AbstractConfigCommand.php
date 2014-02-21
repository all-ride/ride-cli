<?php

namespace ride\cli\command;

use ride\library\cli\command\AbstractCommand;
use ride\library\cli\exception\CliException;
use ride\library\cli\input\AutoCompletable;
use ride\library\config\Config;
use ride\library\config\ConfigHelper;

/**
 * Abstract configuration command which provides auto completion
 */
abstract class AbstractConfigCommand extends AbstractCommand implements AutoCompletable {

    /**
     * Instance of the config
     * @var ride\library\config\Config
     */
    protected $config;

    /**
     * Instance of the config helper
     * @var ride\library\config\ConfigHelper
     */
    protected $configHelper;

    /**
     * Sets the instance of the config
     * @param ride\library\config\Config $config
     * @return null
     */
    public function setConfig(Config $config) {
        $this->config = $config;
    }

    /**
     * Sets the instance of the config helper
     * @param ride\library\config\ConfigHelper $configHelper
     * @return null
     */
    public function setConfigHelper(ConfigHelper $configHelper) {
        $this->configHelper = $configHelper;
    }

    /**
     * Performs auto complete on the provided input
     * @param string $input Input value to auto complete
     * @return array Array with the auto completion matches
     */
    public function autoComplete($input) {
        $this->validateInstance();

        $completion = array();

        if (strpos($input, ' ') !== false) {
            return $completion;
        }

        $values = $this->config->getAll();
        $values = $this->configHelper->flattenConfig($values);

        foreach ($values as $key => $value) {
            if (strpos($key, $input) !== 0) {
                continue;
            }

            $completion[] = $key;
        }

        return $completion;
    }

    /**
     * Checks if the instance of Config and ConfigHelper are set
     * @return null
     * @throws ride\library\cli\exception\CliException when the config or the
     * config helper are not set
     */
    protected function validateInstance() {
        if (!$this->config) {
            throw new CliException('Could not use the command: no config set, use the setConfig() method first');
        } elseif (!$this->configHelper) {
            throw new CliException('Could not use the command: no config helper set, use the setConfigHelper() method first');
        }
    }

}