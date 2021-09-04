<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Roles controller
 * 
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\admin\roles
 * @version 1.3
 */
class Roles extends BASE_Controller
{
	const DEBUG_FILENAME = "roles.log";
	
	/**
	 * array containing all rights available
	 *
	 * @var array
	 */
	private $available_rights = array();
	

	/**
	 * Constructor for the roles controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		$this->load->library("value_objects/T_Role.php");
		$this->load->model("role_model");
		
		$this->javascript = array("roles.js");
		$this->addPlugins(
			E_PLUGIN::DATATABLES, 
			E_PLUGIN::BS_TOGGLE);
		
		$this->available_rights = $this->role_model->loadRights($this->client_id)->data;
		
		write2Debugfile(self::DEBUG_FILENAME, "admin/roles", false);
	}

	/**
	 * Default entry point.
	 * leads to the show method
	 */
	public function index()
	{
		self::show();
	}

	/**
	 * Render view to create new role
	 *
	 * @version 1.0
	 * @param E_RENDERMODE $rendermode        	
	 */
	public function create($rendermode = "FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode"))))
		{
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->breadcrump = "";
		
		if (is_array($this->input->post()) && $this->input->post("save") == 1)
		{ // only if we have a post, we try to save
		  // note that the save method overwrites the user-viewdata and user_roles-viewdata
			self::save(false);
		}
		else
		{
			$this->setViewData("role", array());
			$this->setViewData("role_rights", array());
		}
		
		write2Debugfile(self::DEBUG_FILENAME, "create new role\n" . print_r($this->data, true));
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_rights", $this->available_rights);
		
		$this->render('admin/role/role_form', $rendermode);
	}

	/**
	 * Ajax data-source for the datatable
	 * Rendermode is always JSON-DATA
	 *
	 * @version 1.0
	 */
	public function datatable()
	{
		$edit = $this->hasPermission(E_PERMISSIONS::ROLE_EDIT);
		$delete = $this->hasPermission(E_PERMISSIONS::ROLE_DELETE);
		
		$result = $this->role_model->datatable($this->client_id, T_Role::get_table_columns(), $edit, $delete);
		$result->data = json_decode($result->data); // because the render method will encode it again
		
		write2Debugfile(self::DEBUG_FILENAME, "\nuser datatable edit[$edit] del[$delete] columns-" . print_r(T_Role::get_table_columns(), true) . "\n" . print_r($result, true));
		
		$this->setData($result);
		$this->render(null, E_RENDERMODE::JSON_DATA);
	}

	/**
	 * load role data and set view data and render role form
	 *
	 * @version 1.0
	 * 
	 * @param string $role_id 			>> role identifier
	 * @param E_RENDERMODE $rendermode 	>>
	 */
	public function edit($role_id=null, $rendermode="FULLPAGE")
	{
		if ($this->input->post("role_id") != "" && $role_id == null)
		{
			$role_id = $this->input->post("role_id");
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode"))))
		{
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($role_id == null || $role_id == "")
		{
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		//$role_id 		= decrypt_string($role_id);
		$result_role 	= $this->role_model->load($this->client_id, $role_id);
		
		write2Debugfile(self::DEBUG_FILENAME, "edit role client_id[" . $this->client_id . "] role_id[$role_id] -" . print_r($result_role, true));
		
		if (count($result_role->getData()) == 1 && $result_role->getError() == "")
		{
			$name = ($result_role->data[0]->is_static == 1 ? lang($result_role->data[0]->role_name) : $result_role->data[0]->role_name);
			$this->breadcrump = $name;
			
			if (is_array($this->input->post()) && $this->input->post("save") == 1)
			{ // if we have a post, we try to save
			  // note that the save method sets the role-viewdata and role_rights-viewdata
				self::save(true);
			}
			else
			{
				$role_rights = $this->role_model->loadRoleRights($this->client_id, $role_id);
				
				$this->setViewData("role_rights", $role_rights->getData());
				$this->setViewData("role", $result_role->data[0]);
			}
		}
		else
		{
			write2Debugfile(self::DEBUG_FILENAME, "role[$role_id] NOT found", true);
			$this->breadcrump = lang("msg_entry_not_found");
			$this->setViewError(lang("msg_entry_not_found"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->setViewData("available_rights", $this->available_rights);
		$this->render('admin/role/role_form', $rendermode);
	}

	/**
	 * Deletes a role.
	 *
	 * @version 1.0
	 *         
	 * @param string $role_id
	 *        	>> role id, you want to delete
	 * @param bool $confirmed
	 *        	>> if true, the user has already confirmed this action
	 * @param E_RENDERMODE $rendermode        	
	 *
	 * @return bool >> true if the role has been removed
	 */
	public function remove($role_id = "", $confirmed = 0, $rendermode = "FULLPAGE")
	{
		if ($this->input->post("role_id") != "" && $role_id == "")
		{
			$role_id = $this->input->post("role_id");
		}
		if ($this->input->post("confirmed") == true && $confirmed == 0)
		{
			$confirmed = 1;
		}
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode"))))
		{
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		//$role_id = decrypt_string($role_id);
		if ($role_id == null || $role_id == "")
		{
			$this->render('errors/error_invalid_parameter', $rendermode);
			return;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$result = new BASE_Result(false); // action needs confirmation first
		$removed = false;
		$result_role = $this->role_model->load($this->client_id, $role_id);
		
		write2Debugfile(self::DEBUG_FILENAME, "remove role [$role_id] -" . print_r($result_role, true), false);
		
		if (count($result_role->getData()) == 1 && $result_role->getError() == "")
		{
			$name = ($result_role->data[0]->is_static == 1 ? lang($result_role->data[0]->role_name) : $result_role->data[0]->role_name);
			$this->breadcrump = $name;
			
			if ($confirmed == 1)
			{
				$result = $this->role_model->remove($this->client_id, $role_id, $this->getSessionData(E_SESSION_ITEM::USERNAME));
			}
		}
		else
		{
			$this->breadcrump = lang("msg_entry_not_found");
			$result = new BASE_Result(false, lang("msg_role_not_found"));
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		
		if ($result->data == true && $result->error == "")
		{
			$removed = true;
			$this->setViewSuccess(lang("role_has_been_deleted"));
			// self::show($rendermode);
			// return ;
		}
		$this->setViewData("removed", $removed);
		$this->setViewData("confirmed", $confirmed);
		$this->setViewData("role", $result_role->data);
		
		$this->render('admin/role/role_delete', $rendermode);
		return $removed;
	}

	/**
	 * Ajax-Method to delete a role
	 *
	 * @param string $role_id        	
	 */
	public function remove_ajax($role_id)
	{
		self::remove($role_id, 1, E_RENDERMODE::JSON);
	}

	/**
	 * Saves a role after input validation and sets the viewdata
	 *
	 * @version 1.0
	 *         
	 * @param bool $edit
	 *        	>> create or update action
	 * @return boolean >> returns the saved state
	 */
	private function save($edit)
	{
		write2Debugfile(self::DEBUG_FILENAME, "save role\n" . print_r($this->input->post(), true), false);
		
		//$post = $this->input->post(NULL, TRUE);
		$saved = false;
		
		if ($this->input->post("role_id") != "" && $edit == false)
		{	// correct wrong save mode
			$edit = true;
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set validation rules
		if ($edit){
			$this->form_validation->set_rules('name_orig', 'lang:role_name', 'trim|required|min_length[4]|max_length[50]');
		}
		else{
			$this->form_validation->set_rules('role_name', 'lang:role_name', 'trim|required|min_length[4]|max_length[50]|validate_is_unique[' . $this->input->post("name_orig") . ',' . TBL_ROLES . ',role_name,' . lang("role_already_exist") . ']');
		}
		
		$this->form_validation->set_rules('right[]', 'lang:rights', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('role_desc', 'lang:role_desc', 'trim|max_length[255]');
		$this->form_validation->set_rules('deleted', 'lang:deleted', 'trim|max_length[1]');
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$deleted_at = $this->input->post("deleted_at", TRUE);
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_at") == "")
		{
			$deleted_at = time();
		}
		
		$deleted_by = $this->input->post("deleted_by", TRUE);
		if ($this->input->post("deleted") == "1" && $this->input->post("deleted_by") == "")
		{
			$deleted_by = $this->getSessionData(E_SESSION_ITEM::USER_ID);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->input->post("role_id") == "")
		{
			$role_id = BASE_Model::generateUID(TBL_ROLES, "role_id", $this->client_id . "_", false, 20);
		}
		else
		{
			//$role_id = decrypt_string($this->input->post("role_id", TRUE));
			$role_id = $this->input->post("role_id", TRUE);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$data = array(
			"client_id" => $this->client_id,
			"role_id" => $role_id,
			//"is_static" => ($this->input->post("is_static") == "1" ? 1 : 0),
			"deleted" => ($this->input->post("deleted") == "1" ? 1 : 0),
			"deleted_at" => $deleted_at,
			"deleted_by" => $deleted_by
		);
		
		if ($this->input->post("static") == 0)
		{
			$data["role_name"] = $this->input->post("role_name", TRUE);
			$data["role_desc"] = $this->input->post("role_desc", TRUE);
		}
		
		/*
		 * if ($this->input->post("created_at") == "" && $edit == false){
		 * $data["created_at"] = time();
		 * }
		 */
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run())
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed edit[$edit]...", true);
			
			if ($edit == true)
			{
				$result = $this->role_model->update($this->client_id, $role_id, $data, $this->input->post("right", true));
			}
			else
			{
				$result = $this->role_model->create($this->client_id, $data, $this->input->post("right", true));
			}
		}
		else
		{
			$result = new BASE_Result(null, validation_errors(), $this->form_validation->error_array());
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n" . validation_errors(), true);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		if ($result->error == "")
		{
			$this->setViewSuccess(lang("role_has_been_saved"));
			$saved = true;
		}
		else
		{
			if ($edit == false)
			{
				$data["role_id"] = "";
			}
		}
		$this->setViewData("role", $data); // fill the role with the given post data so the view can populate a filled out form
		$this->setViewData("role_rights", $this->input->post("right"));
		$this->setViewData("saved", $saved);
		
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n" . print_r($this->data, true));
		
		return $saved;
	}

	/**
	 * render the roles list
	 *
	 * @version 1.0
	 *         
	 * @param E_RENDERMODE $rendermode        	
	 */
	public function show($rendermode = "FULLPAGE")
	{
		$data = array();
		
		if ($this->getSessionData(E_SESSION_ITEM::JS_ENABLED) == false)
		{
			// load table data immediatly since the ajax way will not work without js
			$edit 	= $this->hasPermission(E_PERMISSIONS::ROLE_EDIT);
			$delete = $this->hasPermission(E_PERMISSIONS::ROLE_DELETE);
			
			$modelResult = $this->role_model->datatable($this->client_id, T_Role::get_table_columns(), $edit, $delete);
			$data = json_decode($modelResult->getData())->data;
		}
		
		write2Debugfile(self::DEBUG_FILENAME, " - admin/roles/show\n" . print_r($data, true), false);
		
		$this->setViewData("table_data", $data);
		$this->setViewData("table_columns", T_Role::get_table_columns());
		
		$this->render('admin/role/role_list', $rendermode);
	}
}