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
         $dirSkeletons = __DIR__.'/../../../skeleton/';
         $dirProject   = __DIR__.'/../../../../../project/';

         $skeleton = isset($options['skeleton']) ? $options['skeleton'] : 'empty';

         $srcDir = $dirSkeletons.$skeleton;
         if ( is_dir($srcDir = $dirSkeletons.'/'.$skeleton)) {
             $this->recurse_copy($srcDir, $dirProject);
         }else{
             $output->write("Skeleton <info>$skeleton</info> does not exist in <info>$dirSkeletons</info>".PHP_EOL);
         }

    }

    /**
     * recursively copies a directory into another
     * 
     * @param string $src 
     * @param string $dst 
     * @return void
     */
    private function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}

