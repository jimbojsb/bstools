<?php

namespace Bstools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Tubes extends Base
{
    public function configure()
    {
        $this->setName('tubes')
             ->setDescription('list tubes');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);
        $tubes = $pheanstalk->listTubes();
        $output->writeln("<info>Found " . count($tubes) . " tubes...</info>");
        foreach ($tubes as $tube) {
            $output->writeln("<info>$tube</info>");
        }
    }
}
