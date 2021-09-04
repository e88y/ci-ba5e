<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Superuser CRUD (Create.Read.Update.Delete) controller
 * This controller creates models, views, and controllers from database
 *
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\root\crud
 * @version 1.0
 */
class Crud extends BASE_Controller 
{
	const DEBUG_FILENAME = "root_crud.log";
	
	/**
	 * table columns
	 * @var array
	 */
	private $table_columns		= array();
	
	/**
	 * 
	 * @var array
	 */
	protected $tables 	= array();
	
	/**
	 * 
	 * @var string
	 */
	protected $table 	= "";
	
	/**
	 * table fields
	 * @var array
	 */
	protected $fields	= array();
	
	/**
	 * Constructor for the crud controller
	 */
	function __construct()
	{
		parent::__construct(true, true);
		
		$this->load->model("crud_model");
		
    	$this->pageHeading 		= buildPageHeading(lang("crud"), lang("builder"));
    	//$this->javascript		= array("crud.js");
    	
    	$this->addPlugins(
    		E_PLUGIN::DATATABLES, 
    		E_PLUGIN::SELECT2,
    		E_PLUGIN::BS_TOGGLE
    	);
    	
    	$this->table_columns = array(
			new HTML_DTColumn("control_col", "&nbsp;", E_SORTABLE::NO),
    		new HTML_DTColumn("action_col", lang("action"), E_SORTABLE::NO),
    		new HTML_DTColumn("COLUMN_NAME", lang("column_name"), E_SORTABLE::YES),
    		new HTML_DTColumn("DATA_TYPE", lang("data_type"), E_SORTABLE::YES),
    		new HTML_DTColumn("DATA_TYPE", lang("data_type"), E_SORTABLE::YES),
    		new HTML_DTColumn("IS_NULLABLE", lang("null"), E_SORTABLE::YES),
    		new HTML_DTColumn("EXTRA", lang("extra"), E_SORTABLE::YES),
			new HTML_DTColumn("CHARACTER_MAXIMUM_LENGTH", lang("max_length"), E_SORTABLE::YES),
			new HTML_DTColumn("COLUMN_KEY", lang("column_key"), E_SORTABLE::NO),
			new HTML_DTColumn("COLUMN_COMMENT", lang("comment"), E_SORTABLE::YES)
    	);
    	
    	$this->tables = $this->app_model->listTables();
    	$columns = array();
    	foreach ($this->tables as $table)
    	{
    		$columns[$table] = $this->app_model->getColumnsFromInformationScheme($table)->data;
    		
    		//$columns[$table] = $this->crud_model->datatable($this->client_id, $this->table_columns, $table);
    	}
    	$this->fields = $columns;
    	
    	
		write2Debugfile(self::DEBUG_FILENAME, "root/crud\n\ntables-".print_r($this->tables, true)."\n\nfields-".print_r($this->fields, true), false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index() {
		self::show();
	}
	
	/**
	 * Ajax data-source for the datatable
	 * JSON-Rendermode
	 *
	 * @version 1.2
	 */
	public function datatable($table)
	{
		$result = $this->crud_model->datatable($this->client_id, $this->table_columns, $table);
		$result->data = json_decode($result->data);	// because the render method will encode it again
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}
	
	/**
	 * Get column definitions for given database table
	 *  
	 * @param string $table >> table to analyize ( $_POST["table"] can overwrite this)
	 * @param E_RENDERMODE $rendermode
	 * @return bool
	 */
	private function analyze($table=null, $rendermode="FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		$table = $this->input->post("table");
		
		write2Debugfile(self::DEBUG_FILENAME, " - analyze table[$table]");
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set validation rules
		$this->form_validation->set_rules(E_PERMISSIONS::ROOT_CRUD_BUILDER, 'lang:msg_no_permission', 'trim|validate_permission[]');
		$this->form_validation->set_rules("table",'lang:table', 'trim|required');
		
		if ($this->form_validation->run() )
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
			
			// load table data immediatly since the ajax way will not work without js
			$modelResult 	= $this->crud_model->datatable($this->client_id, $this->table_columns, $this->input->post("table"));
			$data 			= json_decode($modelResult->getData())->data;
			
			$this->setViewData("selected_table_columns", $data );
			$analized = true;
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n".validation_errors(), true);
			
			$this->setViewError(validation_errors());
			$this->setViewExtra($this->form_validation->error_array() );
			$this->setViewData("selected_table_columns", self::tabledata($table) );
			$analized = false;
		}
		$this->setViewData("analyzed", $analized);
		
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
			
		return $analized;
	}
	
	
	/**
	 * render the crud overview
	 *
	 * @version 1.0
	 * @param E_RENDERMODE $rendermode
	 *
	 */
	public function show($rendermode="FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (is_array($this->input->post()) && $this->input->post("generate") == 1 ){
			self::generate();
		}
		elseif (is_array($this->input->post()) && $this->input->post("analize") == 1 ){
			self::analyze(); 
		}	
		else{
			$this->setViewData("selected_table_columns", array() );					// table data with db-column definitions
			
		}
		
		$this->setViewData("selected_table", $this->input->post("table"));			// selected table
		$this->setViewData("selected_classname", $this->input->post("classname"));	// entered classname
		$this->setViewData("tables", $this->tables);								// for use in the select
		$this->setViewData("table_columns", $this->table_columns );
		
		
		write2Debugfile(self::DEBUG_FILENAME, " - render view\n".print_r($this->data, true), true);
		$this->render('root/crud/overview', $rendermode);
	}
	
