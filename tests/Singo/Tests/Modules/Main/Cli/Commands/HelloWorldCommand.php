<?php


namespace Singo\Tests\Modules\Main\Cli\Commands;

use Singo\Contracts\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends AbstractCommand
{
    public function configure()
    {
        $this
            ->setName("hello:world")
            ->setDescription("Hello world");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("hello world");
    }
}

