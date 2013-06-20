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
        $this->addArgument('state', InputArgument::OPTIONAL, 'peek from buried instead of ready', 'ready');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);

        $tube = $input->getArgument('tube');
        $state = $input->getArgument('state');
        $permissibleStates = array('ready', 'buried', 'delayed');
        if (!in_array($state, $permissibleStates)) {
            throw new \Exception("$state is not a valid job state");
        }
        try {
            $cmd = "peek" . ucfirst(strtolower($state));
            $job = $pheanstalk->$cmd($tube);
        } catch (\Exception $e) {
            return;
        }
        $jobId = $job->getId();
        $jobData = $job->getData();
        $output->writeln("<info>Job ID: </info> <comment>$jobId</comment>");
        $output->writeln("<info>Job Data: </info> <comment>$jobData</comment>");
    }
}