<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

class Peek extends Base
{
    public function configure()
    {
        $this->setName('peek')
             ->setDescription('Peek at the job on top of the ready or buried queue');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to drain from');
        $this->addOption('buried', null, InputOption::VALUE_NONE, 'drain from buried instead of ready');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = new \Pheanstalk($input->getOption('host'));

        $tube = $input->getArgument('tube');
        $buried = $input->getOption('buried');
        try {
            if ($buried) {
                $job = $pheanstalk->peekBuried($tube);
            } else {
                $job = $pheanstalk->peekReady($tube);
            }
        } catch (\Exception $e) {
            return;
        }
        $jobId = $job->getId();
        $jobData = $job->getData();
        $output->writeln("<info>Job ID: </info> <comment>$jobId</comment>");
        $output->writeln("<info>Job Data: </info> <comment>$jobData</comment>");
    }
}