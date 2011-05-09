<?php
require_once 'Pheanstalk/pheanstalk_init.php';
class Cli
{
    protected $_pheanstalk;
    protected $_host;
    protected $_action; 
    protected $_argv;
    
    public function __construct($argv)
    {
        $this->_pheanstalk = new Pheanstalk($argv[1]);
        $this->_tube = $argv[2];
        $this->_pheanstalk->useTube($this->_tube);
        $this->_argv = $argv;
        if (count($argv) == 3) {
            $this->_action = $argv[2];
        } else {
            $this->_action = $argv[3];
        }    
    }
    
    public function stats()
    {
        return $this->_pheanstalk->statsTube($this->_tube);
    }

    /**
     * Delete all jobs from the ready or buried queue of the current tube.
     *
     * @param string $queue "ready" or "buried"
     **/
    public function drain($queue)
    {
        if(!in_array($queue, array('ready', 'buried'))) {
            throw new Exception("Queue param must be 'ready' or 'buried'");
        }
        $stats = $this->stats();
        $numToDrop = $stats["current-jobs-$queue"];

        $this->_pheanstalk->watch($this->_tube);
        for ($c = 0; $c < $numToDrop; $c++) {
            switch($queue) {
                case 'ready':
                    $job = $this->_pheanstalk->peekReady();
                    break;
                case 'buried':
                    $job = $this->_pheanstalk->peekBuried();
                    break;
            }
            $this->_pheanstalk->delete($job);
        }
    }

    public function dispatch()
    {
    	switch ($this->_action) {
    		case "kick":
    			$num = $this->_argv[4] ? $this->_argv[4] : 1; 
    			var_dump($this->_pheanstalk->kick($num));
    			break;
    		case "peek-buried":
    			var_dump($this->_pheanstalk->peekBuried());
    			break;
    		case "peek":
                var_dump($this->_pheanstalk->peekReady());
    			break;
    		case "delete":
    			$jobId = $this->_argv[4];
    			if ($jobId) {
    				$job = new Pheanstalk_Job($jobId, null);
    				$this->_pheanstalk->delete($job);
    			}
    			break;
    		case "stats":
    			var_dump($this->stats());
    			break;
    		case "tubes":
    			var_dump($this->_pheanstalk->listTubes());
    			break;		
    		case "drain-ready":
                $this->drain('ready');
    			break;
    		case "drain-buried":
                $this->drain('buried');
    			break;
    		case "insert-json":
                $jobData = $this->_argv[4];
                $bits = explode(',', $jobData);
                foreach ($bits as $bit) {
                    list($key, $value) = explode("=", $bit);
                    $job[$key] = $value;
                }
                $job = json_encode($job);
                $this->_pheanstalk->put($job);
    		    break;
			
    	}
	}
}
