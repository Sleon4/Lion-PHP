<?php

namespace App\Console\Framework\Migrations;

use App\Traits\Framework\ClassPath;
use LionFiles\Store;
use LionSQL\Drivers\MySQL\MySQL as DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewMigrateCommand extends Command {

    use ClassPath;

	protected static $defaultName = "migrate:new";

	protected function initialize(InputInterface $input, OutputInterface $output) {

	}

	protected function interact(InputInterface $input, OutputInterface $output) {

	}

	protected function configure() {
		$this
            ->setDescription("Command to generate a new migration")
            ->addArgument('migration', InputArgument::REQUIRED, 'Migration name')
            ->addArgument('connection', InputArgument::REQUIRED, 'Connection name')
            ->addOption("type", "t", InputOption::VALUE_OPTIONAL, "Type of migration", "TABLE");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
        // get migration name and validate that it is not in subfolders
        $migration = $input->getArgument('migration');
        if (str->of($migration)->test("/.*\//")) {
            $output->writeln("<error>Migration cannot be inside subfolders</error>");
            return Command::INVALID;
        }

        // select type of migration
        $option = str->of($input->getOption('type'))->upper()->get();

        // select connection
        $connections = DB::getConnections();
        $connections = arr->of($connections['connections'])->keys()->get();
        $connection = $input->getArgument('connection');

        $db_pascal = str->of($connection)->replace("-", " ")->replace("_", " ")->pascal()->trim()->get();
        $migration_pascal = str->of($migration)->replace("-", " ")->replace("_", " ")->pascal()->trim()->get();
        $migration_pascal = str->of($option === "TABLE" ? "Table" : ($option === "VIEW" ? "View" : "Procedure"))->concat($migration_pascal)->get();
        $env_var = array_search($connection, (array) env);

        if ($option === "TABLE") {
            Store::folder("database/Migrations/{$db_pascal}/tables/");
            $this->new("database/Migrations/{$db_pascal}/tables/{$migration_pascal}", "php");
            $this->add(str->of($this->getTemplateCreateTable())->replace("env->DB_NAME", "env->{$env_var}")->get());
        } elseif ($option === "VIEW") {
            Store::folder("database/Migrations/{$db_pascal}/views/");
            $this->new("database/Migrations/{$db_pascal}/views/{$migration_pascal}", "php");
            $this->add(str->of($this->getTemplateCreateView())->replace("env->DB_NAME", "env->{$env_var}")->get());
        } elseif ($option === "PROCEDURE") {
            Store::folder("database/Migrations/{$db_pascal}/procedures/");
            $this->new("database/Migrations/{$db_pascal}/procedures/{$migration_pascal}", "php");
            $this->add(str->of($this->getTemplateCreateProcedure())->replace("env->DB_NAME", "env->{$env_var}")->get());
        } else {
            $output->writeln("<fg=#E37820>\t>>  The selected migration type does not exist</>");
            return Command::INVALID;
        }

        $output->writeln("<info>\t>>  {$option}: Migration 'database/Migrations/{$db_pascal}/{$migration_pascal}' has been generated</info>");

        $this->force();
        $this->close();

		return Command::SUCCESS;
	}

}
