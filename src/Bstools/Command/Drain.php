<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

class Drain extends Base
{
    public function configure()
    {
        $this->setName('drain')
             ->setDescription('Delete existing jobs from ready or buried states');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to drain from');
        $this->addArgument('num', InputArgument::OPTIONAL, 'number of jobs to drain');
        $this->addOption('buried', null, InputOption::VALUE_NONE, 'drain from buried instead of ready');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = new \Pheanstalk_Pheanstalk($input->getOption('host'));

        $tube = $input->getArgument('tube');
        $buried = $input->getOption('buried');
        $num = $input->getArgument('num');

        if (!$num) {
            $stats = $pheanstalk->statsTube($tube);
            if ($buried) {
                $num = $stats["current-jobs-buried"];
            } else {
                $num = $stats["current-jobs-ready"];
            }
        }

        $output->writeln("<info>Attempting to drain $num jobs from $tube...</info>");
        $drained = 0;
        for ($c = 0; $c < $num; $c++) {
            try {
                if ($buried) {
                    $job = $pheanstalk->peekBuried($tube);
                } else {
                    $job = $pheanstalk->peekReady($tube);
                }
            } catch (\Exception $e) {
                break;
            }
            if ($job) {
                $pheanstalk->delete($job);
                $drained++;
            }
        }
        $output->writeln("<info>Actually drained $drained</info>");
    }
}