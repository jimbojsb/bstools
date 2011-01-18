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
    			var_dump($this->_pheanstalk->statsTube($this->_tube));
    			break;
    		case "tubes":
    			var_dump($this->_pheanstalk->listTubes());		
    		case "drop-pending":
    			$numToDrop = $this->_argv[4] ? $this->_argv[4] : 1;
    			$this->_pheanstalk->watch($this->_tube);
    			for ($c = 0; $c < $numToDrop; $c++) {
    			    $job = $this->_pheanstalk->reserve();
    			    $this->_pheanstalk->delete($job);
    			}
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