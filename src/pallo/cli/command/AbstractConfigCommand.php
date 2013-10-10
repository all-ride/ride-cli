<?php

namespace pallo\cli\command;

use pallo\library\cli\command\AbstractCommand;
use pallo\library\cli\exception\CliException;
use pallo\library\cli\input\AutoCompletable;
use pallo\library\config\Config;
use pallo\library\config\ConfigHelper;

/**
 * Abstract configuration command which provides auto completion
 */
abstract class AbstractConfigCommand extends AbstractCommand implements AutoCompletable {

    /**
     * Instance of the config
     * @var pallo\library\config\Config
     */
    protected $config;

    /**
     * Instance of the config helper
     * @var pallo\library\config\ConfigHelper
     */
    protected $configHelper;

    /**
     * Sets the instance of the config
     * @param pallo\library\config\Config $config
     * @return null
     */
    public function setConfig(Config $config) {
        $this->config = $config;
    }

    /**
     * Sets the instance of the config helper
     * @param pallo\library\config\ConfigHelper $configHelper
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
     * @throws pallo\library\cli\exception\CliException when the config or the
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