	/**
	 * generate files
	 */
	private function generate()
	{
		write2Debugfile("crud_generate.log", " - generate files\n".print_r($this->input->post(), true), false);
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set validation rules
		$this->form_validation->set_rules(E_PERMISSIONS::ROOT_CRUD_BUILDER, 'lang:msg_no_permission', 'trim|validate_permission');
		$this->form_validation->set_rules('table','lang:table', 'trim|required');
		$this->form_validation->set_rules('classname','lang:classname', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('target_folder','lang:target_folder', 'trim|required');
		$this->form_validation->set_rules('settings[]','lang:settings', 'required');
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run() )
		{
			write2Debugfile("crud_generate.log", "\n - form validation passed... start file generation", true);
			
			$this->load->helper('file');
			
			$valid_targets 		= array("app"=>"/", "out"=>"restricted/output/");
			$int_dataTypes 		= array("timestamp", "int", "tinyint", "bigint");
			
			$target_folder		= (!isset( $valid_targets[$this->input->post("target_folder")]) ? $valid_targets["out"] : $valid_targets[$this->input->post("target_folder")] );  
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$cls 				= strtolower($this->input->post("classname"));
			$tbl 				= $this->input->post("table");
			$settings 			= $this->input->post("settings[]"); // array( [columnname][...] )
			$nameField 			= ($this->input->post("name_field") == "" ? $cls.'_id' : $this->input->post("name_field") );

			write2Debugfile("crud_generate.log", "\ngenerate class[$cls]", true);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$permissions = array(
				"create"=> "E_PERMISSIONS::".strtoupper($cls)."_CREATE",
				"list"=> "E_PERMISSIONS::".strtoupper($cls)."_LIST",
				"update"=> "E_PERMISSIONS::".strtoupper($cls)."_EDIT",
				"delete"=> "E_PERMISSIONS::".strtoupper($cls)."_DELETE"
			);
			
			
			$sql_permission = "INSERT INTO `app__rights` (`client_id`, `right_id`, `right_name`, `right_desc`, `group_token`, `active`, `is_root_right`) 
			VALUES 
				('0', 'right_".$cls."_delete', '#right_".$cls."_delete', '#right_".$cls."_delete_desc', 'group_".$cls."', '1', '0'), 
				('0', 'right_".$cls."_list', '#right_".$cls."_list', '#right_".$cls."_list_desc', 'group_".$cls."', '1', '0'), 
				('0', 'right_".$cls."_write', '#right_".$cls."_write', '#right_".$cls."_write_desc', 'group_".$cls."', '1', '0');";
			
			
			$sql_menu = "INSERT INTO `app__menu` (`menu_id`, `menu_label`, `menu_icon`, `menu_ref`, `menu_id_parent`, `right_id`, `requires_root_user`, `sort_order`, `is_visible`) 
			VALUES 
				('menu_".$cls."', '#menu_".$cls."', 'fa fa-hashtag', 'admin/".$cls."/show/', NULL, 'right_".$cls."_list', '0', '90000', '1'), 
				('menu_".$cls."_create', '#menu_".$cls."_create', 'fa fa-plus', 'admin/".$cls."/create/', 'menu_".$cls."', 'right_".$cls."_write', '0', '0', '1'), 
				('menu_".$cls."_show', '#menu_".$cls."_show', 'fa fa-table', 'admin/".$cls."/show/', 'menu_".$cls."', 'right_".$cls."_list', '0', '0', '1'); ";
			
			$sql_menu_l18n = "
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#menu_".$cls."', '".ucfirst($cls)."', 'menu', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#menu_".$cls."', '".ucfirst($cls)."', 'menu', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#menu_".$cls."_create', '".ucfirst($cls)." anlegen', 'menu', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#menu_".$cls."_create', 'Create ".ucfirst($cls)."', 'menu', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#menu_".$cls."_show', '".ucfirst($cls)."übersicht', 'menu', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#menu_".$cls."_show', '".ucfirst($cls)." overview', 'menu', 1);
				";
			
			$sql_permission_l18n = "
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#right_".$cls."_delete', '".ucfirst($cls)." löschen', 'permission', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#right_".$cls."_delete', 'Delete ".$cls."', 'permission', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#right_".$cls."_list', '".ucfirst($cls)." anzeigen', 'permission', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#right_".$cls."_list', 'Show ".$cls."', 'permission', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('DE', '#right_".$cls."_write', '".ucfirst($cls)." anlegen und bearbeiten', 'permission', 1);
				INSERT INTO `app_locales__l18n` (locale_code, locale_id, `text`, group_token, is_translated) VALUES('EN', '#right_".$cls."_write', 'Create and edit ".$cls."', 'permission', 1);
				";
				
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// fill table columns definition and collect input types, validation rules etc.
			$table_def 			= self::tabledata($tbl);
			$table_columns 		= '$this->table_columns = array('."\n\t\t".'new HTML_DTColumn("control_col", "&nbsp;", E_SORTABLE::NO)'."";
			
			$crud = array();
			foreach ($table_def as $column_def) 
			{
				$col_settings = $settings[$column_def["COLUMN_NAME"]];
				
				//write2Debugfile("crud_generate.log", "\n - col[".$column_def["COLUMN_NAME"]."] coldef-".print_r($column_def, true)." ".$column_def["COLUMN_NAME"]."->settings-".print_r($col_settings, true), true);
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				$on_create_action	= "";
				$on_edit_action		= "";
				
				$input_type			= (isset($col_settings["type"]) ? $col_settings["type"] : "text");
				$required			= (isset($col_settings["required"]) ? true:false);
				$hidden				= (isset($col_settings["hidden"]) ? true:false);
				$enabled			= (isset($col_settings["enabled"]) ? true:false);
				$input_options		= null; 
				
				write2Debugfile("crud_generate.log", "- col[".$column_def["COLUMN_NAME"]."] type[".$col_settings["type"]."] required[".$required."] hidden[".$hidden."]", true);
				
				if ($hidden == false && $column_def["COLUMN_NAME"] != "client_id") {
					$table_columns .= ",\n\t\t\t".'new HTML_DTColumn("'.$column_def["COLUMN_NAME"].'", lang("'.$column_def["COLUMN_NAME"].'"), E_SORTABLE::YES)';
				}
				
				$matches = array();
				if ($column_def["DATA_TYPE"] == "enum"){
					preg_match_all('/\'([^\']*)\'/', $column_def['COLUMN_TYPE'], $matches);
					$input_options = $matches[1];
				}
				
				$validation_rules	= "trim";
				

				// build rules
				if ($required && $column_def["COLUMN_NAME"] != "client_id") {
					$validation_rules .= "|required";
				}
					
				if ((int)$column_def["CHARACTER_MAXIMUM_LENGTH"] > 0){
					$validation_rules .= "|max_length[".$column_def["CHARACTER_MAXIMUM_LENGTH"]."]";
				}
				if (in_array($column_def["DATA_TYPE"], $int_dataTypes)){
					$validation_rules .= "|integer";
				}
				if ($column_def["COLUMN_NAME"] == $cls."_name") {
					$xxx = '$this->input->post';
					$validation_rules .= "|validate_is_unique[\".".$xxx."('".$cls."_name_orig').\", '".$tbl."', '".$cls."_name', \".lang(\"".$cls."_already_exist\").\"]";
				}
				
				if ($column_def["COLUMN_KEY"] == "PRI" && ($column_def["COLUMN_NAME"] != "client_id" && $column_def["EXTRA"] != "auto_increment") ) {
					$on_create_action = 'BASE_Model::generateUID("'.$column_def["TABLE_NAME"].'", "'.$column_def["COLUMN_NAME"].'")';
				}
					
				$crud[$column_def["COLUMN_NAME"]] = array(
					"on_create_action" => $on_create_action,
					"on_edit_action"=>$on_edit_action,
					"input_type" => $input_type,
					"validation_rules" => $validation_rules,
					"col_def"=>$column_def,
					"input_type"=>$input_type,
					"input_options"=>$input_options,
					"required"=>$required,
					"hidden"=>$hidden,
					"enabled"=>$enabled
				);
			}
			
			write2Debugfile("crud_generate.log", "\n - CRUD-".print_r($crud, true), true);
			
			$table_columns .= "\n\t\t);";
			

			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// ..:: RUN
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$file_success	= array();
			$file_errors	= array(); 
			$code_templates = array(
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls.".php", "template"=>"controller", "package"=>"application/controllers/admin/"),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls."_model.php", "template"=>"model", "package"=>"application/models/"),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls."_delete.php", "template"=>"view_delete", "package"=>"application/views/admin/".$cls."/",  ),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls."_list.php", "template"=>"view_list", "package"=>"application/views/admin/".$cls."/"),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls."_form.php", "template"=>"view_form", "package"=>"application/views/admin/".$cls."/"),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns, 
						"filename"=>$cls.".js", "template"=>"js", "package"=>"resources/js/"),
					
				array("cdata"=>$crud, "db_table"=>$tbl, "db_name_field"=>$nameField, "classname"=>$cls, "permissions"=>$permissions, "table_columns"=>$table_columns,
						"filename"=>"T_".ucfirst($cls).".php", "template"=>"value_object", "package"=>"application/libraries/value_objects/")
			);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			if (!is_dir($target_folder)){
				mkdir($target_folder, null, true);
			}
			
			$this->setViewData("code_permissions", $permissions);
			$this->setViewData("code_templates", $code_templates);
			$this->setViewData("code_table_columns", $table_columns);
			$this->setViewData("code_crud_data", $crud);
			
			foreach ($code_templates as $template) 
			{
				$template_path 	= root_path()."application/views/root/crud/templates/".$template["template"].".php";
				$package_path	= root_path().$target_folder."/".$template["package"]; 
				if (!is_dir($package_path)){
					mkdir($package_path, null, true);
				}
				
				if (is_file($template_path))
				{
					// load the template view and pass data
					$string = $this->load->view("root/crud/templates/".$template["template"], $template, TRUE);
						
					if ( ! write_file($package_path.$template["filename"], $string))
					{
						$file_errors[] = lang("could_not_create_file").': <b>'.$template["filename"]."</b>";
					}else{
						$file_success[] = lang("source_file_created").": <b>".$template["package"].$template["filename"]."</b>";
					}
				}else{
					$file_errors[] = lang("template_not_found").': <b>'.$template["template"].'</b> | '.$template_path;
				}
			}
			
			$this->setViewStatus(E_STATUS_CODE::SUCCESS);
			
			if (count($file_errors) > 0){
				$this->setViewStatus(E_STATUS_CODE::ERROR);
				$this->setViewError(implode("<br>", $file_errors));
			}
			if (count($file_success) > 0)
			{
				$additional_stuff = "E_PERMISSIONS::<br>";
				foreach ($permissions as $action => $permission)
				{
					$additional_stuff .= 'const '.strtoupper($cls).'_'.strtoupper($action).' 			= "right_'.strtolower($cls).'_'.$action.'";<br>';
				}
				
				$additional_stuff .= "<hr>".$sql_permission;
				$additional_stuff .= "<hr>".$sql_menu;
				$additional_stuff .= "<hr>".$sql_menu_l18n;
				$additional_stuff .= "<hr>".$sql_permission_l18n;
				
				$this->setViewSuccess(implode("<br>", $file_success));
				$this->setViewInfo($additional_stuff);
				
			}
			
			else{
				$this->setViewStatus(E_STATUS_CODE::SUCCESS);
				$this->setViewSuccess(count($code_templates)." ".lang("source_files_created"));
			}
			
			
			$generated	= true;
		}
		else {
			write2Debugfile("crud_generate.log", "\n - form validation failed...\n".validation_errors(), true);
			
			$this->setViewError(validation_errors());
			$this->setViewExtra($this->form_validation->error_array() );
			$generated	= false;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setViewData("generated", $generated);
		
		// load table data immediatly since the ajax way will not work without js
		$modelResult 	= $this->crud_model->datatable($this->client_id, $this->table_columns, $this->input->post("table"));
		$data 			= json_decode($modelResult->getData())->data;
		$this->setViewData("selected_table_columns", $data );
			
		
		
		$this->setViewData("cdata", $crud);
		write2Debugfile("crud_generate.log", "\nthis->data\n".print_r($this->data, true));
			
		return $generated;
		
	}
	
	public function tinyMCE()
	{
		if (is_array($this->input->post()) && $this->input->post("generate") == 1 ){
			self::generate();
		}
		elseif (is_array($this->input->post()) && $this->input->post("analize") == 1 ){
			self::analyze();
		}
		else{
			$this->setViewData("selected_table_columns", array() );					// table data with db-column definitions
				
		}
		
		$this->setViewData("selected_table", $this->input->post("table"));			// selected table
		$this->setViewData("selected_classname", $this->input->post("classname"));	// entered classname
		$this->setViewData("tables", $this->tables);								// for use in the select
		$this->setViewData("table_columns", $this->table_columns );
		
		
		write2Debugfile(self::DEBUG_FILENAME, " - render view\n".print_r($this->data, true), true);
		$this->render('root/crud/overview', E_RENDERMODE::FULLPAGE);
	}
	
	private function tabledata($tbl){
		if (array_key_exists($tbl, $this->fields)){
			return $this->fields[$tbl];
		}
		return array();
		
	}
}


?>