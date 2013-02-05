<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

class Bury extends Base
{
    public function configure()
    {
        $this->setName('bury')
             ->setDescription('Bury existing jobs from ready state');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to drain from');
        $this->addArgument('num', InputArgument::OPTIONAL, 'number of jobs to drain');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = new \Pheanstalk_Pheanstalk_Pheanstalk($input->getOption('host'));

        $tube = $input->getArgument('tube');
        $num = $input->getArgument('num');

        if (!$num) {
            $stats = $pheanstalk->statsTube($tube);
            $num = $stats["current-jobs-ready"];
        }

        $output->writeln("<info>Attempting to bury $num jobs from $tube...</info>");
        $buried = 0;
        for ($c = 0; $c < $num; $c++) {
            try {
                $job = $pheanstalk->reserveFromTube($tube);
                $pheanstalk->bury($job);
                $buried++;
            } catch (\Exception $e) {
                break;
            }
        }
        $output->writeln("<info>Actually drained $buried</info>");
    }
}