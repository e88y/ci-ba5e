<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Superuser localization controller
 *
 * @author Marco Eberhardt
 * @category locales
 * @package application\controllers\root\locales
 * @version 1.0
 */
class Locales extends BASE_Controller
{
	const DEBUG_FILENAME = "locales.log";
	
	/**
	 * @var array
	 */
	private $permissions					= array();
	
	/**
	 * columns for the locales table 
	 * @var array
	 */
	private $table_columns					= array();
	
	/**
	 * Constructor for the locales controller
	 */
	function __construct()
	{
		parent::__construct(true, true);
		
		$this->load->library("value_objects/T_Locale.php");
		$this->load->model("locale_model");
		$this->load->helper("file_helper");
		
		$this->javascript		= array("locales.js");
		
		$this->addPlugins(
			E_PLUGIN::DATATABLES,
			E_PLUGIN::BS_TOGGLE,
			E_PLUGIN::FILE_INPUT,
			E_PLUGIN::SELECT2
		);
		
		$this->table_columns = T_Locale::get_table_columns();
		
		write2Debugfile(self::DEBUG_FILENAME, "root/locales ", false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index(){
		self::show($this->getSessionData(E_SESSION_ITEM::USER_LANGUAGE), E_RENDERMODE::FULLPAGE);
	}
	
	/**
	 * render view to create new loalization entry
	 *
	 * @version 1.2
	 * @param string $locale_code >> target language 
	 * @param E_RENDERMODE $rendermode
	 */
	public function create($locale_code="DE", $rendermode="FULLPAGE")
	{
		if ($this->input->post("locale_code") != ""){
			$locale_code = $this->input->post("locale_code");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (! array_key_exists($locale_code, $this->available_languages))
		{
			$this->setData( new BASE_Result(null, lang("msg_invalid_parameter"), null, E_STATUS_CODE::NOT_FOUND)  );
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->breadcrump = $locale_code;
		
		if (is_array($this->input->post()) && $this->input->post("save") == 1 )
		{	
			// only if we have a post, we try to save
			// note that the save method overwrites the locale-viewdata
			self::save(false);
		}
		else 
		{
			$this->setViewData("locale", array("locale_code"=>$locale_code));
		}
	
		write2Debugfile(self::DEBUG_FILENAME, "create new locale entry\n".print_r($this->data, true));
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_languages", $this->available_languages);
		$this->setViewData("selected_locale", $locale_code);
	
		$this->render('root/locale/locale_form', $rendermode);
	}
	
	/**
	 * Ajax data-source for the datatable
	 * JSON-Rendermode
	 *
	 * @version 1.2
	 * 
	 * @param string $locale_code
	 */
	public function datatable($locale_code="DE")
	{
		$edit	= $this->hasPermission(E_PERMISSIONS::ROOT_LOCALE_EDIT);
		$delete	= $this->hasPermission(E_PERMISSIONS::ROOT_LOCALE_DELETE);
			
		$result = $this->locale_model->datatable($this->client_id, $locale_code, $this->table_columns, $edit, $delete);
		$result->data = json_decode($result->data);	// because the render method will encode it again
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}
	
	/**
	 * load locale data and set view data
	 *
	 * @version 1.2
	 * 
	 * @param string $locale_code >> locale code
	 * @param string $locale_id >> locale token
	 * @param E_RENDERMODE $rendermode 	>>
	 */
	public function edit($locale_code="DE", $locale_id=null, $rendermode="FULLPAGE")
	{
		if ($this->input->post("locale_code") != ""){
			$locale_code = $this->input->post("locale_code");
		}
		if ($this->input->post("locale_token") != ""){	
			$locale_id = $this->input->post("locale_token");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (! array_key_exists($locale_code, $this->available_languages) || $locale_id == "")
		{
			$this->setViewWarning("locale_code[$locale_code] Identifier[$locale_id]");
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
	
		//$locale_id = decrypt_string($locale_id);
		
		write2Debugfile(self::DEBUG_FILENAME, "edit locale code[$locale_code] locale_id[$locale_id] \npost-".print_r($this->input->post(), true));
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result_locale = $this->locale_model->load( $locale_code, $locale_id);
	
		write2Debugfile(self::DEBUG_FILENAME, "edit locale code[$locale_code] locale_id[$locale_id] - ".print_r($result_locale, true), true);
			
		if (count($result_locale->getData()) == 1 && $result_locale->getError() == "")
		{
			$this->breadcrump = $result_locale->data[0]->locale_id;
			
			if (is_array($this->input->post()) && $this->input->post("save") == 1  )
			{	// if we have a post, we try to save
				// note that the save method overwrites the locale-viewdata with the stuff from the post
				self::save(true);
			}
			else
			{
				$this->setViewData("locale", $result_locale->data[0]);
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "locale NOT FOUND code[$locale_code] locale_id[$locale_id]", false);
			$this->breadcrump = $locale_id;
			$this->setViewError(lang("msg_not_found"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_languages", $this->available_languages);
		$this->setViewData("selected_locale", $locale_code);
	
		$this->render('root/locale/locale_form', $rendermode);
	}
	
	/**
	 * Export selected locale to xls
	 */
	public function export($locale_code)
	{
		$result = $this->locale_model->datatable($this->client_id, $locale_code, $this->table_columns, 0, 0);
		$result->data = json_decode($result->data);	// because the render method will encode it again
		
		$this->load->library("Excel");
		$this->excel->generateXLSFromCSVArray(array("sheet"=>"name;mail;active###max;max@x.com;1",
				"sheet2"=>"name;mail;active###max;max@x.com;1" 
		), ";", "###");
	}
	
	/**
	 * Export template file
	 */
	public function export_template($format="csv")
	{
		$filename = "locale_importfile.csv";
		/*
		$template = array(
			'"locale_code"', '"locale_id"', '"text"', '"group_token"'
		);
		$csv = implode(";", $template);
		*/
		
		
		header('Content-Disposition: attachment; filename=data.csv');
		header('Content-Type: "text/comma-separated-values; charset=utf-8"'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); 	//no cache
		
		$output = fopen('php://output', 'w');	// create a file pointer connected to the output stream
		fputcsv($output, array("locale_code", "locale_id", "text", "group_token"));	// output the column headings
		fclose($output);
		exit();
	}
	
	/**
	 * generate a localization file and stores it in the language folder
	 * ('application/language/[folder]/[folder]_lang.php') 
	 * 
	 * @version 1.22
	 * 
	 * @param string $locale_code	>> locale you want to generate or pass ALL to generate all available localization files
	 * @param E_RENDERMODE $rendermode
	 */
	public function generate($locale_code="ALL", $rendermode="FULLPAGE")
	{
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->input->post("locale_code") != "" && $locale_code == "")
		{	//default 
			$locale_code = $this->input->post("locale_code"); 
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		
		
		// language specs containing the target folder
		$localization_result	= $this->locale_model->load_languages($this->client_id, ($locale_code == "ALL" ? null: $locale_code) );	 
		$locales_to_generate	= $localization_result->data;

		//$this->setViewInfo(nl2br(print_r($localization_result, true))."\n".$this->locale_model->lastQuery());
		foreach ($locales_to_generate as $locale_code => $locale_head)
		{
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// load localization entries
			$locales 	= $this->locale_model->load($locale_code);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// define target folder and filename
			$folder 	= $locale_head->folder;
			$filepath 	= APPPATH.'language/'.$folder.'/';
			$filename	= $folder.'_lang.php';
			if (! is_dir($filepath)){
				mkdir($filepath);
			}

			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// beginn file content
			$file_content 	= array();
			$file_content[] = '<?php';
			$file_content[] = '/** '.$locale_code.' l18n file */';
		
			$last_grp 		= "";
			foreach ($locales->data as $locale)
			{
				if ($locale->group_token != $last_grp)
				{
					$file_content[] = '// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..';
					$file_content[] = '// ..:: '.$locale->group_token;
					$file_content[] = '// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..';
		
					$last_grp = $locale->group_token;
				}
				if ($locale->group_token == 'json' || $locale->locale_id == 'datatable' )
				{
					$file_content[] = '$lang["'.$locale->locale_id.'"] = \''.$locale->text.'\';';
				}
				else
				{
					$file_content[] = '$lang["'.$locale->locale_id.'"] = "'.htmlentities($locale->text, ENT_QUOTES, "UTF-8", false).'";';
				}
			}
			$file_content[] = '?>';
		
			$file_content = implode("\n", $file_content);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// write content to file
			if ( ! write_file($filepath.$filename, $file_content))
			{
				$this->setViewError("[$locale_code] ".lang("error_write_file"));
			}
			else
			{
				$this->setViewSuccess("[$locale_code] ".lang("msg_localization_file_written"), true);
			}
				
		}
		
		self::show($locale_code, $rendermode);
	}
	
	/**
	 * Delete localization entry. Acccepts also POST-Data
	 * 
	 * @version 1.2
	 * 
	 * @param string $locale_code 	>> for which language you want delete
	 * @param string $locale_id 	>> locale , you want to delete
	 * @param bool $confirmed 		>> if true, the user has confirmed this action
	 * @param E_RENDERMODE $rendermode
	 * 
	 * @return bool
	 */
	public function remove($locale_code="", $locale_id="", $confirmed=0, $rendermode="FULLPAGE")
	{
		if ($this->input->post("locale_code") != "" && $locale_code == ""){
			$locale_code = $this->input->post("locale_code");
		}
		if ($this->input->post("locale_id") != "" && $locale_id == ""){
			$locale_id = $this->input->post("locale_id");
		}
		if ($this->input->post("confirmed") == 1 && $confirmed == 0){
			$confirmed = 1;
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if (! array_key_exists($locale_code, $this->available_languages) || $locale_id == "")
		{
			//$this->setViewWarning("locale_code[$locale_code] Identifier[$locale_id]");
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result 			= new BASE_Result(false);	// action needs confirmation first
		$removed			= false;
		$result_locale		= $this->locale_model->load($locale_code, $locale_id );
		
		write2Debugfile(self::DEBUG_FILENAME, "remove locale code[$locale_code] locale_id[$locale_id] -".print_r($result_locale, true), true);
		
		if (count($result_locale->getData()) == 1 && $result_locale->getError() == "")
		{
			$this->breadcrump = $result_locale->data[0]->locale_id." (".$result_locale->data[0]->locale_code.")";
			
			if ($confirmed == 1){
				$result	= $this->locale_model->remove($locale_code, $locale_id, $this->getSessionData(E_SESSION_ITEM::USERNAME));
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "locale code[$locale_code] locale_id[$locale_id] NOT found", true);
			$this->breadcrump 	= $locale_id." (".$locale_code.")";
			$result 			= new BASE_Result(false, lang("msg_locale_not_found"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
	
		if ($result->data == true && $result->error == "")
		{
			$removed = true;
			$this->setViewSuccess(lang("locale_has_been_deleted"));
			//self::show($locale_code, $rendermode);
			//return ;
		}
		$this->setViewData("removed", $removed);
		$this->setViewData("confirmed", $confirmed);
		$this->setViewData("locale", $result_locale->data);
	
		$this->render('root/locale/locale_delete', $rendermode);
		return $removed;
	}
	
	/**
	 * saves a locale after input validation and sets the viewdata
	 *
	 * @version 1.2
	 *
	 * @param bool $edit 	>> create or update action
	 * @return boolean  	>> returns the saved state
	 */
	private function save($edit=false)
	{
		$post 	= $this->input->post(null, true);
		$saved	= false;
	
		if ($this->input->post("locale_id_orig") != "" && $edit == false) {	// correct wrong save mode
			$edit = true;
		}
		
		write2Debugfile(self::DEBUG_FILENAME, " - save locale edit[$edit]\n".print_r($post, true), true);
		
		$permission = ($edit ? E_PERMISSIONS::ROOT_LOCALE_EDIT : E_PERMISSIONS::ROOT_LOCALE_CREATE);
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set validation rules
		$this->form_validation->set_rules($permission, 	'lang:msg_no_permission', 'trim|validate_permission');
		$this->form_validation->set_rules('locale_code','lang:locale_code', 'trim|required|exact_length[2]');
		$this->form_validation->set_rules('locale_group','lang:group', 		'trim|required');
		$this->form_validation->set_rules('locale_token',	'lang:locale_id', 	'trim|required|max_length[100]|validate_is_unique['.$this->input->post("locale_id_orig").', app_locales__generic, locale_id, '.lang("msg_entry_already_exist").']');
		$this->form_validation->set_rules('locale_text', 'lang:text', 		'trim|required|min_length[1]');
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$deleted_at = $this->input->post("deleted_at");
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_at") == ""){
			$deleted_at = time();
		}
	
		$deleted_by = $this->input->post("deleted_by");
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_by") == ""){
			$deleted_by = $this->getSessionData(E_SESSION_ITEM::USERNAME);
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->input->post("locale_token") == ""){
			$locale_id = BASE_Model::generateUID(TBL_LOCALES, "locale_id", "", false, 20);
		}else{
			$locale_id = $this->input->post("locale_token");
		}
		
		$data = array(
			"locale_id" => $locale_id,
			"locale_code" => $this->input->post("locale_code"),
			"text" => $this->input->post("locale_text"),
			"group_token" => $this->input->post("locale_group"),
			"deleted" => ($this->input->post("deleted") == "1" ? 1:0),
			"deleted_at" => $deleted_at,
			"deleted_by" => $deleted_by
		);
	
		if ($this->input->post("created_at") == "" && $edit == false){
			$data["created_at"] = time();
		}
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run() )
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed edit[$edit]...", true);
				
			if ($edit == true){
				$result = $this->locale_model->update($this->input->post("locale_code"), $this->input->post("locale_id_orig"), $data );
			}
			else{
				$result = $this->locale_model->create($this->input->post("locale_code"), $this->input->post("locale_id"), $data );
			}
		}
		else {
			$result = new BASE_Result(null, validation_errors(), $this->form_validation->error_array() );
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n".validation_errors(), true);
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		if ($result->error == "")
		{
			$this->setViewSuccess(lang("locale_has_been_saved"));
			$saved = true;
		}
		$this->setViewData("locale", $data);	// re-populate a filled out form
		$this->setViewData("saved", $saved);
	
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
			
		return $saved;
	}
	
	/**
	 * render the locales list
	 * @version 1.2
	 *
	 * @param string $locale_code
	 * @param E_RENDERMODE $rendermode
	 */
	public function show($locale_code="DE", $rendermode="FULLPAGE")
	{
		if (! array_key_exists($locale_code, $this->available_languages) || $locale_code == null)
		{
			$this->render(E_ERROR_VIEW::INVALID_PARAMS, $rendermode);
			return;
		}
	
		$this->breadcrump = $locale_code;
			
		$edit	= $this->hasPermission(E_PERMISSIONS::ROOT_LOCALE_EDIT);
		$delete	= $this->hasPermission(E_PERMISSIONS::ROOT_LOCALE_DELETE);
	
		$data = array();
		if ($this->getSessionData(E_SESSION_ITEM::JS_ENABLED) == false)
		{
			$modelResult 	= $this->locale_model->datatable( $this->client_id, $locale_code, $this->table_columns, $edit, $delete );
			$data 			= json_decode($modelResult->getData())->data;
		}
		write2Debugfile(self::DEBUG_FILENAME, " - root/locales/show\n".print_r($data, true), true);
	
		$this->setViewData("table_data", $data);
		$this->setViewData("table_columns", $this->table_columns );
		$this->setViewData("available_locales", $this->available_languages);
		$this->setViewData("selected_locale", $locale_code);
		$this->setViewData("permissions", $this->permissions);
	
		$this->render("root/locale/locale_list", $rendermode);
	}
	
	/**
	 * INSERT INTO `app_locales__generic` 
	 * (`locale_code`, `locale_id`, `text`, `group_token`, `created_at`, `deleted`, `deleted_at`, `deleted_by`) VALUES ('ss', 'sad', '', '', '', '0', NULL, NULL), ('ss', 'aaa', '', '', '', '0', NULL, NULL);
	 */
	public function toSQL($locale_code="DE")
	{
		$language = $this->lang->language;
		
		$sql = "REPLACE INTO ".TBL_APP_LOCALES_GENERIC." (`locale_code`, `locale_id`, `text`, `group_token`, `created_at`, `deleted`) VALUES ";
		foreach ($language as $locale_id => $text) 
		{
			$sql .= "('$locale_code', '$locale_id', '$text', 'system', '".time()."', 0),\n"; 
		}
		
		$sql = substr($sql, 0, -2);
		
		//echo nl2br(print_r($lang, true));
		echo nl2br($sql);
	}
}
?>