<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Superuser clients controller
 *
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\root\clients
 * @version 1.2
 */
class Clients extends BASE_Controller 
{
	const DEBUG_FILENAME = "root_clients.log";
	
	/**
	 * Constructor for the clients controller
	 */
	function __construct()
	{
		parent::__construct(true, true);
		
		$this->load->library("value_objects/T_Client.php");
		$this->load->model("client_model");
		
		$this->javascript		= array("clients.js");
    	
    	$this->addPlugins(
    		E_PLUGIN::DATATABLES,
    		E_PLUGIN::SELECT2,
    		E_PLUGIN::BS_TOGGLE,
    		E_PLUGIN::FILE_INPUT
    	);
    	
		write2Debugfile(self::DEBUG_FILENAME, "root/client", false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index() {
		self::show();
	}
	
	/**
	 * render view to create new client
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
			
		if (is_array($this->input->post()) && $this->input->post("save") == 1)
		{	// only if we have a post, we try to save
			// note that the save method overwrites the client-viewdata
			self::save(false);
		}
		else
		{
			$this->setViewData("client", array());
		}

		write2Debugfile(self::DEBUG_FILENAME, "create new client\n".print_r($this->data, true));

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->render('root/client/client_form', $rendermode);
	}
	
	/**
	 * Ajax data-source for the datatable
	 * JSON-Rendermode
	 * 
	 * @version 1.2
	 */
	public function datatable()
	{
		$edit	= $this->hasPermission(E_PERMISSIONS::ROOT_CLIENT_EDIT);
		$delete	= $this->hasPermission(E_PERMISSIONS::ROOT_CLIENT_DELETE);
			
		$result = $this->client_model->datatable( $this->client_id, T_Client::get_table_columns(), $edit, $delete);
		$result->data = json_decode($result->data);	// because the render method will encode it again
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}
	
	/**
	 * load client data, set view data and render the clients form
	 *
	 * @version 1.2
	 *
	 * @param string $client_id 		>> client identifier
	 * @param E_RENDERMODE $rendermode 	>>
	 */
	public function edit($client_id=null, $rendermode="FULLPAGE")
	{
		if ($this->input->post("client_id") != ""){
			$client_id = $this->input->post("client_id");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
	
		if ($client_id == null || $client_id == "")
		{
			$this->render(E_ERROR_VIEW::INVALID_PARAMS, $rendermode);
			return;
		}
		
		write2Debugfile(self::DEBUG_FILENAME, "edit client client_id[".$client_id."]", false);
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result_client = $this->client_model->load($client_id);
	
		if (count($result_client->getData()) == 1 && $result_client->getError() == "")
		{
			write2Debugfile(self::DEBUG_FILENAME, " - edit client [$client_id] -".print_r($result_client, true), true);
				
			$this->breadcrump = $result_client->data[0]->client_name;
				
			if (is_array($this->input->post()) && $this->input->post("save") == 1 )
			{	// if we have a post, we try to save
				// note that the save method sets the client-viewdata
				self::save(true);
			}
			else
			{
				$this->setViewData("client", $result_client->data[0]);
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "client[$client_id] NOT found", true);
			$this->breadcrump = $client_id;
			$this->setViewError(lang("msg_not_found"));
		}
	
		write2Debugfile(self::DEBUG_FILENAME, " - client-".print_r($result_client, true)."\n", true);
		
		$this->render('root/client/client_form', $rendermode);
	}
	
	/**
	 * Deletes a client. Accepts POST-data
	 * 
	 * @version 1.2
	 * 
	 * @param string $client_id			>> client, you want to delete
	 * @param bool $confirmed 			>> if true, the user has confirmed this action
	 * @param E_RENDERMODE $rendermode 	>> as usual
	 * 
	 * @return bool
	 */
	public function remove($client_id="", $confirmed=0, $rendermode="FULLPAGE")
	{
		if ($this->input->post("client_id") != "" && $client_id == ""){
			$client_id = $this->input->post("client_id");
		}
		if ($this->input->post("confirmed") == true && $confirmed == 0){
			$confirmed = 1;
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($client_id == null || $client_id == "") 
		{
			$this->render('errors/error_invalid_parameter', $rendermode);
			return; 
		}
		if($client_id == $this->config->item("root_client_id")){
			$this->setViewError(lang("msg_you_cant_delete_this_entry"));
			$this->render('errors/error_general', $rendermode);
			return;
		}
		
					
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result 		= new BASE_Result(false);	// action needs confirmation first
		$removed		= false;
		$result_client	= $this->client_model->load($client_id );
		
		write2Debugfile(self::DEBUG_FILENAME, "remove client[$client_id] - ".print_r($result_client, true));
		
		if (count($result_client->getData()) == 1 && $result_client->getError() == "")
		{
			$this->breadcrump = $result_client->data[0]->client_name;
			if ($confirmed == 1){
				$result	= $this->client_model->remove($client_id, $this->getSessionData(E_SESSION_ITEM::USERNAME));
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "client[$client_id] NOT found", true);
			$this->breadcrump = $client_id;
			$result = new BASE_Result(false, lang("msg_client_not_found"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		if ($result->data == true && $result->error == "")
		{
			$removed = true;
			$this->setViewSuccess(lang("client_has_been_deleted"));
			//self::show($rendermode);
			//return ;
		}
		$this->setViewData("removed", $removed);
		$this->setViewData("confirmed", $confirmed);
		$this->setViewData("client", $result_client->data);
		
		$this->render('root/client/client_delete', $rendermode);
		return $removed;
	}
	
	/**
	 * Saves a client after input validation and sets the viewdata
	 * Note: The permission is checked by validation callback
	 * 
	 * @version 1.2
	 * 
	 * @param bool $edit 	>> create or update action
	 * @return boolean  	>> returns the saved state
	 */
	private function save($edit)
	{
		write2Debugfile(self::DEBUG_FILENAME, "save client\n".print_r($this->input->post(), true), false);
		
    	//$post 	= $this->input->post();
    	$saved	= false;
    	
    	if ($this->input->post("client_id") != "" && $edit == false)
    	{	// correct wrong save mode
    		$edit = true;
    	}
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	// ..:: set validation rules
    	$this->form_validation->set_rules('customer_number', 'lang:customer_number', 'trim|required|min_length[1]|max_length[255]|validate_is_unique['.$this->input->post("customer_number_orig").', '.TBL_CLIENTS.', customer_number, '.lang("customer_number_already_exist").']');
    	$this->form_validation->set_rules('name', 'lang:name', 'required|min_length[3]|max_length[255]|validate_is_unique['.$this->input->post("clientname_orig").', '.TBL_CLIENTS.', client_name, '.lang("user_already_exist").']');
    	$this->form_validation->set_rules('desc', 'lang:desc', 'trim|max_length[255]');
    	$this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email|max_length[255]');
    	$this->form_validation->set_rules('phone', 'lang:phone', 'trim|max_length[100]');
    	$this->form_validation->set_rules('fax', 'lang:fax', 'trim|max_length[100]');
    	$this->form_validation->set_rules('street', 'lang:street', 'trim|required|min_length[5]|max_length[255]');
    	$this->form_validation->set_rules('house_nr', 'lang:house_number', 'trim|required|max_length[5]');
    	$this->form_validation->set_rules('zipcode', 'lang:zipcode', 'trim|required|required|max_length[30]');
    	$this->form_validation->set_rules('location', 'lang:location', 'trim|required|max_length[255]');
    	$this->form_validation->set_rules('logo', 'lang:logo', 'trim|max_length[255]');
    	$this->form_validation->set_rules('deleted', 'lang:deleted', 'max_length[1]');
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	$deleted_at = $this->input->post("deleted_at");
    	if ($this->input->post("deleted") == "1" && $this->input->post("deleted_at") == ""){
    		$deleted_at = time();
    	}
    	
    	$deleted_by = $this->input->post("deleted_by");
    	if ($this->input->post("deleted") == "1" && $this->input->post("deleted_by") == ""){
    		$deleted_by = $this->getSessionData(E_SESSION_ITEM::USER_ID);
    	}
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	if ($this->input->post("client_id") == ""){
    		$client_id = BASE_Model::generateUID(TBL_ROLES, "client_id", "", false, 20);
    	}else{
    		$client_id = $this->input->post("client_id");
    	}
    	
    	$data = array(
			"client_id" => $client_id,
    		"customer_number" => $this->input->post("customer_number"),
			"client_name" => $this->input->post("name"),
			"client_desc" => $this->input->post("desc"),
			"client_email" => $this->input->post("email"),
			"client_phone" => $this->input->post("phone"),
			"client_fax" => $this->input->post("fax"),
			"client_street" => $this->input->post("street"),
			"client_house_nr" => $this->input->post("house_nr"),
			"client_zipcode" => $this->input->post("zipcode"),
			"client_location" => $this->input->post("location"),
			"client_logo" => $this->input->post("logo"),
    		"client_theme" => $this->input->post("theme", true),
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
    		write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
    		if ($edit == true)
    		{
    			$result = $this->client_model->update($client_id, $data );
    		}
    		else
    		{
    			$result = $this->client_model->create($data );
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
			$data["client_id"] = $client_id; // fill the role id so we run an update next time
			
			$this->setViewSuccess(lang("client_has_been_saved"));
			$saved = true;
		}
		$this->setViewData("client", $data);	// fill the client with the given post data so the view can populate a filled out form
		$this->setViewData("saved", $saved);

    	write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
    	
    	return $saved;
	}
	
	/**
	 * render the client list
	 *
	 * @version 1.2
	 * @param E_RENDERMODE $rendermode
	 *
	 */
	public function show($rendermode="FULLPAGE")
	{
		$data = array();
		if ($this->getSessionData(E_SESSION_ITEM::JS_ENABLED) == false)
		{
			// load table data immediatly since the ajax way will not work without js
			$edit			= $this->hasPermission(E_PERMISSIONS::ROOT_CLIENT_EDIT);
			$delete			= $this->hasPermission(E_PERMISSIONS::ROOT_CLIENT_DELETE);
			$modelResult 	= $this->client_model->datatable( $this->client_id, T_Client::get_table_columns(), $edit, $delete );
			$data 			= json_decode($modelResult->getData())->data;
		}
	
		write2Debugfile(self::DEBUG_FILENAME, " - admin/clients/show\n".print_r($data, true), false);
	
		
		$this->setViewData("table_data", $data);
		$this->setViewData("table_columns", T_Client::get_table_columns() );
		
		$this->render('root/client/client_list', $rendermode);
	}
}