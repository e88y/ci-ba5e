<?php
/**
 * Language model
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\locale_model
 * @version 1.0
 */
class Locale_model extends BASE_Model 
{
	const DEBUG_FILENAME = "locale_model.log";
	
	/**
	 * Constructor for the locale model
	 */
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "Locale model", false);
	}
	
	/**
	 * Load data for a given locale_id
	 *
	 * @version 1.0
	 *
	 * @param string $locale_code 	>> DE, EN etc.
	 * @param string $locale_id		>> pass a locale_id to get only this specific entry
	 *
	 * @return BASE_Result
	 */
	function load($locale_code, $locale_id=null)
	{
		$fields = "*";
		$where 	= array(
			"locale_code"=>$locale_code
		);
	
		if ($locale_id != null){
			$where["locale_id"] = $locale_id;
		}
	
		$order_by = array("group_token"=>"desc", "locale_code"=>"desc", "locale_id"=>"desc");
	
		$return = $this->BASE_Select(TBL_LOCALES_L18N, $where, $fields, $order_by);
	
		write2Debugfile(self::DEBUG_FILENAME, " - load locale[$locale_code] locale_id[$locale_id]\n".$this->lastQuery()."\n".print_r($return, true) );
		return $return;
	}
	
	/**
	 * Load available languages from database
	 * 
	 * @version 1.0
	 *  
	 * @param string $client_id		>> client
	 * @param string $locale_code 	>> load a specific loacale entry
	 * @return BASE_Result
	 */
	function load_languages($client_id, $locale_code=null)
	{
		$where = array("enabled"=>1);
		if ($locale_code !== null){
			$where["locale_code"] = $locale_code;
		}
		
		$languages = $this->BASE_Select(TBL_LOCALES, $where, array("locale_code", "locale_name", "folder", "CONCAT(locale_code, ' (', locale_name, ')' ) AS locale_label"), array("locale_code"=>"desc"), "", "", true ); 

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// set locale code as array key
		$data = array();
		if ($locale_code == null)
		{
			foreach ($languages->data as $value) {
				$data[$value->locale_code] = $value;
			}
		}
		else{
			if (is_object($languages->data[0])){
				$data[$locale_code] = $languages->data[0];
			}
		}
		$languages->data = $data;
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		write2Debugfile("load_languages.log", "\n".print_r($languages, true));
		return $languages;
	}
	
	/**
	 * create new locale entry. 
	 * this will enter an entry for all available languages
	 * 
	 * @param string $locale_code
	 * @param string $locale_id
	 * @param array $data
	 * @return BASE_Result
	 */
	function create($locale_code, $locale_id, $data)
	{
		$all_locale_codes = self::load_languages(0, null);
		write2Debugfile(self::DEBUG_FILENAME, "locale_code[".$locale_code."] locale_id[$locale_id] create locale-".print_r($all_locale_codes, true));
		
		$queries = array();
		foreach ($all_locale_codes->data as $current_locale_code => $value) {
			
			$data["is_translated"] = 0;
			if ($locale_code == $current_locale_code){
				$data["is_translated"] = 1;
			}
			$data["locale_code"] = $current_locale_code;
			
			
			$queries[]= $this->getInsertString(TBL_LOCALES_L18N, $data);
		}
		
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}
			
		$return = $this->BASE_Transaction($queries);
		
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".$queries_string."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * load all locales for a given client id and locale_code, utilizing the datatables library
	 *
	 * @param string $client_id
	 * @param string $locale_code
	 * @param string $columns
	 * @param int $btnEdit
	 * @param int $btnDel
	 * @param int $includeDeleted
	 * @return BASE_Result >> containing an array or null
	 */
	function datatable($client_id, $locale_code, $columns, $btnEdit=0, $btnDel=0, $includeDeleted=0)
	{
		$this->load->library('Datatables');
		$this->load->helper('datatable');
	
		$table_columns_a = $this->listFields(TBL_LOCALES);
		$table_columns_b = $this->listFields(TBL_LOCALES_L18N);
	
		write2Debugfile(self::DEBUG_FILENAME, "datatable clientID [$client_id] edit[$btnEdit] del[$btnDel] includeDeleted[$includeDeleted] columns-".print_r($columns, true)."\ndatabase columns-".print_r($table_columns_b, true), true);
		
		$fields 		= prepare_fields($columns, array_merge($table_columns_a, $table_columns_b));
		
		write2Debugfile(self::DEBUG_FILENAME, "select fields -> ".print_r($fields, true));
	
		$this->datatables->select($fields)->from(TBL_LOCALES_L18N);
		$this->datatables->join( 
			TBL_LOCALES, 
			TBL_LOCALES.".locale_code = ".TBL_LOCALES_L18N.".locale_code AND ".
			TBL_LOCALES.".enabled = '1'  ", "inner");
		
		$this->datatables->where(TBL_LOCALES.".locale_code", $locale_code);
		$this->datatables->edit_column('is_translated', '$1', 'callback_integer2checkbox(locale_id, is_translated) ');
		$this->datatables->edit_column('locale_id', '$1', "callback_build_locale_buttons(locale_code,locale_id,locales,$btnEdit,$btnDel)");
		
		
		$this->datatables->edit_column('locale_code', '<code>$1</code>', 'locale_code');
		
		$result = $this->datatables->generate();
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".$this->datatables->last_query()."\n\n".print_r(json_decode($result), true));
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * delete (or set deleted flag) a localization entry
	 *
	 * @version 1.2
	 * 
	 * @param string $locale_code 	>> DE, EN, etc
	 * @param string $locale_id		>> locale token
	 * @param string $deleted_by	>> username 
	 * 
	 * @return BASE_Result
	 */
	function remove($locale_code, $locale_id, $deleted_by)
	{
		$return = $this->BASE_Delete(TBL_LOCALES_L18N, array("locale_code"=>$locale_code, "locale_id"=>$locale_id));
	
		write2Debugfile(self::DEBUG_FILENAME, "\ndelete localization\n".print_r($return, true));
		return $return;
	}

	/**
	 * update an existing locale entry
	 * @version 1.0
	 *
	 * @param string $locale_code >> DE, EN etc. 
	 * @param string $locale_id
	 * @param array $data
	 * @return BASE_Result
	 */
	function update($locale_code, $locale_id, $data)
	{
		write2Debugfile(self::DEBUG_FILENAME, "- update locale[$locale_id]\ndata-".print_r($data, true));
	
		$queries = array(
			$this->getUpdateString(TBL_LOCALES_L18N, $data,  array("locale_code"=>$locale_code, "locale_id" => $locale_id))
		);
	
		$return = $this->BASE_Transaction($queries);
	
		write2Debugfile(self::DEBUG_FILENAME, "\nreturn-".print_r($return, true));
		return $return;
	}
}

