<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BASE_Model - Extension of the standard CI_Model with a lot of common database related helper methods and shortcut functions
 * All models should extend from this class.  
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\core\BASE_Model
 * @version 1.0
 */
class BASE_Model extends CI_Model
{
	const DEBUG_FILENAME = "BASE_Model.log";
	
	/**
	 * BASE_Model constructor
	 * 
	 * @param bool $saveQueries	>> not in use
	 */
	public function __construct($saveQueries = true)
	{
		parent::__construct();
		$active_group 	= (isset($_SERVER['DB_ACTIVE_GROUP']) ? $_SERVER['DB_ACTIVE_GROUP'] : 'default');
		$this->db 		= $this->load->database($active_group, TRUE);
	}

	/**
	 * load another database into $this->db
	 * 
	 * @param string $database
	 * @throws Exception
	 */
	public function load_database($database){
		
		$available_databases = array("default", "models");
		
		if (in_array($database, $available_databases))
		{
			$this->db = $this->load->database($database, TRUE);
		}
		else{
			throw new Exception("unkown database '$database'");
		}
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: Generic Database operations ::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * Run a transaction with one or more queries. 
	 * By default the queries will not be escaped. Set 2nd param to <code>true</code> to do so
	 *
	 * @param array $queries		>> array containing all queries
	 * @param bool $escape_queries	>> runs the escape-method for each query if true (default:false) 
	 * @return BASE_Result
	 */
	public function BASE_Transaction($queries, $escape_queries=false)
	{
		if (!is_array($queries) or count($queries) == 0){
			throw new Exception("array with one or more queries expected");
		}

		write2Debugfile(self::DEBUG_FILENAME, "BASE_transaction with ".count($queries)." queries-".print_r($queries, true));
		
		$this->db->trans_begin();
		
		foreach ($queries as $query) 
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n STATUS[".$this->db->trans_status()."]\n".$query);
			if ($escape_queries === true){
				$this->db->query($this->db->escape($query));
			}
			else{
				$this->db->query($query);
			}
		}
		
		$error = "";
		if ($this->db->trans_status() === FALSE)
		{
			$error = self::generateErrorMessage();
			
			$this->db->trans_rollback();
			$status = E_STATUS_CODE::DB_ERROR;
			
			write2Debugfile(self::DEBUG_FILENAME, "\n\n => Transaction failed ==> Rolling back");
		}
		else
		{
			$status = E_STATUS_CODE::SUCCESS;
			if ($this->db->trans_commit() === false);
			{
				//$status = E_STATUS_CODE::DB_ERROR;
			}
			
			write2Debugfile(self::DEBUG_FILENAME, "\n\n => Tranaction complete");
		}
		
		return new BASE_Result( ($error == "" ? true:false), $error, null, $status);
	}
	
