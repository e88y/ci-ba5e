<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper containing common callback-methods for the datatable library
 *
 * @author Marco Eberhardt
 * @category helper
 * @package application\helpers\datatable_helper
 * @version 1.0
 */

if ( ! function_exists('callback_build_buttons'))
{
	/**
	 * Create buttons (delete & edit) for datatable callbacks
	 *
	 * @author Marco Eberhardt, Elina Nemenova
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id			>> id value
	 * @param string $name			>> name
	 * @param string $folder        >> root/admin
	 * @param string $class			>> js/php class name
	 * @param int $btn_edit			>> add edit button
	 * @param int $btn_delete		>> add delete button
	 * @param int $translate		>> if true, the name will be translated
	 * @param int $encrypt			>> if true, the id will be encrypted
	 * @param int $nobr				>> if true, the row will have <nobr> around it (set as false at looong names)
	 *
	 * @return string
	 */
	function callback_build_buttons($id, $name, $folder, $class, $btn_edit=1, $btn_delete=1, $translate=0, $encrypt=0, $nobr=1)
	{
		if ($encrypt == 1){
			$id = encrypt_string($id);
		}
		
		$buttons 	= "";
		
		if ($btn_delete == 1){
			$buttons .= '<a href="'.base_url($folder.'/'.$class.'/remove/'.$id).'" onclick="$.'.$class.'.remove(\''.$id.'\')" class="dtbt_remove btn btn-xs btn-danger"><i class="fa fa-trash" title="\''.removetags($name).'\'&nbsp;'.lang("delete").'"></i></a>&nbsp;';
		}
		
		if ($btn_edit == 1){
			$buttons .= '<a href="'.base_url($folder.'/'.$class.'/edit/'.$id).'" onclick="$.'.$class.'.edit(\''.$id.'\')" class="dtbt_edit btn btn-xs btn-primary"><i class="fa fa-pencil" title="\''.removetags($name).'\'&nbsp;'.lang("edit").'"></i></a>&nbsp;';
		}
		
		if ($translate == 1)
		{
			$name=lang($name);
		}
		
		if ($nobr == 1){
			$return = "<nobr>".$buttons."&nbsp;".removetags($name)."</nobr>";
		}else{
			$return = $buttons."&nbsp;".removetags($name);
		}
		
		// $return .= "[".intval($nobr)."] - [".boolval($nobr)."] - [".$nobr."]";
		
		return $return;
	}
}

if ( ! function_exists('callback_build_general_buttons'))
{
	/**
	 * Create buttons (delete & edit) for datatable callbacks
	 *
	 * @author ???
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id
	 * @param string $buttons        >> JSON_encoded indexed array: {string $name,string, string $tooltip, string $url, string $jscall, string $classAdd, string $iconClass, boolean $encrypt},{...
	 * @return string
	 */
	function callback_build_general_buttons($id, $moreData, $buttons)
	{
		$buttons = json_decode(base64_decode($buttons),true);
		$return 	= "";
		foreach($buttons as $button)
		{
			if ($button["encrypt"] == true){
				$id = encrypt_string($id);
			}
			if(!array_key_exists("exclude",$button))
			{
				$button["exclude"] = array();
			}
			if(!in_array($id,$button["exclude"]))
			{
				if($moreData != "")
				{
					if ($button["encrypt"] == true){
						$moreData = encrypt_string($moreData);
					}
				}
				$params = "('".$id."'";
				if(trim($moreData) != "")
				{
					$params .= ",'".$moreData."'";
				}
				$params .= ")";
				$return .= '<a href="'.$button["url"].'" onclick="'.$button["jscall"].$params.'" class="btn btn-xs '.$button["classAdd"].'"><i class="'.$button['iconClass'].'" title="'.$button["tooltip"].'"></i></a>&nbsp;';
			}
		}
		if($return != "")
		{
			$return = $return.($button["name"]!=""?"&nbsp;":"").$button["name"];
		}
		return $return;
	}
}

if ( ! function_exists('callback_currency'))
{
	/**
	 * show value as currency value
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $value
	 * @return string
	 */
	function callback_currency($value)
	{
		return format_number($value, "", " ".lang("currency_symbol_eur"));
	}
}

