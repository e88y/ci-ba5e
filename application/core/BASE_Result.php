<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BASE_Result - The generic data object. 
 * 
 * @todo create setter methods
 * 
 * @author Marco Eberhardt
 * @category value object
 * @package application\core\BASE_Result
 * @version 1.0
 */
class BASE_Result
{
	/**
	 * Contains the data as array. 
	 * On error it will usually set to null.
	 * @var array
	 */
	public $data 	= array();
	
	/**
	 * contains the final error string or NULL
	 * @var string
	 */
	public $error 	= null;
	
	/**
	 * contains warning string or NULL
	 * @var string
	 */
	public $warning 	= null;
	
	/**
	 * contains the info string or NULL
	 * @var string
	 */
	public $info = null;
	
	/**
	 * contains the success string or NULL
	 * @var string
	 */
	public $success 	= null;
	
	/**
	 * contains messages or NULL
	 * @var string
	 */
	public $messages 	= null;
	
	/**
	 * addditional data (like the validation_array) can be stored here if needed 
	 * @var mixed
	 */
	public $extra 	= null;
	
	/**
	 * Status code as string (SUCCESS, ERROR, DB_ERROR)
	 * @var E_STATUS_CODE
	 */
	public $status	= "";
	
	/**
	 * indicates that a redirect must be made. usually in combination with error
	 * @var string
	 */
	public $redirect	= null;
	
	
	/**
	 * Constructor for the BASE_Result
	 * 
	 * @todo extend constructor with new properties (message, success, etc.)
	 * 
	 * @param array $data				>> array with results
	 * @param string $error				>> null or error string
	 * @param mixed $extra				>> extra data array or string for what ever
	 * @param E_STATUS_CODE $status		>> status code
	 *
	 * @return BASE_Result
	 */
	public function __construct($data=null, $error=null, $extra=null, $status="SUCCESS", $redirect=null)
	{
		if (!E_STATUS_CODE::isValidValue($status)){
			throw new Exception("invaid status code provided");
		}
		
		$this->data 	= $data;
		$this->extra	= $extra;
		//$this->messages	= $messages;
		$this->status	= $status;
		$this->error 	= $error;
		$this->redirect	= $redirect;
		//$this->warning	= $warning;
		//$this->info 	= $info;
		//$this->success 	= $success;
		return $this;
	}

	/**
	 * Getter for the data property
	 * @return multitype:
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * Getter for the error property
	 * @return string
	 */
	public function getError(){
		return $this->error;
	}
	
	/**
	 * Getter for the warning property
	 * @return string
	 */
	public function getWarning(){
		return $this->warning;
	}
	
	/**
	 * Getter for the info property
	 * @return string
	 */
	public function getInfo(){
		return $this->info;
	}
	
	/**
	 * Getter for the success property
	 * @return string
	 */
	public function getSuccess(){
		return $this->success;
	}
	
	/**
	 * Getter for the extra property
	 * @return mixed
	 */
	public function getExtra(){
		return $this->extra;
	}
	
	/**
	 * Getter for the status property
	 * @return E_STATUS_CODE
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * Return the BASE_Result as array 
	 * @return array
	 */
	public function result_array()
	{
		$arr = array(
			"data"=>$this->data, 
			"extra"=>$this->extra,
			"messages"=>$this->messages,
			"status"=>$this->status,
			"error"=>$this->error,
			"warning"=>$this->warning,
			"info"=>$this->info,
			"success"=>$this->success
		);
		return $arr;
	}
	
	/**
	 * Returns an empty BASE_Result as array 
	 * @return array
	 */
	static function getEmpty()
	{
		return array(
			"data"=>array(), 
			"extra"=>array(),
			"messages"=>array(),
			"status"=>E_STATUS_CODE::SUCCESS,
			"error"=>"",
			"warning"=>"",
			"info"=>"",
			"success"=>""
		);
	}
	
	static function create($status, $data=array(), $extra=null, $messages=null, $error="", $info=null, $warning=null, $success=null){
		$result = new BASE_Result($data, $error, $extra, $status, $redirect);
		$result->messages	= $messages;
		$result->warning	= $warning;
		$result->info 		= $info;
		$result->success 	= $success;
		return $result;
	}
}

?>