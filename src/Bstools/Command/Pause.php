<?php
namespace Bstools\Command;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class Pause extends Base
{
    public function configure()
    {
        $this->setName('pause')
             ->setDescription('Delay any new job being reserved for a given time');
        $this->addArgument('tube', InputArgument::REQUIRED, 'the tube to pause');
        $this->addArgument('delay', InputArgument::REQUIRED, 'number of seconds to wait before reserving any more jobs from the queue');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);
        $tube = $input->getArgument('tube');
        $delay = $input->getArgument('delay');
        $pheanstalk->pauseTube($tube, $delay);
        $output->writeln("<info>Paused tube $tube for $delay seconds</info>");
    }
}
