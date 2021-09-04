<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BASE_Form_validation - Extends the Form_validation with predefined callback functions.
 * 
 * !!! Note: You dont need to add the 'callback_' prefix when setting the rules, because they are not a callback within a controller
 * 
 * @author Marco Eberhardt
 * @category Object
 * @package application\libraries\BASE_Form_validation
 * @version 1.1
 */
class BASE_Form_validation extends CI_Form_validation
{
	const DEBUG_FILENAME 		= "BASE_Form_validation.log";
	
	/**
	 * Codeigniter instance
	 * @var 
	 */
	protected  $ci;
	
	/**
	 * Constructor for BASE_Form_validation 
	 */
	function __construct($config=array())
	{
		parent::__construct($config);
		
		$this->ci =& get_instance();
	}
	
	/**
	 * checks if a value is unique. 
	 * 
	 * Usage example:
	 * @example 
	 * 	$this->form_validation->set_rules('locale_id', 'lang:locale_id', 'validate_is_unique['.$this->input->post("locale_id_orig").', model__locales_l18n, locale_id]');
	 *
	 * @param string $name		>> the new value
	 * @param string $args		>> excepts a comma seperated string to explode original value, table and field (and optionally message).
	 * 
	 * @return boolean
	 */
	function validate_is_unique($name, $args)
	{
		$exploded =  explode(',', $args);
		
		if (count($exploded) == 4)
		{
			list($name_orig, $table, $field, $msg) = $exploded;
		}
		elseif (count($exploded) == 3)
		{
			list($name_orig, $table, $field) = $exploded;
			$msg = "";
		}
		else{
			throw new Exception("arguments invalid");
		}
		
		write2Debugfile("validate_is_unique.log", "validate_is_unique name[$name] orig[$name_orig] table[$table] field[$field]\n", false);
		$useable = true;
		
		if ($name != $name_orig)
		{
			if (BASE_Model::issetID($name, $table, $field) === true)
			{
				$useable = false;
				$this->ci->form_validation->set_message('validate_is_unique', ($msg != "" ? $msg : lang("msg_entry_already_exist")) );
			}
		}
		
			
		
		write2Debugfile("validate_is_unique.log", $this->ci->db->last_query()."\nis_unique/useable [$useable]", true);
	
		return $useable;
	}
	
	/**
	 * check if a value already exist in the database and sets the validation message for rule 'validate_existance' if it DONT EXIST
	 * 
	 * @throws Exception
	 * 
	 * @param string $value	>> the value to check
	 * @param string $args	>> comma seperated arguments containing tablename, table field to check and a message (which will not be used anyway)
	 * 
	 * @return boolean
	 */
	function validate_existance($value, $args)
	{
		$exploded =  explode(',', $args);
		if (count($exploded) != 2 && count($exploded) != 3){
			throw new Exception("2 or 3 comma seperated arugments expected");
		}
		
		list($table, $field, $msg) = $exploded;
		
		write2Debugfile("validate_existance.log", "validate_existance value[$value] table[$table] field[$field]\n", true);
		
		$value_exists = BASE_Model::issetID($value, $table, $field);
		if (! $value_exists)
		{
			$this->ci->form_validation->set_message('validate_existance', lang("entry_does_not_exists"));
		}
		write2Debugfile("validate_existance.log", $this->ci->db->last_query()."\n\nvalue_exists [$value_exists]", true);
		
		return $value_exists;
	}
	
	/**
	 * callback to check permission and set the corresponding message
	 *
	 * @param string $permission
	 * @return bool >> and sets the form validation message
	 */
	function validate_permission($permission)
	{
		$hasPermission = $this->ci->checkPermission( $permission );
		if (! $hasPermission){
			$this->ci->form_validation->set_message('validate_permission', lang("msg_no_permissions")." ". lang("#".$permission) );
		}
	
		write2Debugfile("validate_permission.log", " - validate_save_permission [$hasPermission]\n");
		return $hasPermission;
	}
}

?>