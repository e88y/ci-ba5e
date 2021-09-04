<?php

/**
 * Common application model
 * This model is always autoloaded (@see config/autoload.php).
 * 
 * This give use the ability to call BASE_Model function directly from our controllers ($this->app_model->BASE_Select ... ) if we want to do this.
 * #Note that the following results are cached and you may need to delete the cache files after making changes on theese tables
 *  -> load_nav
 *  -> load_news
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\app_model
 * @version 1.0
 */
class App_model extends BASE_Model 
{
	const DEBUG_FILENAME = "app_model.log";
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: MODEL SHORTCUT FUNCTIONS :::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..	
	/**
	 * count all from table for client
	 *
	 * @param string $client_id
	 * @param string $table
	 * @param string $where
	 * @param bool $includeDeleted
	 *
	 * @return int
	 */
	function do_count($table, $where)
	{
		$this->db->from($table);
		
		foreach ($where as $field => $value) {
			$this->db->where($field, $value);
		}
		$count = $this->db->count_all_results();
	
		write2Debugfile(self::DEBUG_FILENAME, "count '$table' >> result[$count]" );
		return $count;
	}
	
	/**
	 * Shortcut function to perform a BASE_Update (which is the corresponding shortcut function for models)
	 * which is available for all controllers.
	 *
	 * @param string $tablename
	 * @param array $data
	 * @param array $where
	 *
	 * @return BASE_Result
	 */
	function do_update($tablename, $data, $where)
	{
		return $this->BASE_Update($tablename, $data, $where);
	}
	
	/**
	 * Shortcut function to perform a BASE_Insert (which is the corresponding shortcut function for models)
	 * which is available for all controllers.
	 *
	 * @param string $tablename
	 * @param array $data
	 *
	 * @return BASE_Result
	 */
	function do_insert($tablename, $data)
	{
		return $this->BASE_Insert($tablename, $data);
	}
	
	/**
	 * Shortcut function to perform a BASE_Select 
	 *
	 * @param string $tablename
	 * @param array $fields
	 * @param array $where
	 *
	 * @return BASE_Result
	 */
	function do_select($tablename, $fields, $where)
	{
		return $this->BASE_Select($tablename, $where, $fields);
	}
	
