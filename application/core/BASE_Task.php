<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit(0);

require_once (APPPATH.'core/BASE_Result.php');
require_once (APPPATH.'helpers/enums/enum_common_helper.php');

/**
 * BASE_Task - Controller
 * 
 * @author Marco Eberhardt
 * @category controller
 * @package application\core\BASE_Task
 * @version 1.0
 */
class BASE_Task extends CI_Controller
{
	/**
	 * Codeigniter instance
	 */
	public $ci						= null;
	/**
	 *
	 */
	private $task_id				= "";
	
	/**
	 * Taskname
	 * @var string
	 */
	private $task_name				= "";
	
	/**
	 * Task start time 
	 * @var string
	 */
	private $starttime				= "";
	
	/**
	 * recipient for sending email notifications on task error
	 * @var string
	 */
	public $mailToOnError			= "";
	
	/**
	 * recipient for sending email notifications on task finish
	 * @var string
	 */
	public $mailToWhenFinished		= "";

	/**
	 * Switch to only allow calls from command line
	 * @var bool
	 */
	private $onlyCLI				= true;

	/**
	 * trigger to write logs into database 
	 * @var bool
	 */
	public $logEvents 				= true;
	
	/**
	 * trigger to enable email notifications
	 * @var bool
	 */
	public $sendMails				= true;
	
	/**
	 * @var bool
	 */
	public $task_messages			= array();
	public $task_messages_levels	= array("debug", "error", "info", "warning");
	
	/**
	 * 
	 * @param string $logEvents
	 * @param string $mailToOnError
	 * @param string $mailToWhenFinished
	 * @param bool $onlyCLI
	 */
	public function __construct($onlyCLI=true, $mailToOnError=NULL, $mailToWhenFinished=NULL)
	{
		parent::__construct();
		
		if ($onlyCLI === true && $this->is_cli() === false)
		{
			// no access via browser allowed
			header('HTTP/1.1 403 Forbidden');
			die("<h3>403 Forbidden</h3>");
		}
		
		if (!$mailToOnError){$mailToOnError = $this->config->item('email_task_error');}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->ci =& get_instance();
		$this->task_name 			= get_class($this);
		$this->task_id 				= $this->task_name."_".substr(uniqid(), 0, 9)."#".time();
		$this->logEvents			= true;
		$this->starttime			= date("d.m.Y H:i:s");;
		$this->onlyCLI				= $onlyCLI;
		$this->mailToOnError		= $mailToOnError;
		$this->mailToWhenFinished	= $mailToWhenFinished;
		
		$locale_folder = "german";
		$this->lang->load($locale_folder, $locale_folder);
		$this->load->model("app_model");
		$this->load->library("BASE_Mailer");
		$this->load->helper("debug");
		
		
		
		$this->logEvent(E_TASK_EVENT::START, "Task started at: ".date("Y-m-d H:i:s"));
		
		if (!is_cli()){
			echo '<style>body{font-family: Consolas;font-size:12px;font-weight:bold;color:green;background-color:black;}</style>';
		}
		
		self::taskMessage("BASE_Task constructed at [".$this->starttime."] onlyCLI[$onlyCLI] isCLI[".$this->is_cli()."] Host[".gethostname()."]", "info");
		self::taskMessage("OnError [".$this->mailToOnError."]", "debug");
		self::taskMessage("OnFinish [$this->mailToWhenFinished]", "debug");
		self::taskMessage("Task-Name [".$this->task_name."]", "info");
		self::taskMessage("Task-ID [".$this->task_id."]", "info");
		self::taskMessage("Task started...\n", "info");
	}
	
	public function taskMessage($msg, $level="debug")
	{
		if (in_array($level, $this->task_messages_levels)){
			$this->task_messages[$level][] = $msg;
			$this->task_messages["chronic"][] = $msg;
			
			$msg = "[".strtoupper($level)."] ".$msg."\n";
			if ($this->is_cli()){
				echo $msg;
			}else {
				echo nl2br($msg);
			}
		}else{
			throw new Exception("invalid level provided");
		}
	}
	
