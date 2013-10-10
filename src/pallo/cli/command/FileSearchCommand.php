<?php

namespace pallo\cli\command;

use pallo\app\system\System;

use pallo\library\cli\command\AbstractCommand;
use pallo\library\system\file\browser\FileBrowser;

/**
 * Command to search for files relative to the directory structure
 */
class FileSearchCommand extends AbstractCommand {

    /**
     * Instance of the file browser
     * @var pallo\library\system\file\browser\FileBrowser
     */
    protected $fileBrowser;

    /**
     * Constructs a new file search command
     * @param pallo\library\system\file\browser\FileBrowser $fileBrowser
     * @return null
     */
    public function __construct(FileBrowser $fileBrowser) {
        parent::__construct('file', 'Search for files relative to the directory structure.');

        $this->addArgument('path', 'Relative path of the file');

        $this->fileBrowser = $fileBrowser;
    }

    /**
     * Executes the command
     * @return null
     */
    public function execute() {
        $file = ltrim($this->input->getArgument('path'), '/');

        $this->output->writeLine('Application files:');

        $files = $this->fileBrowser->getFiles($file);
        if ($files) {
            foreach ($files as $f) {
                $this->output->writeLine('- ' . $f);
            }
        } else {
            $this->output->writeLine('<none>');
        }

        $this->output->writeLine('');

        $hasPublic = false;
        $this->output->writeLine('Public files:');

        $publicFile = $this->fileBrowser->getPublicDirectory()->getChild($file);
        if ($publicFile->exists()) {
            $this->output->writeLine('- ' . $file);

            $hasPublic = true;
        }

        $files = $this->fileBrowser->getFiles(System::DIRECTORY_PUBLIC . '/' . $file);
        if ($files) {
            foreach ($files as $f) {
                $this->output->writeLine('- ' . $f);
            }
        } elseif (!$hasPublic) {
            $this->output->writeLine('<none>');
        }

        $this->output->writeLine('');
    }

}