<?php

declare(strict_types=1);

namespace Tests\Commands\Lion\Schedule;

use Lion\Bundle\Commands\Lion\Schedule\RunQueuedTasksCommand;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Dependency\Injection\Container;
use Lion\Test\Test;
use ReflectionException;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\Attributes\Test as Testing;

class RunQueuedTasksCommandTest extends Test
{
    private RunQueuedTasksCommand $runQueuedTasksCommand;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->runQueuedTasksCommand = (new RunQueuedTasksCommand())
            ->setContainer(new Container());

        $application = new Application();

        $application->add($this->runQueuedTasksCommand);

        $this->initReflection($this->runQueuedTasksCommand);
    }

    #[Testing]
    public function setContainer(): void
    {
        $this->assertInstanceOf(
            RunQueuedTasksCommand::class,
            $this->runQueuedTasksCommand->setContainer(new Container())
        );

        $this->assertInstanceOf(Container::class, $this->getPrivateProperty('container'));
    }

    #[Testing]
    public function setTaskQueue(): void
    {
        $this->assertInstanceOf(
            RunQueuedTasksCommand::class,
            $this->runQueuedTasksCommand->setTaskQueue(new TaskQueue())
        );

        $this->assertInstanceOf(TaskQueue::class, $this->getPrivateProperty('taskQueue'));
    }
}