if ( ! function_exists('callback_deleted'))
{
	/**
	 * Datatable callback function for the 'locked' column (@see Users for example)
	 * Expects a string containing deleted, deleted_at and deleted_by informations concatenated by hashtag (#)
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helper\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id
	 * @param string $deleted_args >> #-concatenated string containing deleted, deleted_at and deleted_by (@see ::prepare_fields)
	 *
	 * @return string
	 */
	function callback_deleted($id, $deleted_args)
	{
		$com = "callback parameter has an unsupported format | id[$id] args[$deleted_args]";
		if (substr_count($deleted_args, "#") == 2)
		{
			list($deleted, $deleted_at, $deleted_by) = explode("#", $deleted_args);
			$com = ($deleted != 1 ?  E_ICONS::SQUARE_WHITE : E_ICONS::CHECK_SQUARE_WHITE." ".format_timestamp2datetime($deleted_at, '')." (".$deleted_by.")" );
		}
		
		return $com;
	}
}

if ( ! function_exists('callback_integer2checkbox'))
{
	/**
	 * datatable callback function to represent int value as a checkbox.
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id	>> identifier
	 * @param int $checked	>> 0=unchecked | 1=checked
	 *
	 * @return string
	 */
	function callback_integer2checkbox($id, $checked)
	{
		$com = ($checked != "1" ?  E_ICONS::SQUARE_WHITE."&nbsp;" : E_ICONS::CHECK_SQUARE_WHITE."&nbsp;" );
		return $com;
	}
}

if ( ! function_exists('callback_integer2checkbox_editable'))
{
	/**
	 * Datatable callback function to create editable checkboxes.
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id	>> identifier
	 * @param string $name	>>
	 * @param string $label	>>
	 * @param int $checked	>> 0=unchecked | 1=checked
	 * @param bool $enabled
	 * @return string
	 */
	function callback_integer2checkbox_editable($id, $name, $label, $checked, $enabled=true)
	{
		$checked = ($checked == 1 ? true:false);
		$cb = new HTML_Checkbox($name."_".$id, $name."[]", $label, $checked, $id, $enabled);
		return $cb->generateHTML();
	}
}


if ( ! function_exists('callback_link_button'))
{
	/**
	 * Create link-button for datatable callbacks
	 *
	 * @author Elina Nemenova
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $function
	 * @param string $btn_class
	 * @param string $link_class
	 * @param bool $show
	 * @param bool $translate		>> if true, the name will be translated
	 * @param bool $encrypt			>> if true, the id will be encrypted
	 * @return string
	 */
	function callback_link_button($id, $name, $class, $function, $btn_class, $title='', $link_class='', $show=true, $translate=false, $encrypt=true)
	{
		$button 	= "";
		if ((bool)$show == true)
		{
			if ($translate)
			{
				$name=lang($name);
				if ($title != '')
				{
					$title = lang($title);
				}
			}
			if ($link_class == '')
			{
				$link_class = 'dtbt_edit btn btn-xs btn-primary';
			}
			
			
			$link_class = 'class="'.$link_class.'"';
			$btn_class = 'class="'.$btn_class.'"';
			
			if ($encrypt == true){
				$id = encrypt_string($id);
			}
			$button = '<a href="'.base_url().'admin/'.$class.'/'.$function.'/'.$id.'" onclick="$.'.$class.'.'.$function.'(\''.$id.'\')" '.$link_class.'><i '.$btn_class.' title="\''.$name.'\'&nbsp;'.$title.'"></i></a>&nbsp;';
			
			$button = "<nobr>".$button."&nbsp;".$name."</nobr>";
		}
		
		return $button;
	}
}

if ( ! function_exists('callback_locked'))
{
	/**
	 * Datatable callback function for the 'locked' column (@see Users for example)
	 * Expects a string containing locked, locked_at and locked_by informations concatenated by hashtag (#)
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helper\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id
	 * @param string $locked_args >> #-concatenated string containing locked, locked_at and locked_by (@see ::prepare_fields)
	 *
	 * @return string
	 */
	function callback_locked($id, $locked_args)
	{
		$com = "callback parameter has an unsupported format | id[$id] args[$locked_args]";
		if (substr_count($locked_args, "#") == 2)
		{
			list($locked, $locked_at, $locked_by) = explode("#", $locked_args);
			$com = ($locked != 1 ?  E_ICONS::SQUARE_WHITE : E_ICONS::CHECK_SQUARE_WHITE." ".format_timestamp2datetime($locked_at, '') );
		}
		
		return $com;
	}
}