	public function getMessages($level="ALL", $return=true)
	{
		$br		= "<br>";
		$isCLI 	= $this->is_cli();
		if ($isCLI){
			$br = "\n";
		}

		if ($level == "ALL"){
			if ($return){
				return $this->task_messages["chronic"];
			}
			else{
				echo implode($br, $this->task_messages);
			}
		}
		else
		{
			if (array_key_exists($level, $this->task_messages))
			{
				if ($return){
					return $this->task_messages[$level];
				}
				else 
				{
					echo implode($br, $this->task_messages[$level]);
				}
			}
		}
	}
	
	/**
	 * Call this at the end, when your task has finished all jobs 
	 * 
	 * @author Marco Eberhardt
	 * @version 1.0
	 */
	public function task_finished()
	{
		$errors 	= $this->getMessages("error");
		$runtime	= time() - $this->starttime();
		$message	= "Task finished with at [".time()."] with [".count($errors)."] errors. Runtime [".($runtime)."]";
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->taskMessage(str_repeat("_", 80), "debug");
		$this->taskMessage($message, "debug");
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Log the Finish
		$this->logEvent(E_TASK_EVENT::NOTICE, implode("\n - ", $this->getMessages()));
		
		if (count($errors) > 0)
		{
			$this->logEvent(E_TASK_EVENT::ERROR, "Errors:\n".print_r($errors, true));
		}
		$this->logEvent(E_TASK_EVENT::STOP, $message);
	}
	
	/**
	 * @param string $eventname
	 * @param string $comment
	 * @throws Exception
	 */
	public function logEvent($eventname, $comment="")
	{
		if ($this->logEvents === false){
			// logging disabled
			return;
		}
	
		if ( E_TASK_EVENT::isValidValue($eventname) === false){
			throw new Exception("invalid event name ($eventname) provided");
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($eventname == E_TASK_EVENT::START){
			// remember the start time
			$this->starttime = time();
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($eventname == E_TASK_EVENT::ERROR and trim($this->mailToOnError) != "")
		{
			if ($this->sendMails === true)
			{
				$data = array(
					"task_name"=>$this->task_name,
					"task_details"=> implode("<br><li>", self::getMessages("error") )
				);
				
				$result = $this->base_mailer->send_emailFromTemplate(E_MAIL_TEMPLATES::TASK_ERROR, $this->mailToOnError, $data, array(), null);
			}
		
		}
		else if ($eventname == E_TASK_EVENT::ERROR and trim($this->mailToOnError) === "" ){
			log_message("error", "task error in $this->task_name:".$comment);
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($eventname == E_TASK_EVENT::STOP and $this->starttime !="")
		{
			$runtime 	= time() - $this->starttime;
			$comment 	= "runtime: $runtime\n\n".$comment;
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			if (trim($this->mailToWhenFinished) != "")
			{
				if ($this->sendMails === true)
				{
					$data = array(
						"task_name"=>$this->task_name,
						"task_details"=> implode("<br>", self::getMessages("ALL") )
					);
					
					$result = $this->base_mailer->send_emailFromTemplate(E_MAIL_TEMPLATES::TASK_FINISHED, $this->mailToWhenFinished, $data, array(), null);
				}
			}
		} 
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->log_taskEvent($this->task_name, $eventname, $comment, $this->task_id);
	}
	
	
	public function log_taskEvent($taskname, $eventname, $comment, $taskID)
	{
		$insert_array = array(
			"task_id"				=>  $taskID,
			"task_name"				=>	$taskname,
			"event_time" 			=> 	date("Y-m-d H:i:s"),
			"event_type"			=> 	$eventname,
			"comment"				=> 	$comment
		);
		$this->db->insert(TBL_LOG_TASKS, $insert_array);
	}
	
	/**
	 * check for command line interface
	 * @return boolean
	 */
	public function is_cli()
	{
		if( defined('STDIN') ){
			return true;
		}
			
		if( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0){
			return true;
		}
			
		return false;
	}
	
	/**
	 * Getter for the starttime
	 * @return string
	 */
	public function starttime(){
		return $this->starttime;
	}
}