	/**
	 * Insert data - Will simply insert data using <code>$this->db->insert($tablename, $data);</code> and return a BASE_Result-Object.
	 * Fields are automatically escaped.
	 *
	 * @param string $tablename		>> the database table
	 * @param array $data			>> associative array with the data.<br><code>$data = array( 'title' => 'My title', 'name' => 'My Name', 'date' => 'My date');</code>
	 *
	 * @return BASE_Result
	 */
	public function BASE_Insert($tablename, $data)
	{
		$this->db->insert($tablename, $data);
		
		$error 	= self::generateErrorMessage();
		$status = ($error != null ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);

		$extra	= array(
			"affected_rows" => $this->getAffectedRows(),
			"insert_id" => $this->getInsertID()
		);
		
		$result = new BASE_Result(null, $error, $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Insert\n".print_r($this->db->queries,true)."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Insert multiple rows using <code>$this->db->insert_batch($tablename, $data);</code><br>
	 * Fields are automatically escaped
	 * 
	 * Note: 
	 * 	The extra portion in the BASE_Result contains the key 'insert_id' from <code>$this->db->insert_id()</code>. You need to know, that codeigniter returns only the id of the first insert.
	 *  The last insert id will be <code>$extra["insert_id"] + count($data) -1 </code> if the batch was sucessfull. 
	 *
	 * @param string $tablename >> the database table
	 * @param array $data >>  associative array with the data to store.<br><code>
	 * 	$data = array(<br>
	 * 	&nbsp;&nbsp;&nbsp;&nbsp;array( 'title' => 'My title', 'name' => 'My Name', 'date' => 'My date'),<br>
	 * 	&nbsp;&nbsp;&nbsp;&nbsp;array( 'title' => 'Another title', 'name' => 'Another Name', 'date' => 'Another date')<br>
	 * 	);</code><br>
	 *
	 * @return BASE_Result
	 */
	public function BASE_InsertBatch($tablename, $data)
	{
		$this->db->insert_batch($tablename, $data);
	
		$error 	= $this->db->error();	// Has keys 'code' and 'message'
		$status = ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra	= array(
			"affected_rows" => $this->getAffectedRows(),
			"insert_id" => $this->getInsertID()
		);
		
		$result = new BASE_Result(null, self::generateErrorMessage(), $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_InsertBatch\n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Replace data - Replace a row using <code>$this->db->replace($tablename, $data);</code><br>
	 * Fields are automatically escaped<br><br>
	 * This method executes a REPLACE statement, which is basically the SQL standard for (optional) DELETE + INSERT, using PRIMARY and UNIQUE keys as the determining factor.
	 *
	 * @param string $tablename >> the database table
	 * @param array $data >> data to replace as associative array.<br><code>$data = array( 'title' => 'My title', 'name' => 'My Name', 'date' => 'My date');</code>
	 *
	 * @return BASE_Result
	 */
	public function BASE_Replace($tablename, $data)
	{
		$this->db->replace($tablename, $data);
	
		$error		= $this->db->error();	// Has keys 'code' and 'message'
		$status 	= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra	= array(
			"affected_rows" => $this->getAffectedRows()
		);
		
		$result = new BASE_Result(null, self::generateErrorMessage(), $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Replace\n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Select and return a BASE_Result 
	 * 
	 * @param string $tablename		>> the table name
	 * @param array $where			>> pass an array like <code>array('field1'=>'value', 'field2'=>'value');</code> or a where statement as string
	 * @param string $fields 		>> komma-seperated fields
	 * @param array $orderBy		>> pass an array like <code>array('field1'=>'asc', 'field2'=>'desc')</code>
	 * @param string $limit			>> 
	 * @param string $limitOffset	>>
	 * @param bool $returnObjectOnLimit_1 	>> Return Object when limit is set to 1
	 * @param bool $resultAsArray	>> should BASE_Result->data contain an array or object 
	 * 
	 * @return BASE_Result
	 */
	function BASE_Select($tablename, $where=array(), $fields="*", $orderBy=array(), $limit=null, $limitOffset=null, $returnObjectOnLimit_1=true, $resultAsArray=false)
	{
		$this->db->select($fields)->from($tablename);

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (is_array($where) && count($where) > 0)
		{
			foreach ($where as $field=>$value)
			{
				if (is_array($value)===false)
				{
					$this->db->where($field, $value);
				}
			}
		}
		elseif (is_string($where) == true)
		{
			$this->db->where($where);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (is_array($orderBy) && count($orderBy) > 0)
		{
			foreach ($orderBy as $field=>$sort)
			{
				$this->db->order_by($field, $sort);
			}
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($limit && is_int($limit) && $limit > 0)
		{
			if($limitOffset && is_int($limitOffset) && $limitOffset > 0) {
				$this->db->limit($limit, $limitOffset);
			}
			else {
				$this->db->limit($limit);
			}
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$query = $this->db->get();
		$data	= null;
		$error 	= self::generateErrorMessage();
		$status = ($error != null ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		
		if ($query && $error == "")
		{
			$extra	= array(
				"num_rows" => $query->num_rows(),
				"num_fields" => $query->num_fields()
			);
			
			if ($limit == 1 && $returnObjectOnLimit_1 === true)
			{
				$data = $query->row();
			}
			else {
				if ($resultAsArray){
					$data = $query->result_array();
				}else{
					$data = $query->result_object();
				}
			}
		}
			
		$result = new BASE_Result($data, $error, $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Select -> \n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Shortcut to perform BASE_Select with result as array 
	 * 
	 * @param string $tablename
	 * @param array $where
	 * @param string $fields
	 * @param array $orderBy		>> array("field_1"=>"desc", "field_2"=>"asc")
	 * @param int $limit
	 * @param int $limitOffset
	 * 
	 * @return BASE_Result
	 */
	function BASE_SelectArray($tablename, $where=array(), $fields="*", $orderBy=array(), $limit="", $limitOffset="")
	{
		return self::BASE_Select($tablename, $where, $fields, array(), $limit, $limitOffset, false, true);
	}
	
	/**
	 * Update data - Update a row using '$this->db->update($tablename, $data);' and '$this->db->where($where);'
	 * Fields are automatically escaped.
	 * 
	 * @param string $tablename >> the database table
	 * @param array $data >> associative array with the new data. <br><code>$data = array( 'title' => 'My title', 'name' => 'My Name', 'date' => 'My date');</code>. You can also supply an object.
	 * @param array $where >> associative array for the where statement.<br><code>$where = array( 'id'=>'1');</code> You can also pass a string like <code>id = 1</code>
	 * @return BASE_Result
	 */
	public function BASE_Update($tablename, $data, $where)
	{
		foreach ($where as $field => $value){
			$this->db->where($field, $value);
			
			// remove where-fields from the data array 
			unset($data[$field]);
			
		}
		$this->db->update($tablename, $data);
		
	
		$error 		= $this->db->error();	// Has keys 'code' and 'message'
		$status 	= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra		= array(
			"affected_rows" => $this->getAffectedRows()
		);
		$data 		= ($this->getAffectedRows() > 0 ? 1:0);
		$error_msg 	= self::generateErrorMessage();
		
		$result = new BASE_Result($data, $error_msg, $extra, $status);
		
		if ($error_msg == "" && $this->getAffectedRows() == 0){
			$result->info = lang("msg_no_rows_affected");
		}
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Update\n".$this->lastQuery()."\naffectedRows[".$this->getAffectedRows()."] error-".print_r($error, true)."result-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Update multiple rows - Update a row using <code>$this->db->update_batch($tablename, $data, $where);</code><br>
	 * Generates an update string based on the data you supply, and runs the query. The first parameter will contain the table name, the second is an associative array of values, the third parameter is the where key.
	 *
	 * @param string $tablename >> the database table
	 * @param array $data 		>> associative array with the new data. <br><code>$data = array( 'title' => 'My title', 'name'=>'New Name', 'date'=>'New date');</code>. You can also supply an object.
	 * @param string $where		>> the where key (e.g. title)
	 * @return BASE_Result
	 */
	public function BASE_UpdateBatch($tablename, $data, $where)
	{
		/**
		 * the update_batch method itself only supports one single string as the where key - Bummer
		 * @todo maybe we can utilize the WHERE-Method >> $this->db->where('name','My Name 2');
		 */
		$affected_rows = $this->db->update_batch($tablename, $data, $where);
	
		$error 		= $this->db->error();	// Has keys 'code' and 'message'
		$status 	= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra	= array(
			"affected_rows" => $this->getAffectedRows()
		);
	
		$result = new BASE_Result(null, self::generateErrorMessage(), $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_UpdateBatch\n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Delete row(s) - Delete one or more rows using <code>$this->db->delete($tablename);</code><br>
	 *
	 * @param array|string $tables 	>> the tablename from where you want to delete. You can also pass an array with tables to delete from more than one table (<code>$tables = array( 'table1', 'table2');</code>)
	 * @param array $where			>> associative array for the where statement.<br><code>$where = array( 'id'=>'1');</code>
	 * @param bool	$useOrWhere		>> if set to true, <code>or_where()</code> will be used instead of <code>where()</code>
	 * @return BASE_Result
	 */
	public function BASE_Delete($tables, $where, $useOrWhere=false)
	{
		foreach ($where as $field => $value)
		{
			if ($useOrWhere === true){
				$this->db->or_where($field, $value);
			}else{
				$this->db->where($field, $value);
			}
		}
		$this->db->delete($tables);
	
		$error 		= $this->db->error();	// Has keys 'code' and 'message'
		$status 	= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra	= array(
			"affected_rows" => $this->getAffectedRows()
		);
	
		$result = new BASE_Result(null, self::generateErrorMessage(), $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Delete\n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Truncate a table - If the TRUNCATE command isn't available, truncate() will execute as "DELETE FROM table".
	 *
	 * @param string $tablename
	 * @return BASE_Result
	 */
	public function BASE_Truncate($tablename)
	{
		$this->db->truncate('mytable');
	
		$error 		= $this->db->error();	// Has keys 'code' and 'message'
		$status 	= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra	= array(
			"affected_rows" => $this->getAffectedRows()
		);
	
		$result = new BASE_Result(null, self::generateErrorMessage(), $extra, $status);
		
		write2Debugfile(self::DEBUG_FILENAME, "BASE_Truncate\n".$this->lastQuery()."\nresult-".print_r($result, true));
		return $result;
	}
	
	/**
	 * Write into history-Tables
	 *
	 * @param string $client_id
	 * @param string $table		>> Table of database action
	 * @param string $action	>> action (INSERT,UPDATE,....)
	 * @param array	$keyarray	>> array of keys for databaseoperation (like "client_id"=>x,"customer_id"=> .....)
	 * @param array $data		>> whole Data Array
	 * @param array $prior_data	 >> whole Previous Data Array
	 * @param string $username	>> Name of user
	 * @return BASE_Result
	 */
	public function InsertHistory($client_id,$table,$action,$keyarray=array(),$data=array(),$prior_data=array(),$username='')
	{
		
		/*
		 * TBL_HISTORY
		 * TBL_HISTORY_DETAILS
		 * 
		 */
		$new_history_id = $this->generateUID(TBL_HISTORY, 'history_id');
		// 1. insert into TBL_HISTORY
		$dataForHistoryMainTable = array(
			"client_id"			=>$client_id,
			"history_id"			=>$new_history_id,
			"table_name"		=>$table,
			"db_action"			=>$action,
			"json_key"			=>(!empty($keyarray)?json_encode($keyarray,JSON_UNESCAPED_UNICODE):''),
			"created_at"			=>time(),
			"created_by"		=>$username
		);
		
		write2Debugfile('InsertHistory.log', print_r($dataForHistoryMainTable, true));
		$this->BASE_Insert(TBL_HISTORY, $dataForHistoryMainTable);
		$differences = array();
		//2. insert into  TBL_HISTORY_DETAILS
		
		if((($prior_data)) && (is_array($data) && !empty($prior_data)))
		{
			foreach($data as $k=>$v)
			{
				if($prior_data[$k] != $v)
				{
					$differences[$k] = array("new"=>$v,"old"=>$prior_data[$k]);
				}
				
			}
		}
		
		$dataForHistoryDetailsTable = array(
			"client_id"			=>$client_id,
			"history_id"			=>$new_history_id,
			"json_differences"		=>(!empty($differences)?json_encode($differences,JSON_UNESCAPED_UNICODE):''),
			"json_before_update"	=>(!empty($prior_data)?json_encode($prior_data,JSON_UNESCAPED_UNICODE):''),
			"json_key"			=>(!empty($keyarray)?json_encode($keyarray,JSON_UNESCAPED_UNICODE):''),
			"json_data"			=>(!empty($data)?json_encode($data,JSON_UNESCAPED_UNICODE):'')
		);
		$this->BASE_Insert(TBL_HISTORY_DETAILS, $dataForHistoryDetailsTable);
		//$this->loadSingleHistoryEntry($client_id,$new_history_id);
	}
	
	/**
	 * 
	 * @param string $client_id >>ID of Client
	 * @param string $history_id >> ID of History
	 * @return array
	 */
	function loadSingleHistoryEntry($client_id,$history_id)
	{
		$return = array();
				
		
		//$this->db->select('*');
		$this->db->select(TBL_HISTORY.'.table_name, '.TBL_HISTORY.'.db_action, '.TBL_HISTORY.'.json_key, '.TBL_HISTORY_DETAILS.'.json_differences, '.TBL_HISTORY_DETAILS.'.json_data'); 
		$this->db->from(TBL_HISTORY);
		$this->db->join(TBL_HISTORY_DETAILS,
			TBL_HISTORY_DETAILS.'.client_id = '.TBL_HISTORY.'.client_id'.
			" AND ".TBL_HISTORY_DETAILS.'.history_id = '.TBL_HISTORY.'.history_id',
			'inner'
			);
		$this->db->where(TBL_HISTORY.'.client_id',$client_id);
		$this->db->where(TBL_HISTORY.'.history_id',$history_id);
		$query = $this->db->get();
		
	
		if($query)
		{
			$result = $query->result_array();
			
			foreach($result as $result_K=>$result_V)
			{
				if(trim($result_V['json_differences']) != '')
				{
					$return_array = json_decode($result_V['json_differences'], true);
					foreach($return_array as $return_array_K => $return_array_V)
					{
						$old			= $this->replaceValuesForHistoryDetails($return_array_K,$return_array_V['old'],$client_id,$return_array_V['table_name'],$return_array_V['json_data']);
						$new			= $this->replaceValuesForHistoryDetails($return_array_K,$return_array_V['new'],$client_id,$return_array_V['table_name'],$return_array_V['json_data']);
						$keyfield		= $this->replaceKeyfieldForHistoryDetails($return_array_K);
						
						$bindestrich = "&nbsp;&rarr;&nbsp;";
						if(trim($old)== '')
						{
							$bindestrich = "";
						}
						if(trim($new)== '')
						{
							$new = lang('empty');
						}
						$html_string		= "<b>".$keyfield."</b>".":<br>".$old.$bindestrich.$new;
						$string		= $keyfield.": ".$old." - ".$new;
						$array		= array("field"=>$return_array_K,"old"=>$old,"new"=>$new);
						$return[]		= array("html_string_history"=>$html_string,"string_history"=>$string,"array_history"=>$array);
					}
				}
				else if(trim($result_V['json_data']) != '' && $result_V['table_name']==TBL_DEBITOR_DOCUMENTS)
				{
					$array = json_decode($result_V['json_data'],true);
					if($array !== false)
					{						
						$html_string		= "<b>".lang('custom_document_name').":</b>"."<br>".$array['custom_document_name'];
						$html_string		.= "<br>"."<b>".lang('filename').":</b>"."<br>".$array['filename'];
						$string		= lang('custom_document_name').$array['custom_document_name'].lang('filename').$array['filename'];
						$array		= array("field"=>'',"old"=>'',"new"=>'');
						$return[]		= array("html_string_history"=>$html_string,"string_history"=>$string,"array_history"=>$array);
					}
					else
					{
						$html_string		= $result_V['db_action'] == 'INSERT'?lang('INSERT'):lang('no_changes_detected');
						$string		= $result_V['db_action'] == 'INSERT'?lang('INSERT'):lang('no_changes_detected');
						$array		= array("field"=>'',"old"=>'',"new"=>'');
						$return[]		= array("html_string_history"=>$html_string,"string_history"=>$string,"array_history"=>$array);
					}
				}
				else
				{
					$html_string		= $result_V['db_action'] == 'INSERT'?lang('INSERT'):lang('no_changes_detected');
					$string		= $result_V['db_action'] == 'INSERT'?lang('INSERT'):lang('no_changes_detected');
					$array		= array("field"=>'',"old"=>'',"new"=>'');
					$return[]		= array("html_string_history"=>$html_string,"string_history"=>$string,"array_history"=>$array);
				}
			}
		}
		
		
		$error			= $this->db->error();
		$status		= ($error["message"] != "" ? E_STATUS_CODE::DB_ERROR : E_STATUS_CODE::SUCCESS);
		$extra		= array( );
		$data 		= ($this->getAffectedRows() > 0 ? 1:0);
		$error_msg		= self::generateErrorMessage();
		
		$result = new BASE_Result($return, $error_msg, $extra, $status);
		return $return;
		
	}
	
	/**
	 * 
	 * @param string $field		>> Database Field
	 * @param string $value		>> Value to replace
	 * @return string			>> if no replace happens, value will be returned
	 */
	function replaceValuesForHistoryDetails($field,$value,$client_id,$table='',$json_data= "")
	{
		$arrayTimestampFields = array(
			'anamnese_at', 'no_additional_payment_from_date', 'no_additional_payment_till_date', 'locked_at', 'locked_till', 'birthday'
		);
		
		$arrayYesNoFields = array(
		// Debitor - Anamnese
		'caregiver_is_partner','caregiver_is_child', 'caregiver_is_parent','caregiver_is_caregiver',
		'stool_severity_level_1', 'stool_severity_level_2', 'stool_severity_level_3',  'insured_advice','caregiver_advice','copayment_exemption', 'sending_care_himi_request',
		'mobile_full', 'mobile_partially', 'mobile_wheelchair', 'mobile_bed', 'inco_type_urine', 'inco_type_stool', 'inco_type_urine_and_stool',
		'incontinence_form_light', 'incontinence_form_medium', 'incontinence_form_severe',
		'diuretic_drugs', 'diuretic_drug_morning', 'diuretic_drug_noon', 'diuretic_drug_evening',
		'care_by_nursing_service','care_by_nurse','care_by_relatives','care_by_self','recipe_is_available','sample_delivery',
		'urine_severity_level_1','urine_severity_level_2','urine_severity_level_3','stool_incontinence_fluid','stool_incontinence_thick',
		// Debitor
		'generate_payment_contract','no_additional_payment','is_deceased','locked',

		);
		
		$arrayReplaceWithValuesFields = array(	
		't1_customer_kind'		=>array('table'=>TBL_CUSTOMER_KIND,			"select_field"=>'customer_kind_name',		"where_field"=>'customer_kind_id',			"with_client_id"=>0),
		't3_customer_type'	=>array('table'=>TBL_CUSTOMER_TYPE,			"select_field"=>'customer_type_name',		"where_field"=>'customer_type_id',			"with_client_id"=>0),
		't2_customer_group'	=>array('table'=>TBL_CUSTOMER_GROUP,		"select_field"=>'customer_group_name',		"where_field"=>'customer_group_id',			"with_client_id"=>0),
		'locked_by'			=>array('table'=>TBL_USER,					"select_field"=>"username",				"where_field"=>"user_id",				"with_client_id"=>0),
		'sales_group'		=>array('table'=>TBL_SAMPLE_SALES_GROUPS,	"select_field"=>"sample_order_account_group",	"where_field"=>"sample_order_account_id",	"with_client_id"=>0),
		'team_id'			=>array('table'=>TBL_TEAMS,					"select_field"=>"team_name",				"where_field"=>"team_id",				"with_client_id"=>1),
		'skonto'			=>array('table'=>TBL_S_ACCOUNT,				"select_field"=>"s_account_name",			"where_field"=>"s_account_id",			"with_client_id"=>0),
		 'payment_model'		=>array('table'=>TBL_PAYMENT_MODEL,			"select_field"=>"payment_model_name",		"where_field"=>"payment_model_id",			"with_client_id"=>0),
		'payment'			=>array('table'=>TBL_PAYMENT_METHOD,		"select_field"=>"payment_method_name",		"where_field"=>"payment_method_id",		"with_client_id"=>0),
		 'payment_terms'		=>array('table'=>TBL_PAYMENT_TERMS,			"select_field"=>"name",					"where_field"=>"payterm_id",		"with_client_id"=>0),
		);
		
		$arrayTranslateValue = array(
		 'salutation'
		);
		
		$arrayValueFromEnum = array(
		 
		);
		if(in_array($field,$arrayTimestampFields))
		{
			if($value != '')
			{
				$value	= format_timestamp2date($value);
			}
		}
		else if(in_array($field,$arrayYesNoFields))
		{
			$value	= ($value==1?lang('yes'):lang('no'));	
		}
		else if(array_key_exists($field,$arrayReplaceWithValuesFields))
		{
			$table		= $arrayReplaceWithValuesFields[$field]['table'];
			$select	= $arrayReplaceWithValuesFields[$field]['select_field'];
			$where	= array($arrayReplaceWithValuesFields[$field]['where_field']=>$value);
			if($arrayReplaceWithValuesFields[$field]['with_client_id']==1)
			{
				$where['client_id']		=	$client_id;
			}
			$result = $this->BASE_Select($table, $where, $select, array(), null, null, true, true);
			write2Debugfile('replaceValuesForHistoryDetails.log', print_r($this->db->last_query(), true));
			if($result->error == '')
			{
				$value = $result->data[0][$arrayReplaceWithValuesFields[$field]['select_field']];
			}
			
			
		}
		else if(in_array($field,$arrayTranslateValue))
		{
			$value = lang($value);
		}
		return $value;
	}
	
	function replaceKeyfieldForHistoryDetails($keyfield)
	{
		$keyfield = lang($keyfield);
		return $keyfield;
	}
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: Helper Methods :::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * Generates a HTML database error message meant for BASE_Result["error"]
	 * 
	 * If you dont pass anything to that function, it will look if an database error ocured and fill it up automaticly.
	 * If the log_threshold is greater 0 and the ENVIRONMENT is not set to production, the query and the database error message will also be added to the details.
	 *
	 * @param string $error
	 * @param string $errorDetail
	 *
	 * @return mixed >> null or the error string
	 */
	function generateErrorMessage($error="", $errorDetail="")
	{
		$str = null;
		
		if ($error == "" && $errorDetail == "")
		{
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// ..:: Auto check for database errors :::::::::::::::::::::::::::::::::::::::..
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$dbError = $this->db->error();
			
			if ($dbError["message"] != "")
			{
				$error	 = "<h1>".lang("sorry")."</h1><p>".lang("msg_database_error")."<br>".lang("error_code").": ".$dbError["code"]."<br></p>";		// per default we only show the Error-Code
				//$error	.= $dbError["message"];
					
				if (config_item('log_threshold') > 0 && ENVIRONMENT != E_ENVIRONMENT::PRODUCTION)
				{
					// append the error message and the last query to the details
					
					$errorDetail = $dbError["message"]."<br><br>".$this->lastQuery();
					log_message("error", "Model-Error [".$dbError["code"]."]:\n".$dbError["message"]."\n\n".$this->lastQuery() );
				}
				
				write2Debugfile("DB-Error.log", "\ngenerateErrorMessage[".print_r(get_called_class(), true)."] Error-Code [".$dbError["code"]."]\nMessage: ".$dbError["message"]."\nQuery:\n".$this->lastQuery(), true);
				
			}else{
				
			}
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($error != "" || $errorDetail != ""){
			$str = nl2br($error."<br>".$errorDetail);
			
			//write2Debugfile("DB-Error.log", "::generateErrorMessage - output [".$str."]");
		}
		write2Debugfile("DB-Error.log", "\nreturn: ".$str, true);
		
		return $str;
	}
	
	
	/**
	 * Generates a unique identifier using uniqid-method or rand-method
	 * EDITED 17.10.2018:meb >> $justNumber will be hardcoded overwritten to 'true'
	 * 
	 * @access static
	 
	 * @param string $table			>> the database table, where you want to generate a new ID
	 * @param string $field			>> the table column where the id will take place
	 * @param string $prefix		>> prefix for the id
	 * @param bool $justNumber		>> set to true, idf you just want a numeric value (rand-method will be used). Note that this beats the prefix value
	 * @param int $length 			>> output length
	 * 
	 * @return string 				>> new ID 
	 */
	static function generateUID($table, $field, $prefix="", $justNumber=false, $length=16, &$generated = null)
	{
		$ok 		= false;
		$justNumber = true;
		if (is_null($generated))
		{
			$generated = array();
		}
		while($ok === false)
		{
			if (!$justNumber)
			{
				$CI =& get_instance();
				$CI->load->helper('string');
				
				if ($length != null){
					$id = $prefix . random_string("alnum", ($length - strlen($prefix)) );
				}
				else{
					$id = $prefix . random_string();
				}
				$id = strtolower($id);
			}
			else{
				$id = rand(0, 2000000000);
				
				if ($length != null){
					$id = substr($id, 0, $length);
				}
			}
	
			//$id = str_replace(".", "_", $id);
			if (self::issetID($id, $table, $field) === false && !in_array($generated))
			{
				$ok = true;
				$generated[] = $id;
			}
		}
		
		write2Debugfile(self::DEBUG_FILENAME, "::generateUID table[$table] UniqueID[".$id."]");
		return $id;
	}
	

	/**
	 * Generates a username using uniqid-method
	 *
	 * @access static
	
	 * @param string $prefix		>> prefix for the id
	 *
	 * @return string 				>> new ID
	 */
	static function generateUsername($prefix="")
	{
		$ok = false;
	
		$count = 0;
		if ($prefix == "")
		{
			$prefix = random_string();
		}
		if (strlen($prefix) > 35)
		{
			$prefix = substr($prefix, 0, 35);
		}
		$postfix = random_string("alnum",5);
		while($ok === false)
		{
			$id = $prefix."_".$postfix;
			if ($count > 0)
			{
				$id = $prefix."_".$count.$postfix;
			}
			if (self::issetID($id, "app_user", "username") === false)
			{
				$ok = true;
			}
			$count++;
			if ($count == 999)
			{
				$count = 0;
				$postfix = random_string("alnum",5);
			}
			
		}
	
		write2Debugfile(self::DEBUG_FILENAME, "::generate username UserName[".$id."]");
		return $id;
	}
	
	/**
	 * Check if the given $id already exists in $field of $table
	 * 
	 * @access static
	 * 
	 * @param string $id
	 * @param string $table
	 * @param string $field
	 * @return boolean
	 */
	static function issetID($id, $table, $field, $include_deleted=false)
	{
		$return = false;
		$CI =& get_instance();
		$CI->db = $CI->load->database("default", true);
		$CI->db->from($table)->where($field, $id);
		if (!$include_deleted && in_array("deleted", $CI->db->list_fields($table)))
		{
			$CI->db->where("deleted", 0);
		}
		$records = $CI->db->count_all_results();
		$return 	= ($records <= 0 ? false:true);
		return $return;
	}
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: Query Helper Methods
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * count all from $table for client
	 *
	 * @param string $table	>> tablename
	 * @param array $where	>> where-array("field_1"=>"1", "field_2"=>"lorem")
	 *
	 * @return int	
	 */
	function count($table, $where)
	{
		$this->db->from($table);

		foreach ($where as $field => $value) {
			$this->db->where($field, $value);
		}
		
		$count = $this->db->count_all_results();
	
		write2Debugfile(self::DEBUG_FILENAME, "count '$table' >> ".$this->lastQuery()." result[$count]" );
		return $count;
	}
	
	/**
	 * Determine the number of rows in a particular table.
	 * 
	 * @param string $table >> the database table
	 * @return int >> number of rows in $table
	 */
	public function countAll($table){
		return $this->db->count_all($table);
	}
	
	/**
	 * Permits you to determine the number of rows in a particular Active Record query.
	 *
	 * @return int >> number of records 
	 */
	public function countResults(){
		return $this->db->count_all_results();
	}
	
	/**
	 * Returns the last query
	 * @return string
	 */
	public function lastQuery(){
		return $this->db->last_query();
	}
	
	/**
	 * Returns an array containing the field names for the given table.
	 *
	 * @param string $table
	 * @return array
	 */
	public function listFields($table){
		return $this->db->list_fields($table);
	}
	
	/**
	 * Returns an array containing the names of all the tables in the database you are currently connected to
	
	 * @return array
	 */
	public function listTables(){
		return $this->db->list_tables();
	}
	
	/**
	 * Check if a field exists in a table
	 *
	 * @param string $table
	 * @param string $field
	 * @return bool
	 */
	public function fieldExists($table, $field){
		return $this->db->field_exists($field, $table);
	}
	
	/**
	 * Returns an array of objects containing field information.
	 * The following data is available in the result array
	 *  - name 			-> column name
	 *  - max_length 	-> maximum length of the column
	 *  - primary_key 	-> 1 if the column is a primary key
	 *  - type 			-> the type of the column
	 *
	 * @param string $table
	 * @return array
	 */
	public function fieldData($table){
		return $this->db->field_data($table);
	}
	
	/**
	 * Check if a table exists
	 *
	 * @param string $table
	 * @return bool
	 */
	public function tableExists($table){
		return $this->db->table_exists($table);
	}
	
	
	/**
	 * In MySQL "DELETE FROM TABLE" returns 0 affected rows. The database class has a small hack that allows it to return the correct number of affected rows.
	 * By default this hack is enabled but it can be turned off in the database driver file.
	 *
	 * @return >> The number of affected rows, when doing "write" type queries (insert, update, etc.).
	 */
	public function getAffectedRows(){
		return $this->db->affected_rows();
	}
	
	/**
	 * Get the database error array or null
	 * @return mixed >> null or the error-array("code"=>"", "message"=>"")
	 */
	public function getDBError(){
		
		$error = $this->db->error();
		return ($error["message"] != "" ? $error : null);
	}
	
	/**
	 * Get the last insert id
	 * 
	 * @return int >> The insert ID number when performing database inserts.
	 */
	public function getInsertID(){
		return $this->db->insert_id();
	}
	
	/**
	 * returns a correctly formatted SQL insert string
	 * Note: Values are automatically escaped, producing safer queries.
	 *
	 * @param string $table
	 * @param array $data
	 *
	 * @return string
	 */
	public function getInsertString($table, $data){
		return $this->db->insert_string($table, $data);
	}
	
	/**
	 * returns a correctly formatted SQL replace string
	 * Note: Values are automatically escaped, producing safer queries.
	 *
	 * @param string $table
	 * @param array $data
	 *
	 * @return string
	 */
	public function getReplaceString($table, $data){
		
		$protected_data = array();
		foreach ($data as $k => $v)
		{
			$protected_data[$this->db->protect_identifiers($k, FALSE, true)] = $this->db->escape($v);
		}
		$table = $this->db->protect_identifiers($table, TRUE, NULL, FALSE);
		$sql = 'REPLACE INTO '.$table.' ('.implode(', ', array_keys($protected_data)).') VALUES ('.implode(', ', array_values($protected_data)).')';
		return $sql;
	}	
	/**
	 * 
	 * @version 1.0
	 * @return string
	 *
	 */
	public function getUTCOffset(){
		//SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP);
	}
	
	
	/**
	 * returns a correctly formatted SQL update string
	 * Note: Values are automatically escaped, producing safer queries.
	 *
	 * @param string $table
	 * @param array $data
	 * @param string $where
	 *
	 * @return string
	 */
	public function getUpdateString($table, $data, $where){
		return $this->db->update_string($table, $data, $where);
	}
	
	/**
	 * Get the database platform you are running (MySQL, MS SQL, Postgres, etc...)
	 * 
	 * @return string 
	 */
	public function getPlatform(){
		return $this->db->platform();
	}

	/**
	 * Permits you to generate a CSV from a query result. The first parameter of the method must contain the result object from your query.
	 *
	 * @param object $query
	 * @param string $delimiter
	 * @param string $newline
	 * @param string $enclosure
	 * @return string >> csv
	 */
	public function getResultAsCSV($query, $delimiter=",", $newline="\r\n", $enclosure='"')
	{
		$this->load->dbutil();
		$csv = $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);
	
		return $csv;
	}
	
	/**
	 * Permits you to generate XML from a query result. The first parameter expects a query result object
	 *
	 * @param object $query
	 * @param string $root
	 * @param string $element
	 * @param string $newline
	 * @param string $tab
	 * @return string >> xml
	 */
	public function getResultAsXML($query, $root="root", $element="node", $newline="\n", $tab="\t")
	{
		$this->load->dbutil();
	
		$config = array (
			'root'		=> $root,
			'element'	=> $element,
			'newline'	=> $newline,
			'tab'		=> $tab
		);
	
		$xml = $this->dbutil->xml_from_result($query, $config);
		return $xml;
	}
	
	/**
	 * Retrieve the database version you are running
	 *  
	 * @return string
	 */
	public function getVersion(){
		return $this->db->version();
	}
	
	/**
	 * retrieve MYSQL information scheme
	 *
	 * @param string $table
	 * @return BASE_Result
	 */
	public function getColumnsFromInformationScheme($table)
	{
		$db 	= $this->db->database;
		$sql 	= "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$table'";
		$query 	= $this->db->query($sql);
		
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
		
		$num_rows 		= $query->num_rows();
		$result_array	= $query->result_array();
		
		return new BASE_Result($result_array, "", null, E_STATUS_CODE::SUCCESS );
	}
	
}

?>