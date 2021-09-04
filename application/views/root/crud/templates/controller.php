<?php 

	/***
	 * $db_table
	 * $classname
	 * $package
	 * $table_columns
	 * $permissions
	 */

	$exclude_from_data 	= array("client_id", "deleted", "deleted_at", "deleted_by");
	$exclude_from_rules	= array("client_id", $classname."_id");
	
	$data = '$data = array(';
	$data .= "\n\t\t\t".'"client_id" => $this->client_id,'; 
	$data .= "\n\t\t\t".'"deleted" => ($this->input->post("deleted") == "1" ? 1:0),';
	$data .= "\n\t\t\t".'"deleted_at" => $deleted_at,';
	$data .= "\n\t\t\t".'"deleted_by" => $deleted_by,';

	$validation_rules = '';
	foreach ($cdata as $key => $value) {
		
		if (! in_array($key, $exclude_from_rules)){
			$validation_rules .= '$this->form_validation->set_rules("'.$key.'", "lang:'.$key.'", "'.$value["validation_rules"].'");'."\n"."\t\t";
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($key == $classname.'_id'){
			$data .= "\n\t\t\t".'"'.$key.'" => $'.$classname.'_id,';
		}
		elseif (! in_array($key, $exclude_from_data)){
			$data .= "\n\t\t\t".'"'.$key.'" => $this->input->post("'.$key.'"),';
		}
		
	}
	$data = substr($data, 0, -1);
	$data .= "\n\t\t);";

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$str = '<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
/**
 * '.ucfirst($classname).' controller
 *
 * @author Marco Eberhardt
 * @category controller
 * @package '.$package.strtolower($classname).'
 * @version 1.0
 */
class '.ucfirst($classname).' extends BASE_Controller 
{
	const DEBUG_FILENAME = "'.strtolower($classname).'.log";
		
	/**
	 * array of HTML_DTColumns for the '.strtolower($classname).' table
	 * @var array
	 */
	private $table_columns		= array();
	
	/**
	 * Constructor for the '.strtolower($classname).' controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		$this->load->model("'.$classname.'_model");
    	$this->load->library("value_objects/T_'.ucfirst($classname).'.php");

    	$this->javascript		= array("'.strtolower($classname).'.js");
    	
    	$this->addPlugins(
			E_PLUGIN::DATATABLES,
			E_PLUGIN::BS_TOGGLE
    	);
    	
    	'.$table_columns.'
    	
    	
		
		write2Debugfile(self::DEBUG_FILENAME, "'.$package.'\ntable-columns-".print_r($this->table_columns, true), false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index() {
        self::show();
	}
	
	/**
	 * render view to create new '.strtolower($classname).'
	 *
	 * @version 1.2
	 * @param E_RENDERMODE $rendermode
	 */
	public function create($rendermode="FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->breadcrump = "";

		$saved = false;
		if (is_array($this->input->post()) && $this->input->post("save") == 1 )
		{	// only if we have a post, we try to save. Note that the save method also sets viewdata
			$saved = self::save(false);
		}
		else
		{
			$this->setViewData("'.$classname.'", array());
		}

		write2Debugfile(self::DEBUG_FILENAME, "create new '.$classname.'\n".print_r($this->data, true));
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// $this->setViewData("additional_viewdata", array() );

		$this->render("admin/'.$classname.'/'.$classname.'_form", $rendermode);
	}
	
	/**
	 * Ajax data-source for the datatable.
	 * JSON-Rendermode
	 *
	 * @version 1.2
	 */
	public function datatable()
	{
		if ($this->hasPermission('.$permissions["list"].'))
		{
			$edit	= $this->hasPermission('.$permissions["update"].');
			$delete	= $this->hasPermission('.$permissions["delete"].');
				
			$result 		= $this->'.$classname.'_model->datatable( $this->client_id, $this->table_columns, $edit, $delete);
			$result->data 	= json_decode($result->data);	// because the render method (JSON_DATA) will encode it again
			write2Debugfile(self::DEBUG_FILENAME, "\n'.$classname.' datatable edit[$edit] del[$delete] columns-".print_r($this->table_columns, true)."\n".print_r($result, true));
		}
		else{
			$result = new BASE_Result(json_encode(array()), lang("msg_no_permission"), null, E_STATUS_CODE::FORBIDDEN);
		}
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}
	
	/**
	 * load '.$classname.' entry, set viewdata and render the form
	 * 
	 * @version 1.2
	 * @param string $'.$classname.'_id >> '.$classname.' identifier
	 * @param E_RENDERMODE $rendermode 
	 */
	public function edit($'.$classname.'_id=null, $rendermode="FULLPAGE")
	{
		if ($this->input->post("'.$classname.'_id") != ""){
			$'.$classname.'_id = $this->input->post("'.$classname.'_id");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($'.$classname.'_id == null || $'.$classname.'_id == "") 
		{
			$this->render("errors/error_invalid_parameter", $rendermode);
			return; 
		}
				
		$'.$classname.'_id = decrypt_string($'.$classname.'_id);	// decrypt the identifier
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result_'.$classname.' = $this->'.$classname.'_model->load( $this->client_id, $'.$classname.'_id );
		
		write2Debugfile(self::DEBUG_FILENAME, "edit '.$classname.' client_id[".$this->client_id."] '.$classname.'_id[$'.$classname.'_id] -".print_r($result_'.$classname.', true));
		
		if (count($result_'.$classname.'->getData()) == 1 && $result_'.$classname.'->getError() == "")
		{
			$this->breadcrump = $result_'.$classname.'->data->'.$db_name_field.';
			
			$saved = false;
			if (is_array($this->input->post()) && $this->input->post("save") == 1 )
			{	// if we have a post, we try to save. Note that the save method sets viewdata 
				$saved = self::save(true);
			}
			else
			{
				$this->setViewData("'.$classname.'", $result_'.$classname.'->data );
			}	
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "'.$classname.'[$'.$classname.'_id] NOT found", true);
			$this->breadcrump = $'.$classname.'_id;
			$this->setViewError(lang("msg_not_found"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// $this->setViewData("additional_viewdata", array() );
					
		$this->render("admin/'.$classname.'/'.$classname.'_form", $rendermode);
	}
	
	/**
	 * Deletes a entry. Acccepts also POST-Data.
	 * 
	 * @version 1.2
	 * 
	 * @param string $'.$classname.'_id >> '.$classname.' id, you want to delete.
	 * @param bool $confirmed >> if true, the user has already confirmed this action.
	 * @param E_RENDERMODE $rendermode
	 * 
	 * @return bool >> true if the entry has been removed
	 */
	public function remove($'.$classname.'_id="", $confirmed=0, $rendermode="FULLPAGE")
	{
		if ($this->input->post("'.$classname.'_id") != "" && $'.$classname.'_id == ""){
			$'.$classname.'_id = $this->input->post("'.$classname.'_id");
		}
		if ($this->input->post("confirmed") == true && $confirmed == 0){
			$confirmed = 1;
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($'.$classname.'_id == null || $'.$classname.'_id == "") 
		{
			$this->render("errors/error_invalid_parameter", $rendermode);
			return; 
		}
		
		$'.$classname.'_id = decrypt_string($'.$classname.'_id);	// decrypt the identifier
				
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result 		= new BASE_Result(false);	// action needs confirmation first
		$removed		= false;
		$result_'.$classname.' 	= $this->'.$classname.'_model->load( $this->client_id, $'.$classname.'_id );
		
		write2Debugfile(self::DEBUG_FILENAME, "remove '.$classname.' [$'.$classname.'_id] -".print_r($result_'.$classname.', true), false);
		
		if (count($result_'.$classname.'->getData()) == 1 && $result_'.$classname.'->getError() == "")
		{
			$this->breadcrump = $result_'.$classname.'->data->'.$db_name_field.';
			
			if ($confirmed == 1) {
				$result	= $this->'.$classname.'_model->remove($this->client_id, $'.$classname.'_id, $this->getSessionData(E_SESSION_ITEM::USERNAME));
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "'.$classname.'[$'.$classname.'_id] NOT found", true);
			$this->breadcrump 	= $'.$classname.'_id;
			$result 			= new BASE_Result(false, lang("msg_'.$classname.'_not_found"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		
		if ($result->data == true && $result->error == "")
		{
			$removed = true;
			$this->setViewSuccess(lang("'.$classname.'_has_been_deleted"));
		}
		$this->setViewData("removed", $removed);
		$this->setViewData("confirmed", $confirmed);
		$this->setViewData("'.$classname.'", $result_'.$classname.'->data);
		
		$this->render("admin/'.$classname.'/'.$classname.'_delete", $rendermode);
		return $removed;
	}
	
	/**
	 * Saves a '.$classname.' after input validation and sets the viewdata
	 * Note: The permission check is made via validation callback (@see BASE_Form_validation)
	 * 
	 * @version 1.2
	 * 
	 * @param bool $edit 	>> create or update action
	 * @return boolean  	>> returns the saved state
	 */
	private function save($edit)
	{
		write2Debugfile(self::DEBUG_FILENAME, "save '.$classname.'\n".print_r($this->input->post(), true), false);
	
		$post 	= $this->input->post();
		$saved	= false;
		
		if ($this->input->post("'.$classname.'_id") != "" && $edit == false){	
			// correct wrong save mode
			$edit = true;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set validation rules
		'.$validation_rules.'
				
				

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
		if ($this->input->post("'.$classname.'_id") == ""){
			$'.$classname.'_id = BASE_Model::generateUID("'.$db_table.'", "'.$classname.'_id", $this->client_id."#", false, 20);
		}else{
			$'.$classname.'_id = $this->input->post("'.$classname.'_id");
		}
		
		'.$data.'
		
		if ($this->input->post("created_at") == "" && $edit == false){
			$data["created_at"] = time();
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run() )
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
			
			if ($edit == true){
				$result = $this->'.$classname.'_model->update($this->client_id, $this->input->post("'.$classname.'_id"), $data);
			}
			else{
				$result = $this->'.$classname.'_model->create($this->client_id, $data );
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
			$this->setViewSuccess(lang("'.$classname.'_has_been_saved"));
			$saved = true;
		}
		$this->setViewData("'.$classname.'", $data);	// fill the '.$classname.' with the givven post data so the view can populate a filled out form
		$this->setViewData("saved", $saved);
		// $this->setViewData("additional_viewdata", array() );
	
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
		 
		return $saved;
	}
	
	/**
	 * render the '.$classname.' list
	 * @version 1.2
	 *
	 * @param E_RENDERMODE $rendermode
	 */
	public function show($rendermode="FULLPAGE")
	{
		$data = array();
		if ($this->getSessionData(E_SESSION_ITEM::JS_ENABLED) == false)
		{
			// load table data immediatly since the ajax way will not work without js
			$edit			= $this->hasPermission('.$permissions["update"].');
			$delete			= $this->hasPermission('.$permissions["delete"].');
			$modelResult 	= $this->'.$classname.'_model->datatable( $this->client_id, $this->table_columns, $edit, $delete );
			$data 			= json_decode($modelResult->getData())->data;
		}

		write2Debugfile(self::DEBUG_FILENAME, " - admin/'.$classname.'/show\n".print_r($data, true), false);

		$this->setViewData("table_data", $data);
		$this->setViewData("table_columns", $this->table_columns );

		$this->render("admin/'.$classname.'/'.$classname.'_list", $rendermode);
	}
}
?>';
	
	echo $str;
?>