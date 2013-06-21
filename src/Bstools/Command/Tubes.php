<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Bstools\Table;

class Tubes extends Base
{
    public function configure()
    {
        $this->setName('tubes')
             ->setDescription('list tubes');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pheanstalk = $this->createConnection($input);
        $tubes = $pheanstalk->listTubes();
        $output->writeln("<info>Found " . count($tubes) . " tubes...</info>");
        foreach ($tubes as $tube) {
            $output->writeln("<info>$tube</info>");
        }
    }
}