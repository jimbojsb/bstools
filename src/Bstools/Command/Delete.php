<?php

namespace Bstools\Command;

use Pheanstalk\Job;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends Base
{
    public function configure()
    {
        $this->setName('delete')
             ->setDescription('Delete a job by ID');
        $this->addArgument('jobid', InputArgument::REQUIRED, 'the job id to delete');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);

        $jobId = $input->getArgument('jobid');
        $job = new Job($jobId, null);
        $pheanstalk->delete($job);
        $output->writeln("<info>Deleted $jobId...</info>");
    }
}
