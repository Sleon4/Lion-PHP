<?php

declare(strict_types=1);

namespace Lion\Bundle\Commands\Lion\Migrations;

use Lion\Bundle\Helpers\Commands\ClassFactory;
use Lion\Bundle\Helpers\Commands\Migrations\MigrationFactory;
use Lion\Bundle\Helpers\Commands\Selection\MenuCommand;
use Lion\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewMigrationCommand extends MenuCommand
{
    const OPTIONS = ['Table', 'View', 'Store-Procedure'];

    private ClassFactory $classFactory;
    private MigrationFactory $migrationFactory;

    /**
     * @required
     * */
    public function setClassFactory(ClassFactory $classFactory): NewMigrationCommand
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    /**
     * @required
     * */
    public function setMigration(MigrationFactory $migrationFactory): NewMigrationCommand
    {
        $this->migrationFactory = $migrationFactory;

        return $this;
    }

	protected function configure(): void
    {
		$this
            ->setName('migrate:new')
            ->setDescription('Command to generate a new migration')
            ->addArgument('migration', InputArgument::REQUIRED, 'Migration name');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migration = $input->getArgument('migration');

        if (str->of($migration)->test("/.*\//")) {
            $output->writeln($this->errorOutput("\t>>  migration cannot be inside subfolders"));

            return Command::INVALID;
        }

        $selectedConnection = $this->selectConnection($input, $output);

        $selectedType = $this->selectMigrationType($input, $output, self::OPTIONS);

        $migrationPascal = $this->str->of($migration)->replace('-', ' ')->replace('_', ' ')->pascal()->trim()->get();
        $dbPascal = $this->str->of($selectedConnection)->replace('-', ' ')->replace('_', ' ')->pascal()->trim()->get();

        if ('Table' === $selectedType) {
            $this->store->folder("database/Migrations/{$dbPascal}/Tables/");
            $this->classFactory->classFactory("database/Migrations/{$dbPascal}/Tables/", $migrationPascal);
            $body = $this->migrationFactory->getTableBody();

            $this->classFactory
                ->create($this->classFactory->getClass(), 'php', $this->classFactory->getFolder())
                ->add($this->str->of($body)->replace('--CONNECTION--', $selectedConnection)->get())
                ->close();
        }

        if ('View' === $selectedType) {
            $this->store->folder("database/Migrations/{$dbPascal}/Views/");
            $this->classFactory->classFactory("database/Migrations/{$dbPascal}/Views/", $migrationPascal);
            $body = $this->migrationFactory->getViewBody();

            $this->classFactory
                ->create($this->classFactory->getClass(), 'php', $this->classFactory->getFolder())
                ->add($this->str->of($body)->replace('--CONNECTION--', $selectedConnection)->get())
                ->close();
        }

        if ('Store-Procedure' === $selectedType) {
            $this->store->folder("database/Migrations/{$dbPascal}/StoreProcedures/");
            $this->classFactory->classFactory("database/Migrations/{$dbPascal}/StoreProcedures/", $migrationPascal);
            $body = $this->migrationFactory->getStoreProcedureBody();

            $this->classFactory
                ->create($this->classFactory->getClass(), 'php', $this->classFactory->getFolder())
                ->add($this->str->of($body)->replace('--CONNECTION--', $selectedConnection)->get())
                ->close();
        }

        $output->writeln($this->warningOutput("\t>>  MIGRATION: {$this->classFactory->getClass()}"));

        $output->writeln(
            $this->successOutput(
                "\t>>  MIGRATION: the '{$this->classFactory->getNamespace()}\\{$this->classFactory->getClass()}' migration has been generated"
            )
        );

		return Command::SUCCESS;
	}
}
