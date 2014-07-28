<?php

namespace Bstools\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsJob extends Base
{
    public function configure()
    {
        $this->setName('stats-job')
             ->setDescription('Stats on a specific job id');
        $this->addArgument('job-id', InputArgument::REQUIRED, 'the integer job id');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);

        $jobId = $input->getArgument('job-id');
        $stats = $pheanstalk->statsJob($jobId);
        $output->writeln("<info>Job ID: </info> <comment>$jobId</comment>");
        foreach ($stats as $key => $val) {
            $output->writeln("<info>$key: </info><comment>$val</comment>");
        };
    }
}
