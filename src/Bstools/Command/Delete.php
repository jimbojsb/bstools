<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

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
        $job = new \Pheanstalk_Pheanstalk_Job($jobId, null);
        $pheanstalk->delete($job);
        $output->writeln("<info>Deleted $jobId...</info>");
    }
}