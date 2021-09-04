<?php

require_once( APPPATH.'/core/BASE_Task.php');
require_once( APPPATH.'/core/BASE_Enum.php');

abstract class E_IMPORT_MODES extends BASE_Enum
{
	CONST IMPORT_1	= 1;
	CONST IMPORT_2	= 2;
	CONST IMPORT_3	= 4;
	CONST IMPORT_4	= 8;
}

/**
 * Task Controller
 *
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\tasks\Task_import
 * @version 1.0
 */
class Task_import extends BASE_Task
{
	protected $time_now;
	protected $date_today;
	
	private $debug_mode 	= 1;
	private $client_id 		= null;
	
	public function __construct()
	{
		parent::__construct(false);
		
		$this->load->model('tasks/Task_import_model', 'task_model');
		
		$this->time_now 				= time();	
		$this->date_today				= date("Y-m-d", $this->time_now);
	}
	
	public function index(){
		die ("forbidden");
	}
	
	/**
	 * Run the job(s)
	 *
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 * @param string $client_id
	 * @param int $import_mode
	 * @param number $debug
	 */
	public function run($client_id, $import_mode=null, $debug=1)
	{
		$this->taskMessage("CLIENT[$client_id] IMPORT-MODE[$import_mode] DEBUG-MODE [$debug]", "info");
		
		$this->client_id 	= $client_id;
		$this->debug_mode	= $debug;
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if($import_mode & E_IMPORT_MODES::IMPORT_1)
		{
			$this->import_1();
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if($import_mode & E_IMPORT_MODES::IMPORT_2)
		{
			$this->import_2();
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if($import_mode & E_IMPORT_MODES::IMPORT_3)
		{
			$this->import_3();
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if($import_mode & E_IMPORT_MODES::IMPORT_2)
		{
			$this->import_4();
		}
		
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->task_finished();
	}
	
	private function import_1()
	{
		$this->taskMessage("processing import 1", "info");
	}
	
	private function import_2()
	{
		$this->taskMessage("processing import 2", "info");
	}
	
	private function import_3()
	{
		$this->taskMessage("processing import 3", "info");
	}
	
	private function import_4()
	{
		$this->taskMessage("processing import 4", "info");
	}
}