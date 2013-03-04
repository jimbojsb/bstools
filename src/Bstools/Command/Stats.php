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
    private $refreshRate = 0.5;

    public function configure()
    {
        $this->setName('stats')
             ->setDescription('Print stats on [tube] or on all tubes if [tube] is omitted');
        $this->addArgument('tube', InputArgument::OPTIONAL, 'the tube to show stats for');
        $this->addOption('monitor', null, InputOption::VALUE_NONE, 'Monitor mode');
        $this->addOption('rate', null, InputOption::VALUE_OPTIONAL, 'Refresh rate', $this->refreshRate);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = new \Pheanstalk_Pheanstalk($input->getOption('host'));
        $tube = $input->getArgument('tube');
        $monitor = $input->getOption('monitor');

        if ( !$rate = (float)$input->getOption('rate') ) $rate = $this->refreshRate;

        while(1) {
            if ( $monitor ) $this->clearScreen(); 
            $outputTable = $this->calc($pheanstalk, $tube);
            $output->write($outputTable->render(), false);

            if ( $monitor ) usleep($rate * 1000000);
            else break;
        }
    }

    private function clearScreen() 
    {
        print chr(27) . '[2J' . chr(27) . '[;H';
    }

    private function calc(\Pheanstalk_Pheanstalk $pheanstalk, $tube = null)
    {
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

        return new Table($stats);
    }
}