/**
 * 
 * @param string $locale_code
 * @param string $locale_id
 * @param string nown $label
 * @param string $class
 * @param int $btn_edit
 * @param int $btn_delete
 * @return string
 */
function callback_build_locale_buttons($locale_code, $locale_id, $class, $btn_edit=0, $btn_delete=0)
{	
	$label 			= $locale_id;
	//$locale_id 		= encrypt_string($locale_id);
	$label			= lang($label);

	$buttons 	= "";
	if ($btn_delete == 1){
		$buttons .= '<a href="'.base_url().'root/'.$class.'/remove/'.$locale_code.'/'.$locale_id.'" onclick="$.'.$class.'.remove(\''.$locale_code.'\', \''.$locale_id.'\')" class="dtbt_remove btn btn-xs btn-danger"><i class="fa fa-trash" title="\''.$label.'\'&nbsp;'.lang("delete").'"></i></a>&nbsp;';
	}

	if ($btn_edit == 1){
		$buttons .= '<a href="'.base_url().'root/'.$class.'/edit/'.$locale_code.'/'.$locale_id.'" onclick="$.'.$class.'.edit(\''.$locale_code.'\', \''.$locale_id.'\')" class="dtbt_edit btn btn-xs btn-primary"><i class="fa fa-pencil" title="\''.$label.'\'&nbsp;'.lang("edit").'"></i></a>&nbsp;';
	}

	
	return $buttons."&nbsp;".$label;
}
