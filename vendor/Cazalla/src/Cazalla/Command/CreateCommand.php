<?php
namespace Cazalla\Command;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Cazalla\Util;

class CreateCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('cazalla:create')
            ->addOption('skeleton', null, InputOption::VALUE_OPTIONAL, 'Create a new project based in a skeleton, such as "tutorial"')
            ->setHelp(<<<EOT
The <info>cazalla:create</info> command generates a new project:

<info>./app/console cazalla:create</info>

You can specity an skeleton, such as "tutorial" if you prefer to start with an already working project

<info>./app/console cazalla:create --skeleton=name</info>
EOT
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
         $options = $input->getOptions();

         //Possible skeletons
         $dirGenerator = __DIR__.'/../../../generator/';
         $dirBase   = __DIR__.'/../../../../../';
         $dirProject   = $dirBase.'/project/';

         $skeleton = isset($options['skeleton']) ? $options['skeleton'] : 'empty';

         if (is_dir($dirProject)) {
             mkdir($dirProject);
         }

         $srcDir = $dirGenerator.'skeleton/'.$skeleton;
         if (is_dir($srcDir)) {
            Util::recurse_copy($srcDir, $dirProject);
         }else{
            $output->write("Skeleton <info>$skeleton</info> does not exist in <info>".$dirGenerator."skeleton/'</info>".PHP_EOL);
         }

         $appName = $skeleton == 'empty' ? 'myApp.php' : $skeleton.".php";

         $srcApp = $dirGenerator.'app/myApp.php';
         copy($srcApp, $dirBase.$appName);
    }

}

