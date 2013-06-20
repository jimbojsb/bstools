<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

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