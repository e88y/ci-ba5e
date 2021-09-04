<?php 
/**
 * Locale value object
 *
 * @author Marco Eberhardt
 * @category value object
 * @package application\libraries\value_objects\T_Locale
 * @version 1.0
 */
class T_Locale extends T_Pseudo
{
	const DEBUG_FILENAME 	= "T_Locale.log";
	
	public $locale_code 	= "DE";
	public $locale_id		= "";
	public $locale_token	= "";
	public $text			= "";
	public $group_token		= "";
	public $created_at		= NULL;

	function __construct($data=array())
	{
		parent::__construct($data);
		
		write2Debugfile(self::DEBUG_FILENAME, "T_Locale src-".print_r($data, true)."\nresult-".print_r($this, true), false);
	}
	
	/**
	 * get table columns array containing HTML_DTColumn
	 * @return array
	 */
	static function get_table_columns()
	{
		return array(
			new HTML_DTColumn("control_col", "&nbsp;", E_SORTABLE::NO, E_VISIBLE::YES, E_SEARCHABLE::NO, null, array(), array("control"), array()),
			new HTML_DTColumn(TBL_LOCALES_L18N.".locale_code", lang("locale_code"), E_SORTABLE::NO, E_VISIBLE::NO),
			new HTML_DTColumn("locale_id", lang("locale_id"), E_SORTABLE::YES),
			new HTML_DTColumn("text", lang("text"), E_SORTABLE::YES),
			new HTML_DTColumn("group_token", lang("group"), E_SORTABLE::YES)
		);
	}
}
	
?>