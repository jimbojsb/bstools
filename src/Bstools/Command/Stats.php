<?php

namespace Bstools\Command;

use Bstools\Renderer\Raw;
use Bstools\Renderer\Table;
use Pheanstalk\Pheanstalk;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stats extends Base
{

    public function configure()
    {
        $this->setName('stats')
             ->setDescription('Print stats on [tube] or on all tubes if [tube] is omitted');
        $this->addArgument('tube', InputArgument::OPTIONAL, 'the tube to show stats for');
        $this->addOption('monitor', 'm', InputOption::VALUE_NONE, 'monitor mode');
        $this->addOption('refresh', 'r', InputOption::VALUE_OPTIONAL, 'monitor refresh rate in seconds', 1);
        $this->addOption('renderer', 'e', InputOption::VALUE_OPTIONAL, 'select a renderer (`table` or `raw`)', 'table');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);
        $tube = $input->getArgument('tube');
        $monitor = $input->getOption('monitor');

        $renderer = $this->getRenderer($input);

        if ($monitor) {
            $rate = $input->getOption('refresh');
            while (true) {
                $this->clearScreen();
                $output->writeln($renderer->render($this->generateStatsTable($pheanstalk, $tube)));;
                sleep($rate);
            }
        } else {
            $output->writeln($renderer->render($this->generateStatsTable($pheanstalk, $tube)));
        }
    }

    private function clearScreen()
    {
        print chr(27) . '[2J' . chr(27) . '[;H';
    }

    private function generateStatsTable(Pheanstalk $pheanstalk, $tube = null)
    {
        if ($tube) {
            $tubes[] = $tube;
        } else {
            $tubes = $pheanstalk->listTubes();
        }

        $stats = array();
        foreach ($tubes as $tube) {
            $stats[$tube] = (array) $pheanstalk->statsTube($tube);
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

        return $stats;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Bstools\Renderer\RendererInterface
     */
    private function getRenderer(InputInterface $input)
    {
        switch ($input->getOption('renderer')) {
            case 'raw':
                return new Raw();
        }

        return new Table();
    }
}
