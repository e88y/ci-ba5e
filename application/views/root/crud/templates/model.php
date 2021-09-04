<?php 

	$validation_rules = '';
	foreach ($cdata as $key => $value) {
		$validation_rules .= '$this->form_validation->set_rules("'.$key.'", "lang:'.$key.'", "'.$value["validation_rules"].'");'."\n"."\t\t";
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: MAKE SURE YOU HAVE YOUR SH** TOGHETHER FROM HERE ON ::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$str = '<?php
/**
 * 
 * '.ucfirst($classname).' model
 *
 * @author Marco Eberhardt
 * @category model
 * @package '.$package.'
 * @version 1.0
 */
class '.ucfirst($classname).'_model extends BASE_Model 
{
	const DEBUG_FILENAME = "'.$classname.'_model.log";
	
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "'.$classname.'_model", false);
	}
	
	/**
	 * count all '.$classname.' for client
	 *
	 * @param string $client_id
	 * @param string $'.$classname.'_id
	 * @param bool $includeDeleted
	 *
	 * @return int
	 */
	function count_'.$classname.'($client_id, $'.$classname.'_id, $includeDeleted=false)
	{
		$this->db->from("'.$db_table.'");
		$this->db->where("'.$classname.'_id", $'.$classname.'_id);
	
		if ($includeDeleted === false){
			$this->db->where("deleted", 0);
		}
		$count = $this->db->count_all_results();
	
		write2Debugfile(self::DEBUG_FILENAME, "count '.$classname.' clientID[$client_id] count[$count]" );
		return $count;
	}
	
	/**
	 * creates a new '.$classname.' entry 
	 * @version 1.2 
	 */
	function create($client_id, $data)
	{
		write2Debugfile(self::DEBUG_FILENAME, "client_id[".$client_id."] create '.$classname.'-".print_r($data, true));
		if ($data["'.$classname.'_id"] == ""){
			$data["'.$classname.'_id"] 	= BASE_Model::generateUID("'.$db_table.'", "'.$classname.'_id", "", false, 10);
		}
	
		$data["client_id"] = $client_id;
	
		$queries = array( $this->getInsertString("'.$db_table.'", $data) );
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}
				
				
		$return = $this->BASE_Transaction($queries);
	
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".$queries_string."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * load all '.$classname.' for a given client id, utilizing the datatables library
	 *
	 * @param string $client_id		>> client identifier
	 * @param array $columns		>> array with the views column definition
	 * @param bool $btnEdit 		>> add edit button 
	 * @param bool $btnDel 			>> add delete button 
	 * @param bool $includeDeleted	>> show deleted entries
	 * 
	 * @return BASE_Result >> containing an array or null 
	 */
	function datatable($client_id, $columns, $btnEdit=false, $btnDel=false, $includeDeleted=false)
	{
		$this->load->library("Datatables");
		$this->load->helper("datatable");

	 	$fields = prepare_fields($columns, $this->listFields("'.$db_table.'"));
	
		$this->datatables->select($fields);
	 	$this->datatables->from("'.$db_table.'");
		$this->datatables->where("client_id", $client_id);
	 																						
		$this->datatables->edit_column("'.$classname.'_id", "$1" , "callback_build_buttons('.$classname.'_id,'.$classname.'_name,admin,'.$classname.',$btnEdit,$btnDel,0,1,1)");
		$this->datatables->edit_column("deleted", "$1" , "callback_deleted('.$classname.'_id, deleted) ");
		$this->datatables->edit_column("created_at", "$1" , "format_timestamp2datetime(created_at) ");
		
		if ($includeDeleted === false){
			$this->datatables->where("deleted", "0");
		}
	
		$result = $this->datatables->generate();
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".$this->datatables->last_query()."\n\n".print_r(json_decode($result), true));
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * Load data for a given '.$classname.'
	 *
	 * @version 1.2
	 * @param string $client_id 		>> client id
	 * @param string $'.$classname.'_id	>> '.$classname.' id
	 * @param bool $includeDeleted		>> load also deleted items
	 *
	 * @return BASE_Result
	 */
	function load($client_id, $'.$classname.'_id=null, $includeDeleted=true)
	{
		$fields = "*";
		$limit = null;
		$where 	= array(
			"client_id"=>$client_id
		);
	
		if ($'.$classname.'_id != null){
			$where["'.$classname.'_id"] = $'.$classname.'_id;
			$limit = 1;
		}
		if ($includeDeleted === false){
			$where["deleted"] = "0";
		}
	
		$order_by = array("'.$db_name_field.'"=>"asc");
		
		$return = $this->BASE_Select("'.$db_table.'", $where, $fields, $order_by, $limit, null, true);
	
		write2Debugfile(self::DEBUG_FILENAME, " - load client_id[$client_id] '.$classname.'_id[$'.$classname.'_id]\n".$this->lastQuery()."\n".print_r($return, true) );
		return $return;
	}
	
	/**
	 * delete (or set deleted flag) a '.$classname.' and all its related data (uses transaction)
	 *
	 * @version 1.2
	 * @param string $'.$classname.'_id
	 * @return BASE_Result
	 */
	function remove($client_id, $'.$classname.'_id, $deleted_by)
	{
		$data = array(
			"deleted" => 1,
			"deleted_by" => $deleted_by,
			"deleted_at" => time()
		);
		$return = $this->BASE_Update("'.$db_table.'", $data, array("client_id"=>$client_id, "'.$classname.'_id"=>$'.$classname.'_id));
	
		write2Debugfile(self::DEBUG_FILENAME, "\ndelete '.$classname.'\n".print_r($return, true));
		return $return;
	}

	/**
	 * update an existing '.$classname.' 
	 * uses transactions
	 * 
	 * @version 1.2
	 * 
	 * @param string $client_id
	 * @param string $'.$classname.'_id
	 * @param array $data
	 * @return BASE_Result 
	 */
	function update($client_id, $'.$classname.'_id, $data)
	{
		write2Debugfile(self::DEBUG_FILENAME, "- update client_id[".$client_id."] '.$classname.'[$'.$classname.'_id] \nsavedata-".print_r($data, true));

		$queries = array(
			$this->getUpdateString("'.$db_table.'", $data,  array("client_id" => $client_id, "'.$classname.'_id" => $'.$classname.'_id))
		); 
		

		//foreach ($additional_data as $index => $data){
			//$queries[] = $this->getInsertString("enter_table", $data);
		//}
		
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}
		
		$return = $this->BASE_Transaction($queries);
		
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".$queries_string."\nreturn-".print_r($return, true));
		return $return;
	}
} // End of Class

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Place your very custom callback functions here. You can find common callbacks in the datatable_helper 
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
					
?>';
echo $str;
?>