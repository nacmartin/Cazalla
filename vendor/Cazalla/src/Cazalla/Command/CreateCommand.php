<?php
namespace Cazalla\Command;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class CreateCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('cazalla:test')
            ->addArgument('argument', InputArgument::REQUIRED, 'Call')
            ->addOption('option', 'o', InputOption::VALUE_OPTIONAL, 'Option', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
         $arg1 = $input->getArgument('argument');
         $options = $input->getOptions();
    }
}

