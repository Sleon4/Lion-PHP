<?php

declare(strict_types=1);

namespace Lion\Bundle\Commands\Lion\New;

use DI\Attribute\Inject;
use Lion\Bundle\Helpers\Commands\ClassFactory;
use Lion\Command\Command;
use Lion\Files\Store;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate classes to generate templates in HTML
 *
 * @property ClassFactory $classFactory [Fabricates the data provided to
 * manipulate information (folder, class, namespace)]
 * @property Store $store [Store class object]
 *
 * @package Lion\Bundle\Commands\Lion\New
 */
class HtmlCommand extends Command
{
    /**
     * [Fabricates the data provided to manipulate information (folder, class,
     * namespace)]
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

    #[Inject]
    public function setClassFactory(ClassFactory $classFactory): void
    {
        $this->classFactory = $classFactory;
    }

    #[Inject]
    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('new:html')
            ->setDescription('Command needed to create new HTML templates')
            ->addArgument('html', InputArgument::OPTIONAL, 'Html name', 'ExampleHtml');
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
     * @return int
     *
     * @throws LogicException [When this abstract method is not implemented]
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $html = $input->getArgument('html');

        $this->classFactory->classFactory('app/Html/', $html);

        $folder = $this->classFactory->getFolder();

        $namespace = $this->classFactory->getNamespace();

        $class = $this->classFactory->getClass();

        $this->store->folder($folder);

        $this->classFactory
            ->create($class, ClassFactory::PHP_EXTENSION, $folder)
            ->add(
                <<<PHP
                <?php

                declare(strict_types=1);

                namespace {$namespace};

                use Lion\Bundle\Helpers\Commands\Html;
                use Lion\Bundle\Interface\HtmlInterface;

                /**
                 * Define an HTML template
                 *
                 * @package {$namespace}
                 */
                class {$class} extends Html implements HtmlInterface
                {
                    /**
                     * {@inheritdoc}
                     */
                    public function template(): {$class}
                    {
                        \$this->add(
                            <<<HTML
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="utf-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1">
                                <title>HTML Template</title>
                            </head>
                            <body>
                                <h1>HTML Template (--REPLACE--)</h1>
                            </body>
                            </html>
                            HTML
                        );

                        return \$this;
                    }
                }

                PHP
            )
            ->close();

        $output->writeln($this->warningOutput("\t>>  HTML: {$class}"));

        $output->writeln($this->successOutput("\t>>  HTML: the '{$namespace}\\{$class}' html has been generated"));

        return Command::SUCCESS;
    }
}
