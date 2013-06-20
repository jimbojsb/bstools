<?php
namespace Bstools\Command;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
class Base extends Command
{
    public function __construct()
    {
        parent::__construct();
        $this->addOption('host', null, InputOption::VALUE_OPTIONAL, 'hostname of the beanstalk server', 'localhost');
        $this->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'port of the beanstalk server', \Pheanstalk_PheanstalkInterface::DEFAULT_PORT);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return \Pheanstalk_Pheanstalk
     */
    protected function createConnection(InputInterface $input)
    {
        $pheanstalk = new \Pheanstalk_Pheanstalk($input->getOption('host'), $input->getOption('port'));
        return $pheanstalk;
    }
}