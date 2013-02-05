<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

class Stats extends Base
{
    public function configure()
    {
        $this->setName('stats')
             ->setDescription('Print stats on [tube] or on all tubes if [tube] is omitted');
        $this->addArgument('tube', InputArgument::OPTIONAL, 'the tube to show stats for');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = new \Pheanstalk_Pheanstalk($input->getOption('host'));

        $tube = $input->getArgument('tube');
        if ($tube) {
            $tubes[] = $tube;
        } else {
            $tubes = $pheanstalk->listTubes();
        }

        $stats = array();
        foreach ($tubes as $tube) {
            $stats[$tube] = (Array) $pheanstalk->statsTube($tube);
        }

        $statsToDisplay = array('current-jobs-urgent',
                                'current-jobs-ready',
                                'current-jobs-reserved',
                                'current-jobs-delayed',
                                'current-jobs-buried',
                                'current-waiting',
                                'total-jobs');
        foreach ($stats as &$tubeStats) {
            foreach ($tubeStats as $key => $val) {
                if (!in_array($key, $statsToDisplay)) {
                    unset($tubeStats[$key]);
                }
            }
        }



        $outputTable = new Table($stats);
        $output->writeln($outputTable->render());
    }
}