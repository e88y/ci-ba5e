<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Users controller
 *
 * @author Marco Eberhardt
 * @category users
 * @package application\controllers\admin\users
 * @version 1.3
 */
class Users extends BASE_Controller 
{
	const DEBUG_FILENAME = "users.log";
	
	/**
	 * array containing all available roles
	 * @var array
	 */
	private $available_roles 		= null;
	
	/**
	 * array containing all available countries
	 * @var array
	 */
	private $available_countries	= null;
	
	/**
	 * Constructor for the users controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		$this->load->library("value_objects/T_User.php");
		$this->load->model("user_model");
		$this->load->model("role_model");
		
    	$this->javascript		= array("users.js");
    	$this->hasBreadcrump 	= true;
    	
    	$this->addPlugins(
    		E_PLUGIN::DATATABLES,
    		E_PLUGIN::FILE_INPUT,
    		E_PLUGIN::BS_TOGGLE,
    		E_PLUGIN::SELECT2
    	);
    	 
    	$this->available_roles 		= $this->role_model->load($this->client_id)->data;;
		$this->available_countries	= $this->app_model->getCountries($this->loaded_language)->data;	// db-cache is on for this one
		
		write2Debugfile(self::DEBUG_FILENAME, "admin/users\n", false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index() {
		self::show();
	}
	
	/**
	 * Delete a user. 
	 *
	 * @version 1.0
	 *
	 * @param string $user_id >> user, you want to delete
	 * @param bool $confirmed >> if true, the user has confirmed this action
	 * @param E_RENDERMODE $rendermode
	 *
	 * @return bool >> return true if the user has been removed
	 */
	public function remove($user_id="", $confirmed=0, $rendermode="FULLPAGE")
	{
		if ($this->input->post("user_id") != "" && $user_id == ""){
			$user_id = $this->input->post("user_id");
		}
		if ($this->input->post("confirmed") == true && $confirmed == 0){
			$confirmed = 1;
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($user_id == null || $user_id == "")
		{
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result 		= new BASE_Result(false);	// action needs confirmation first
		$removed		= false;
		$result_user	= $this->user_model->load( $this->client_id, $user_id );
	
		write2Debugfile(self::DEBUG_FILENAME, "remove user [$user_id] user-".print_r($result_user, true), false);
		if ($result_user->data != null && $result_user->getError() == "")
		{
			$this->breadcrump = $result_user->data->username;
	
			if ($confirmed == 1){
				$result	= $this->user_model->remove($this->client_id, $user_id, $this->getSessionData(E_SESSION_ITEM::USERNAME));
				$result = new BASE_Result(true);
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "user[$user_id] NOT found", true);
			$this->breadcrump = lang("entry_not_found");
			$result = new BASE_Result(false, lang("msg_user_not_found"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
	
		if ($result->data == true && $result->error == "")
		{
			$removed = true;
			$this->setViewSuccess(lang("user_has_been_deleted"));
		}
		$this->setViewData("removed", $removed);
		$this->setViewData("confirmed", $confirmed);
		$this->setViewData("user", $result_user->data);
	
		$this->render('admin/user/user_delete', $rendermode);
		return $removed;
	}
	
	/**
	 * render the user_form to create new user
	 * @version 1.0
	 * 
	 * @param E_RENDERMODE $rendermode
	 */
	public function create($rendermode="FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->breadcrump = "";
	
		if (is_array($this->input->post()) && $this->input->post("save") == 1 )
		{	// only if we have a post, we try to save
			// note that the save method overwrites the user-viewdata and user_roles-viewdata
			self::save(false);
		}
		else
		{
			$this->setViewData("user", array());
			$this->setViewData("user_roles", array() );
		}
	
		write2Debugfile(self::DEBUG_FILENAME, "create new user\n".print_r($this->data, true));
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_countries", $this->available_countries);
		$this->setViewData("available_languages", $this->available_languages);
		$this->setViewData("available_roles", $this->available_roles );
	
		$this->render('admin/user/user_form', $rendermode);
	}
	
	
	/**
	 * Ajax data-source for the datatable
	 * JSON-Rendermode
	 *
	 * @version 1.0
	 */
	public function datatable()
	{
		$result = $this->user_model->datatable( $this->client_id, $this->user_id, T_User::get_table_columns(), $this->hasPermission(E_PERMISSIONS::USER_EDIT), $this->hasPermission(E_PERMISSIONS::USER_DELETE));
		$result->data = json_decode($result->data);	// because the render method will encode it again
			
		write2Debugfile(self::DEBUG_FILENAME, "\nuser datatable\n".print_r($result, true));
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}
	
	/**
	 * load user data, set view data and render user form
	 *
	 * @param string $user_id 			>> user identifier; can be passed by parameter and also in post
	 * @param E_RENDERMODE $rendermode 	>> render mode
	 */
	public function edit($user_id=null, $rendermode="FULLPAGE")
	{
		if ($this->input->post("user_id") != ""){
			$user_id = $this->input->post("user_id");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($user_id == null || $user_id == "")
		{
			$this->render(E_ERROR_VIEW::INVALID_PARAMS, $rendermode);
			return;
		}
			
		write2Debugfile(self::DEBUG_FILENAME, "edit user client_id[".$this->client_id."] user_id[$user_id]", false);
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result_user 		= $this->user_model->load( $this->client_id, $user_id);
	
		if ($result_user->data != null && $result_user->getError() == "")
		{
			$this->breadcrump = $result_user->data->username;
			
			if (is_array($this->input->post()) && $this->input->post("save") == 1 )
			{	// if we have a post, we try to save
				// note that the save method sets the user-viewdata and user_roles-viewdata
				self::save(true);
			}
			else
			{
				$user_roles = $this->user_model->loadRoles($this->client_id, $user_id);
	
				$this->setViewData("user_roles", $user_roles->data );
				$this->setViewData("user", $result_user->data );
			}
		}
		else {
			write2Debugfile(self::DEBUG_FILENAME, "user[$user_id] NOT found", true);
			$this->setViewError(lang("msg_entry_not_found"));
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_countries", $this->available_countries);
		$this->setViewData("available_languages", $this->available_languages);
		$this->setViewData("available_roles", $this->available_roles);
		
		$this->render('admin/user/user_form', $rendermode);
	}
	
	/**
	 * saves a user after input validation and sets some viewdata
	 * Note: The permission check is made via validation callback
	 * 
	 * @access private
	 * @version 1.0
	 *
	 * @param bool $edit 	>> create or update action
	 * @return bool 		>> returns the saved state
	 */
	private function save($edit)
	{
		write2Debugfile(self::DEBUG_FILENAME, "\nsave user\npost-".print_r($this->input->post(), true), true);
		$saved	= false;
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Set validation rules
		if ($this->input->post("user_id") == "" || $this->input->post("password") != "" || $edit == false)
		{	// set password rules
			$this->form_validation->set_rules('password', 'lang:password', 'trim|required|matches[password_repeat]|min_length[8]');
			$this->form_validation->set_rules('password_repeat', 'lang:password_repeat', 'trim');
		}
	
		if ($this->input->post("username_orig") != $this->input->post("username") || $edit === false)
		{	// validate the username if it has been changed or when creating new ones
			$this->form_validation->set_rules('username', 'lang:username', 'required|min_length[5]|max_length[50]|validate_is_unique['.$this->input->post("username_orig").','.TBL_USER.',username,'.lang("user_already_exist").']');
		}
	
		$this->form_validation->set_rules('role[]', 		'lang:roles', 'required|min_length[1]');
		//$this->form_validation->set_rules('country', 		'lang:country', 'required|exact_length[2]|validate_existance['.TBL_COUNTRIES.', country_code, '.lang('unknown_country').']');
		//$this->form_validation->set_rules('locale', 		'lang:language', 'required|exact_length[2]|validate_existance['.TBL_LOCALES.', locale_code, '.lang("unknown_locale").']');
		
		$this->form_validation->set_rules('email', 			'lang:email', 'trim|required|valid_email|max_length[255]');
		$this->form_validation->set_rules('phone', 			'lang:phone', 'trim|max_length[100]');
		$this->form_validation->set_rules('firstname', 		'lang:firstname', 'trim|required|min_length[1]|max_length[255]');
		$this->form_validation->set_rules('lastname', 		'lang:lastname', 'trim|required|min_length[1]|max_length[255]');
		$this->form_validation->set_rules('locked', 		'lang:locked', 'max_length[1]');
		$this->form_validation->set_rules('deleted', 		'lang:deleted', 'trim|max_length[1]');
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$locked_at = $this->input->post("locked_at");
		if ($this->input->post("locked") == 1 && $this->input->post("locked_at") == ""){
			$locked_at = time();
		}
			
		$locked_by = $this->input->post("locked_by");
		if ($this->input->post("locked") == "1" && $this->input->post("locked_by") == ""){
			$locked_by = $this->getSessionData(E_SESSION_ITEM::USER_ID);
		}
			
		$deleted_at = $this->input->post("deleted_at");
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_at") == ""){
			$deleted_at = time();
		}
			
		$deleted_by = $this->input->post("deleted_by");
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_by") == ""){
			$deleted_by = $this->getSessionData(E_SESSION_ITEM::USER_ID);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->input->post("user_id") == ""){
			$user_id = BASE_Model::generateUID(TBL_USER, "user_id", "", false, 25);
		}else{
			$user_id = $this->input->post("user_id", true);
		}
		
		$data = array(
			"client_id" => $this->client_id,
			"user_id" => $user_id,
			"username" => $this->input->post("username", true),
			"email" => $this->input->post("email", true),
			"phone" => $this->input->post("phone", true),
			"firstname" => $this->input->post("firstname", true),
			"lastname" => $this->input->post("lastname", true),
			"zipcode" => $this->input->post("zipcode", true),
			"country" => $this->input->post("country", true),
			"language" => $this->input->post("locale", true),
			"locked" => ($this->input->post("locked") == "1" ? 1:0),
			"locked_at" => $locked_at,
			"locked_by" => $locked_by,
			"deleted" => ($this->input->post("deleted") == "1" ? 1:0),
			"deleted_at" => $deleted_at,
			"deleted_by" => $deleted_by
		);
		
		if ($this->input->post("created_at") == "" && $edit == false){
			$data["created_at"] = time();
		}
			
		if ($this->input->post("password") != ""){
			$data["password"] = $this->input->post("password", true);
		}
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run() )
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
			write2Debugfile(self::DEBUG_FILENAME, "data-".print_r($data, true)."\nFILES-".print_r($_FILES, true), true);
	
			if ($edit == true){
				$result = $this->user_model->update($this->client_id, $user_id, $data, $this->input->post("role"));
			}
			else
			{
				$data["user_id"] 	= $user_id;
				$data["salt"]		= BASE_Model::generateUID(TBL_USER, "salt", "", false, 8);
				$data["password"] 	= hash("sha256", $data["salt"] . APP_SALT_SEPERATOR . $data["password"]);
	
				$result = $this->user_model->create($this->client_id, $data, $this->input->post("role") );
	
				$data["password"] 	= $this->input->post("password"); // reset to plain text value
	
				if ($result->error != ""){
					$data["user_id"] = "";
				}
			}
			
		}
		else
		{
			$result = new BASE_Result(null, validation_errors(), $this->form_validation->error_array() );
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n".validation_errors(), true);
		}
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		if ($result->error == "")
		{
			$this->setViewSuccess(lang("user_has_been_saved"));
			$saved = true;
		}
		$this->setViewData("user", $data);
		$this->setViewData("user_roles", $this->input->post("role"));
		$this->setViewData("saved", $saved);
			
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
			
	
		if ($this->getSessionData(E_SESSION_ITEM::USER_ID) == $user_id)
		{
			// @todo update session data, if the own user has been edited
		}
		return $saved;
	}
	
	/**
	 * Render the user list
	 * @version 1.0
	 * 
	 * @param E_RENDERMODE $rendermode
	 */
	public function show($rendermode="FULLPAGE")
	{
		$data 			= array();
		
		if ($this->getSessionData(E_SESSION_ITEM::JS_ENABLED) == E_ENABLED::NO)
		{
			// load table data immediatly since the ajax way will not work without js
			$edit			= $this->hasPermission(E_PERMISSIONS::USER_EDIT);
			$delete			= $this->hasPermission(E_PERMISSIONS::USER_DELETE);

			$modelResult 	= $this->user_model->datatable( $this->client_id, $this->user_id, T_User::get_table_columns(), $edit, $delete);
			$data 			= json_decode($modelResult->getData())->data;
		}
		
		$this->setViewData("table_data", $data);
		$this->setViewData("table_columns", T_User::get_table_columns() );
		
		$this->render('admin/user/user_list', $rendermode);
	}
	
	/**
	 * Unlock a user
	 * JSON-Rendermode 
	 * 
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 */
	public function unlock()
	{
		$data = array(
			"locked"=>0,
			"locked_at" => null,
			"locked_by" => null,
			"locked_reason" => null
		);
		$result = $this->user_model->update($this->client_id, $this->input->post("user_id", true), $data);
		
		$this->setData($result);
		$this->render('admin/user/user_list', E_RENDERMODE::JSON);
	}
}