if ( ! function_exists('callback_build_radio_buttons'))
{
	/**
	 * Create radio buttons
	 *
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param string $id		>> id value
	 * @param string $name		>> name
	 * @return string
	 */
	
	function callback_build_radio_buttons($id, $name="radio", $class="dt_radio", $text="", $checked=false){
		$com = new HTML_Radio($id, $name, $text, $checked);
		return '<div class="text-center">'.$com->generateHTML()."</div>";
	}
}

if ( ! function_exists('callback_translate_if'))
{
	/**
	 * Datatable callback function to translate items recived from database.
	 *
	 * @category helper
	 * @package application\helper\datatable_helper
	 * @version 1.0
	 *
	 * @param int $translate 	>> 0=dont translate / 1=translate
	 * @param string $str 		>> the string
	 *
	 * @return string
	 */
	function callback_translate_if($translate, $str)
	{
		if ($translate == 1){
			return lang($str);
		}
		return $str;
	}
}

if ( ! function_exists('prepare_fields'))
{
	/**
	 * Create the fields string for the select portion.
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param array $columns			>> array with desired DT_Columns
	 * @param array $table_columns		>> array with the available columns from the database table (use $this->listFields(TBL_NAME) to get them)
	 * @param array $additional_fields
	 *
	 * @return string
	 */
	function prepare_fields($columns, $table_columns, $additional_fields = array())
	{
		$return 		= "";
		
		$fields 		= array();
		if (count($columns) > 0)
		{
			foreach ($columns as $value)
			{
				$table = $value->table.($value->table == "" ? "":".");
				
				if (! in_array($value->data, $table_columns)){
					$fields[] = "'' AS ".$value->data;
					continue;
				}
				else if ($value->data == "deleted")
				{
					$fields[] = "IF (deleted = 1, CONCAT_WS ('#', deleted, deleted_at, deleted_by), '0#0#0') AS deleted";
					continue;
				}
				else if ($value->data == "locked")
				{
					$fields[] = "IF (locked = 1, CONCAT_WS ('#', locked, locked_at, locked_by), '0#0#0') AS locked";
					continue;
				}
				
				$fields[] = $table.$value->data;
			}
		}
		if (count($additional_fields) > 0)
		{
			foreach ($additional_fields as $field)
			{
				$fields[] = $field;
			}
		}
		$return = implode(", ", $fields);
		
		return $return;
	}
}

if (!function_exists('prepare_group_by'))
{
	/**
	 * Create the fields string for the group_by portion.
	 * Importent: only combined with same $columns in function prepare_fields!
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\datatable_helper
	 * @version 1.0
	 *
	 * @param array $columns			>> array with desired HTML_DTColumns
	 * @param array $table_columns		>> array with the available columns from the database table (use $this->listFields(TBL_NAME) to get them)
	 * @param array $additional_fields
	 *
	 * @return string
	 */
	function prepare_group_by($columns, $table_columns, $additional_fields = array())
	{
		$return 		= "";
		
		$fields 		= array();
		if (count($columns) > 0)
		{
			foreach ($columns as $value)
			{
				$table = $value->table.($value->table == "" ? "":".");
				if (! in_array($value->data, $table_columns)){
					continue;
				}
				else if ($value->data == "deleted")
				{
					$fields[] = "IF (deleted = 1, CONCAT_WS ('#', deleted, deleted_at, deleted_by), '0#0#0') AS deleted";
					continue;
				}
				else if ($value->data == "locked")
				{
					$fields[] = "IF (locked = 1, CONCAT_WS ('#', locked, locked_at, locked_by), '0#0#0') AS locked";
					continue;
				}
				
				$fields[] = $table.$value->data;
			}
		}
		if (count($additional_fields) > 0)
		{
			foreach ($additional_fields as $field)
			{
				$fields[] = $field;
			}
		}
		$return = implode(", ", $fields);
		
		return $return;
	}
}
?>