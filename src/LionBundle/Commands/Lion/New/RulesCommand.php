<?php

declare(strict_types=1);

namespace Lion\Bundle\Commands\Lion\New;

use Lion\Bundle\Helpers\Commands\ClassFactory;
use Lion\Command\Command;
use Lion\Files\Store;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate a rule
 *
 * @property ClassFactory $classFactory [ClassFactory class object]
 * @property Store $store [Store class object]
 *
 * @package Lion\Bundle\Commands\Lion\New
 */
class RulesCommand extends Command
{
    /**
     * [ClassFactory class object]
     *
     * @var ClassFactory $classFactory
     */
    private ClassFactory $classFactory;

    /**
     * [Store class object]
     *
     * @var Store $store
     */
    private Store $store;

    /**
     * @required
     * */
    public function setClassFactory(ClassFactory $classFactory): RulesCommand
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    /**
     * @required
     * */
    public function setStore(Store $store): RulesCommand
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('new:rule')
            ->setDescription('Command required for rule creation')
            ->addArgument('rule', InputArgument::OPTIONAL, 'Rule name', 'ExampleRule');
    }

    /**
     * Executes the current command
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method
     *
     * @param InputInterface $input [InputInterface is the interface implemented
     * by all input classes]
     * @param OutputInterface $output [OutputInterface is the interface
     * implemented by all Output classes]
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rule = $input->getArgument('rule');

        $this->classFactory->classFactory('app/Rules/', $rule);

        $folder = $this->classFactory->getFolder();

        $class = $this->classFactory->getClass();

        $namespace = $this->classFactory->getNamespace();

        $this->store->folder($folder);

        $this->classFactory
            ->create($class, ClassFactory::PHP_EXTENSION, $folder)
            ->add(
                <<<EOT
                <?php

                declare(strict_types=1);

                namespace {$namespace};

                use Lion\Bundle\Helpers\Rules;
                use Lion\Bundle\Interface\RulesInterface;
                use Valitron\Validator;

                /**
                 * Rule defined for the '' property
                 *
                 * @property string \$field [field for '']
                 * @property string \$desc [description for '']
                 * @property string \$value [value for '']
                 * @property bool \$disabled [Defines whether the column is optional for postman
                 * collections]
                 *
                 * @package {$namespace}
                 */
                class {$class} extends Rules implements RulesInterface
                {
                    /**
                     * [field for '']
                     *
                     * @var string \$field
                     */
                    public string \$field = '';

                    /**
                     * [description for '']
                     *
                     * @var string \$desc
                     */
                    public string \$desc = '';

                    /**
                     * [value for '']
                     *
                     * @var string \$value;
                     */
                    public string \$value = '';

                    /**
                     * [Defines whether the column is optional for postman collections]
                     *
                     * @var bool \$disabled;
                     */
                    public bool \$disabled = false;

                    /**
                     * {@inheritdoc}
                     */
                    public function passes(): void
                    {
                        \$this->validate(function (Validator \$validator): void {
                            \$validator
                                ->rule('required', \$this->field)
                                ->message('the "" property is required');
                        });
                    }
                }

                EOT
            )
            ->close();

        $output->writeln($this->warningOutput("\t>>  RULE: {$class}"));

        $output->writeln($this->successOutput("\t>>  RULE: the '{$namespace}\\{$class}' rule has been generated"));

        return Command::SUCCESS;
    }
}
