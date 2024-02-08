<?php

declare(strict_types=1);

namespace Lion\Bundle\Commands\Lion\New;

use Lion\Bundle\Helpers\Commands\ClassFactory;
use Lion\Command\Command;
use Lion\Files\Store;
use Lion\Helpers\Arr;
use Lion\Helpers\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CapsuleCommand extends Command
{
    private ClassFactory $classFactory;
    private Store $store;
    private Str $str;
    private Arr $arr;

    /**
     * @required
     * */
    public function setClassFactory(ClassFactory $classFactory): CapsuleCommand
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    /**
     * @required
     * */
    public function setStore(Store $store): CapsuleCommand
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @required
     * */
    public function setStr(Str $str): CapsuleCommand
    {
        $this->str = $str;

        return $this;
    }

    /**
     * @required
     * */
    public function setArr(Arr $arr): CapsuleCommand
    {
        $this->arr = $arr;

        return $this;
    }

	protected function configure(): void
    {
		$this
            ->setName('new:capsule')
            ->setDescription('Command required for creating new custom capsules')
            ->addArgument('capsule', InputArgument::OPTIONAL, 'Capsule name', 'Example')
            ->addOption(
                'propierties',
                'p',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Defined properties for the capsule',
                []
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $capsule = $input->getArgument('capsule');
        $propierties = $input->getOption('propierties');

        $this->classFactory->classFactory('database/Class/', $capsule);
        $folder = $this->classFactory->getFolder();
        $namespace = $this->classFactory->getNamespace();
        $class = $this->classFactory->getClass();

        $listPropierties = [];
        $listMethods = [];

        foreach ($propierties as $key => $propierty) {
            $split = explode(':', $propierty);

            if (!empty($split[1])) {
                $data = $this->classFactory->getPrivatePropierty($split[0], $class, $split[1]);

                $listPropierties[] = $data->type;

                $listMethods[] = [
                    'getter' => $data->getter->method,
                    'setter' => $data->setter->method,
                    'config' => $data
                ];
            } else {
                $data = $this->classFactory->getPrivatePropierty($split[0], $class);

                $listPropierties[] = $data->type;

                $listMethods[] = [
                    'getter' => $data->getter->method,
                    'setter' => $data->setter->method,
                    'config' => $data
                ];
            }
        }

        $this->store->folder($folder);

        $this->str->of("<?php")->ln()->ln()
            ->concat('declare(strict_types=1);')->ln()->ln()
            ->concat("namespace")->spaces(1)
            ->concat($namespace)
            ->concat(";")->ln()->ln()
            ->concat('use JsonSerializable;')->ln()
            ->concat('use Lion\Bundle\Interface\CapsuleInterface;')->ln()->ln()
            ->concat("class")->spaces(1)
            ->concat($class)->spaces(1)
            ->concat('implements CapsuleInterface, JsonSerializable')->ln()
            ->concat("{")->ln();

        if (count($propierties) > 0) {
            $this->str->lt()->concat($this->arr->of($listPropierties)->join("\n\t"))->ln()->ln();
        }

        $this->str
            ->lt()->concat('public function jsonSerialize(): array')->ln()
            ->lt()->concat('{')->ln()
            ->lt()->lt()->concat('return get_object_vars($this);')->ln()
            ->lt()->concat('}')->ln()->ln();

        if ($this->arr->of($propierties)->length() > 0) {
            $this->str
                ->lt()->concat("/**\n\t * {@inheritdoc}\n\t * */")->ln()
                ->lt()->concat("public function capsule(): {$class}")->ln()
                ->lt()->concat('{')->ln()
                ->lt()->lt()->concat('$this')->ln();

            foreach ($listMethods as $key => $method) {
                $this->str
                    ->lt()->lt()->lt()->concat('->')
                    ->concat($method['config']->setter->name)
                    ->concat('(request->' . $method['config']->format . ' ?? null)')
                    ->concat($key === (count($listMethods) - 1) ? ';' : '')->ln();
            }

            $this->str->ln()->lt()->lt()->concat('return $this;')->ln()
                ->lt()->concat('}');
        } else {
            $this->str
                ->lt()->concat("/**\n\t * {@inheritdoc}\n\t * */")->ln()
                ->lt()->concat("public function capsule(): {$class}")->ln()
                ->lt()->concat('{')->ln()
                ->lt()->lt()->concat('return $this;')->ln()
                ->lt()->concat('}');
        }

        if (count($propierties) > 0) {
            $this->str->ln()->ln();

            foreach ($listMethods as $key => $method) {
                if ($key === (count($listMethods) - 1)) {
                    $this->str->concat($method['getter'])->ln()->ln();
                    $this->str->concat($method['setter'])->ln();
                } else {
                    $this->str->concat($method['getter'])->ln()->ln();
                    $this->str->concat($method['setter'])->ln()->ln();
                }
            }
        } else {
            $this->str->ln();
        }

        $contentFile = $this->str->concat("}")->get();
        $this->classFactory->create($class, 'php', $folder)->add($contentFile)->close();

        $output->writeln($this->warningOutput("\t>>  CAPSULE: {$class}"));
        $output->writeln($this->successOutput("\t>>  CAPSULE: the '{$namespace}\\{$class}' capsule has been generated"));

        return Command::SUCCESS;
	}
}
