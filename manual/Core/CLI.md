The command line interface gives you a quick tool to perform some maintenance tasks on your installation.

## Use The Command Line Interface

To run a single command of the CLI, use:

    php cli.php [<command>]
    
When no command is provided, the help will be displayed.
    
You can run the console as a interactive shell:

    php cli.php --shell
    
The interactive shell has tab auto completion and a input history. 

## Add A Command

You can add a command by defining one in _config/commands.json_, some examples:

    {
        "commands": [
            {
                "name": "hello",
                "class": "vendor\\app\\command\\HelloCommand"
            },
            {
                "name": "foo",
                "aliases": "f",
                "class": "vendor\\app\\command\\HelloCommand"
            },
            {
                "name": "very long command",
                "aliases": ["c", "vlc"],
                "class": "vendor\\app\\command\\HelloCommand#dependencyId"
            }
        ]
    } 

## Create A Command

The following sample command takes a name as optional argument and prints it out:

    <?php
    
    namespace vendor\app\command;
    
    use ride\cli\command\AbstractCommand;

    class HelloCommand extends AbstractCommand {
    
        protected function initialize() {
            $this->setDescription('Say a greeting');
            
            $this->addArgument('name', 'Your name', false);
        }
        
        public function invoke($name = 'John Doe') {
            $this->output->writeLine('Hello ' . $name);
        }
    
    }
    
Dependencies can be injected automatically.

    <?php
    
    namespace vendor\app\command;
    
    use ride\application\system\System;
    
    use ride\cli\command\AbstractCommand;

    class SystemNameCommand extends AbstractCommand {
    
        protected function initialize() {
            $this->setDescription('Prints the system name');
        }   
        
        public function invoke(System $system) {
            $this->output->writeLine('I'm ' . $system->getName());
        }
    
    }

### Add Autocompletion To Your Command

The interactive shell has tab autocompletion builtin.

For your command however, the autocompletion depends on the data it handles.
The CLI cannot know this and you will have to implement it manually.
It's optional but can improve the user experience of your command. 

To add the auto completion to your command, you simply implement the AutoCompletable interface in it.

    <?php
    
    namespace vendor\app\command;

    use ride\cli\command\AbstractCommand;
    
    use ride\library\cli\input\AutoCompletable;

    class HelloCommand extends AbstractCommand implements AutoCompletable {
    
        // ...
        
        /**
         * Performs auto complete on the provided input
         * @param string $input The input value to auto complete
         * @return array|null Array with the auto completion matches or null when
         * no auto completion is available
         */
        public function autoComplete($input) {
            ...
        }
    
    }
    
The parameter _$input_ of the _autoComplete_ method, is the current CLI input without the command.

For example:

    > help j
    
When the user presses tab, the _$input_ variable will be 'j'.
