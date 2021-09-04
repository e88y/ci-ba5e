<?php 
/**
 * Client Value Object
 *
 * @author Marco Eberhardt
 * @category value object
 * @package application\libraries\value_objects\T_Client
 * @version 1.0
 */
class T_Client extends T_Pseudo
{
	const DEBUG_FILENAME 	= "T_Client.log";
	
	public $client_id 		= "";
	public $customer_number	= "";
	public $client_name 	= "";
	public $client_desc 	= "";
	public $client_email	= "";
	public $client_phone	= "";
	public $client_fax		= "";
	public $client_street	= "";
	public $client_house_nr	= "";
	public $client_zipcode	= "";
	public $client_location = "";
	public $client_logo 	= "";
	public $client_theme 	= "";
	
	public $deleted		= 0;
	public $deleted_by	= NULL;
	public $deleted_at	= NULL;

	function __construct($data=array())
	{
		if (isset($data->last_login)){
			$data->last_login = DateTime::createFromFormat('U', $data->last_login);		// U for unix timestamp
		}
		
		parent::__construct($data);
		
		write2Debugfile(self::DEBUG_FILENAME, "T_Client src-".print_r($data, true)."\nresult-".print_r($this, true), false);
	}
	
	/**
	 * get table columns array containing HTML_DTColumn  
	 * @return array
	 */
	static function get_table_columns()
	{
		return array(
			new HTML_DTColumn("control_col", "&nbsp;", E_SORTABLE::NO, E_VISIBLE::YES, E_SEARCHABLE::NO, null, array(), array("control"), array()),
			new HTML_DTColumn("client_id", lang("id"), E_SORTABLE::NO, E_VISIBLE::NO),
			new HTML_DTColumn("client_name", lang("client_name")),
			new HTML_DTColumn("client_desc", lang("desc")),
			new HTML_DTColumn("customer_number", lang("customer_number")),
			new HTML_DTColumn("client_street", lang("street")),
			new HTML_DTColumn("client_zipcode", lang("zipcode")),
			new HTML_DTColumn("client_location", lang("location")),
			new HTML_DTColumn("created_at", lang("created_at")),
			new HTML_DTColumn("deleted", E_ICONS::TRASH_WHITE)
		);
	}
}
	
?>