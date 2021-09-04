<?php
/**
 * CRUD model
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\crud_model
 * @version 1.1
 */
class crud_model extends BASE_Model 
{
	const DEBUG_FILENAME = "crud_model.log";
	
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "crud model", false);
	}
	
	/**
	 * 
	 * @link https://github.com/IgnitedDatatables/Ignited-Datatables/wiki/Function-Reference
	 */
	function datatable($client_id, $columns, $table)
	{
		$this->load->library('Datatables');
		
		$table_columns = $this->listFields("INFORMATION_SCHEMA.COLUMNS");
		
		write2Debugfile(self::DEBUG_FILENAME, "datatable clientID [$client_id] columns-".print_r($columns, true)."\n");
		$fields = array();
		if (count($columns) > 0)
		{
			foreach ($columns as $key => $value)
			{
				if (! in_array($value->data, $table_columns)){
					$fields[] = "'&nbsp;' AS ".$value->data;
					continue;
				}
				$fields[] = $value->data;
			}
			$fields = implode(", ", $fields);
		}
		
		
		
		$this->datatables->select($fields)->from("INFORMATION_SCHEMA.COLUMNS")->where("TABLE_SCHEMA", $this->db->database)->where("TABLE_NAME", $table);
		$this->datatables->edit_column('action_col', '$1', 'callback_preselect_options(COLUMN_NAME, DATA_TYPE, COLUMN_KEY, IS_NULLABLE, EXTRA, CHARACTER_MAXIMUM_LENGTH)');
		
		$result = $this->datatables->generate();
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".$this->datatables->last_query()."\n\n".print_r(json_decode($result), true));
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
}
/**
 * datatable callback function for the 'actions' 
 *
 * @author _BA5E
 * @category Model
 * @package application\models\crud_model
 * @since 1.2
 * @version 1.0
 *
 * @param int $static
 * @param string $str
 *
 * @return string
 */
function callback_preselect_options($name, $type, $key, $is_nullable, $extra, $maxLength)
{
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$usually_hidden		= array("created_at", "deleted", "deleted_by", "deleted_at");
	$int_dataTypes 		= array("timestamp", "int", "tinyint", "bigint");
	$text_dataTypes 	= array("varchar", "text");
	$list_dataTypes 	= array("set", "enum");
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$required 		= false;
	$hidden			= false;
	$enabled		= true;
	$input_type		= "TEXT";
	$numeric		= false;
	$is_list		= false;

	if (preg_match('/(?<=_)id(\b|_)/', $name)){
		$enabled = false;
	}
	
	if ($key == "PRI" || $key == "MUL" && $extra != "auto_increment"){
		$required = true;
	}
	if (in_array($name, $usually_hidden)){
		$hidden = true;
	}
	if (in_array($type, $int_dataTypes)){
		$numeric = true;
	}
	if (in_array($type, $list_dataTypes)){
		$is_list = true;
		$input_type = "SELECT";
	}
	if ($type == "text"){
		$is_list = true;
		$input_type = "TEXTAREA";
	}
	if ($maxLength == "1" && ($type == "int" || $type == "tinyint") ){
		$input_type = "RADIO";
	}
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$cb_required 	= new HTML_Toggle("required_$name","settings[$name][required]", $required, lang("required"), 1, E_SIZES::XS );
	$cb_hidden 		= new HTML_Toggle("hidden_$name", "settings[$name][hidden]", $hidden, lang("hidden"), 1, E_SIZES::XS);
	$cb_enabled 		= new HTML_Toggle("enabled_$name", "settings[$name][enabled]", $enabled, lang("enabled"), 1, E_SIZES::XS);
	$aTypes = array(
		array("key"=>"TEXT", "label"=>"TEXT"),
		array("key"=>"TEXTAREA", "label"=>"TEXTAREA"),
		array("key"=>"SELECT", "label"=>"SELECT"),
		array("key"=>"RADIO", "label"=>"RADIO"),
		array("key"=>"CHECK", "label"=>"CHECK"),
	);
	$dd_type		= new HTML_Select("type_$name", "settings[$name][type]", HTML_Select::buildOptions($aTypes, "key", "label", $input_type), lang("select"));

	$str = 
		$cb_required->generateHTML().
		$cb_hidden->generateHTML().
		$cb_enabled->generateHTML().
		$dd_type->generateHTML();
	
	return $str;
}
?>