<?php

namespace Bstools\Command;

use Pheanstalk\PheanstalkInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Insert extends Base
{
    public function configure()
    {
        $this->setName('insert')
             ->setDescription('Insert a job into a given tube');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to insert the job into');
        $this->addArgument('jobdata', InputArgument::REQUIRED, 'plain text data for the job');
        $this->addOption('priority', null, InputOption::VALUE_OPTIONAL, 'Priority for the job', PheanstalkInterface::DEFAULT_PRIORITY);
        $this->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay for the job', PheanstalkInterface::DEFAULT_DELAY);
        $this->addOption('ttr', null, InputOption::VALUE_OPTIONAL, 'TTR for the job', PheanstalkInterface::DEFAULT_TTR);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $jobData = $input->getArgument('jobdata');
        $priority = $input->getOption('priority');
        $delay = $input->getOption('delay');
        $ttr = $input->getOption('ttr');

        $pheanstalk = $this->createConnection($input);
        $pheanstalk->putInTube($tube, $jobData, $priority, $delay, $ttr);

        $output->writeln("<info>Added job to $tube with priority $priority, delay $delay, TTR $ttr</info>");
    }
}
