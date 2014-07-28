<?php

namespace Bstools\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Kick extends Base
{
    public function configure()
    {
        $this->setName('kick')
             ->setDescription('Kick jobs from buried back into ready');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to kick jobs in');
        $this->addArgument('num', InputArgument::OPTIONAL, 'the number of jobs to kick');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);
        $tube = $input->getArgument('tube');
        $num = $input->getArgument('num');
        if (!$num) {
            $stats = $pheanstalk->statsTube($tube);
            $num = $stats["current-jobs-buried"];
        }
        if ($num > 0) {
            $pheanstalk->useTube($tube);
            $output->writeln("<info>Trying to kick $num jobs from $tube...</info>");
            $kicked = $pheanstalk->kick($num);
            $output->writeln("<info>Actually kicked $kicked.</info>");
        }
    }
}