	/**
	 * Shortcut function to perform a BASE_Delete (which is actually a shortcut function for models)
	 * which is available for all controllers.
	 * 
	 * @param string $tablename
	 * @param array $where
	 * @param bool $useOrWhere
	 * 
	 * @return BASE_Result
	 */
	function do_delete($tablename, $where, $useOrWhere=false)
	{
		return $this->BASE_Delete($tablename, $where);
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: COMMON FUNCTIONS :::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * log controller access into the database (automaticly called by the logging_hook)
	 *
	 * @return BASE_Result
	 */
	function log_controller_access()
	{
		// session_id()
		$input_data = array(
			"session_id" 	=> ( $this->session->userdata(E_SESSION_ITEM::SESSION_ID) != NULL ? $this->session->userdata(E_SESSION_ITEM::SESSION_ID) : "expired-".time()  ),
			"user_id"		=> ( $this->session->userdata(E_SESSION_ITEM::USER_ID) != NULL ? $this->session->userdata(E_SESSION_ITEM::USER_ID) : "expired-".time()  ),
			"request_uri" 	=> $this->input->server('REQUEST_URI'),
			"timestamp" 	=> time(),
			"ip"			=> $this->input->server('REMOTE_ADDR'),
			"user_agent" 	=> $this->agent->agent_string(),
			"referer_page" 	=> $this->agent->referrer()
		);
	
		return $this->BASE_Insert(TBL_LOG_ACCESS, $input_data);
	}
	
	/**
	 * log database queries access into the database (automaticly called by the hook)
	 *
	 * @return BASE_Result
	 */
	function log_database($query)
	{
		// session_id()
		$input_data = array(
			"log_time" 		=> time(),
			"db_table"		=> "",
			"action" 		=> "",
			"username" 		=> ( $this->session->userdata(E_SESSION_ITEM::USER_ID) != NULL ? $this->session->userdata(E_SESSION_ITEM::USER_ID) : "expired-".time()  ),
			"query"			=> $query
		);
	
		return $this->BASE_Insert(TBL_LOG_DATABASE, $input_data);
	}
	
	/**
	 * store informations about file uploads
	 * 
	 * @param string $client_id
	 * @param string $user_id
	 * @param string $filename 
	 * @param string $filename_orig 
	 * @param string $hash
	 * @param string $use_for
	 * 
	 * @return BASE_Result
	 *
	 */
	function log_fileupload($client_id, $user_id, $filename, $filename_orig, $hash, $use_for=NULL)
	{
		$data = array(
			"client_id"=>$client_id,
			"user_id" => $user_id,
			"upload_time"=>time(),
			"ip"=>$this->input->server('REMOTE_ADDR'),
			"filename"=>$filename,
			"filename_orig"=>$filename_orig,
			"file_hash"=>$hash,
			"use_for"=>$use_for
		);
	
		return $this->BASE_Insert(TBL_LOG_UPLOADS, $data);
	}
	
	/**
	 * log controller acces to the database
	 */
	function log_access(){
		
	}
	
	/**
	 * Import csv into database
	 * 
	 * @param string $csv_string
	 * @param string $table
	 */
	function import_csv($csv_string, $table)
	{
		$allowed_for_import = array(TBL_LOCALES_TEMP);
		
		if ($csv_string != "" && in_array($table, $allowed_for_import))
		{
			//INFILE
			/*
			
			$query = $this->db->query("LOAD DATA INSTRING '".$csv_string."'
                    INTO TABLE $table
                    FIELDS TERMINATED BY ';'
                    ESCAPED BY '\"'
                    LINES TERMINATED BY '\r\n'
                    IGNORE 1 LINES
                    ");
          	*/
		}
		
	}
	
	
	/**
	 * loads default (top) navigation items from the table <code>TBL_APP_NAV</code>
	 * 
	 * @return BASE_Result 
	 */
	function load_nav()
	{
		return self::getNav(); 
	}
	
	/**
	 * loads custom config items, depending on the client-id
	 * 
	 * @param string $sub >> subdomain 
	 * @return BASE_Result
	 */
	function getConfig($client_id=0)
	{
		//$this->db->cache_on();
		$return = $this->BASE_Select( TBL_APP_SETTINGS, array("id"=>0), "*", array(), 1);
		//$this->db->cache_off();
		
		return $return;
	}
	
	/**
	 * loads default (top) navigation items from the table <code>TBL_APP_NAV</code>
	 *
	 * @return BASE_Result
	 */
	function getNav()
	{
		$this->db->cache_on();
		$return = $this->BASE_Select( TBL_APP_NAV , array("visible"=>1));
		$this->db->cache_off();
	
		return $return;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: GEO DATA :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * load an array with the worlds continents
	 * 	(cache result)
	 *
	 * @param string $locale			>> load only the specified localization
	 * @param string $continent_code 	>> load data for a specific continent
	 * 
	 * @return BASE_Result
	 */
	function getContinents($locale=null, $continent_code=null)
	{
		$andLocale = "";
		if ($locale){
			$andLocale = " AND ".TBL_CONTINENS_L18N.".locale_code = '".$locale."' ";
		}
	
		$this->db->cache_on();
		
		$this->db
		->select('*')->from( TBL_CONTINENS)
		->join( TBL_CONTINENS_L18N, TBL_CONTINENS.".continent_code = ".TBL_CONTINENS_L18N.".continent_code $andLocale ", "inner");
	
		if ($continent_code){
			$this->db->where("continent_code", $continent_code);
		}
	
		$query = $this->db->get();
		
		$this->db->cache_off();
	
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
	
		$data_obj	= $query->result_object();
		$data		= array();
		foreach ($data_obj as $key => $value) {
			$cc = $value->continent_code;
	
			$data[$cc]["area"] 					= $value->area;
			$data[$cc]["population"] 			= $value->population;
			$data[$cc]["earth_surface"]			= $value->earth_surface;
			$data[$cc]["population_percentage"]	= $value->population_percentage;
			$data[$cc]["population_per_km2"]	= $value->population_per_km2;
			if ($locale){
				$data[$cc]["continent_name"]	= $value->continent_name;
			}
			else{
				$data[$cc]["text"][$value->locale_code] = $value->country_name;
			}
		}
			
		$return = new BASE_Result($data, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
	
		write2Debugfile(self::DEBUG_FILENAME, "getCountries locale[$locale] continent[$continent_code]:\n".$this->lastQuery()."\n".print_r($return, true));
	
		return $return;
	}
	
	/**
	 * load an array with the worlds countries
	 * !! cached result !!
	 *
	 * @param string $locale		>> load only the specified localization
	 * @param string $country_code 	>> load data for a specific country
	 * 
	 * @return BASE_Result
	 */
	function getCountries($locale=null, $country_code=null)
	{
		$andLocale = "";
		if ($locale){
			$andLocale = " AND ".TBL_COUNTRIES_L18N.".locale_code = '".$locale."' ";
		}
		$this->db->cache_on();
		
		$this->db
		->select("*, CONCAT(".TBL_COUNTRIES.".country_code, ' (', ".TBL_COUNTRIES_L18N.".country_name, ')' ) AS country_label")->from( TBL_COUNTRIES )
		->join( TBL_COUNTRIES_L18N, TBL_COUNTRIES .".country_code = ".TBL_COUNTRIES_L18N.".country_code $andLocale ", "inner");
	
		if ($country_code){
			$this->db->where("country_code", $country_code);
		}
	
		$query = $this->db->get();
	
		$this->db->cache_off();
		
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
	
		$data_obj	= $query->result_object();
		$data		= array();
		foreach ($data_obj as $key => $value)
		{
			$cc = $value->country_code;
				
			$data[$cc]["iso_2"] 			= $value->country_code;
			$data[$cc]["iso_3"] 			= $value->iso_3;
			$data[$cc]["population"] 		= $value->population;
			$data[$cc]["area"] 				= $value->area;
			$data[$cc]["gdp"] 				= $value->gdp;
			$data[$cc]["phone_code"]		= $value->phone_code;
			$data[$cc]["country_label"]		= $value->country_label;
			$data[$cc]["european_union"]	= $value->european_union;

			if ($locale){
				$data[$cc]["country_name"]	= $value->country_name;
			}
			else{
				$data[$cc]["text"][$value->locale_code] = $value->country_name;
			}
		}
			
		$return = new BASE_Result($data, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
	
		write2Debugfile(self::DEBUG_FILENAME, "getCountries locale[$locale] country[$country_code]:\n".$this->lastQuery()."\n".print_r($return, true));
		return $return;
	}
}

