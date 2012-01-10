<?php
namespace Bstools;
use \Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
class App extends Application
{
    public function __construct()
    {
        parent::__construct('bsTools', '0.3');
        $this->add(new \Bstools\Command\Stats());
    }
}