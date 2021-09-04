<?php
/**
 * User model
 *
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\user_model
 * @version 1.1
 */
class User_model extends BASE_Model 
{
	const DEBUG_FILENAME = "user_model.log";
	
	/**
	 * If a user enters an incorrect password x times, the system locks the user account.
	 * This const defines, how long (in minutes) the user stays locked, till he can retry to login again. 
	 * When this is set to NULL, no login is possible anymore until an administrator unlock the account.
	 * 
	 * @var int
	 */
	const MINUTES_TO_ALLOW_LOCKED_USER_TO_LOGIN = 15;
	
	/**
	 * Constructor for the role model
	 */
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "User_model", true);
	}

	/**
	 * When the user has been locked because of too many failed logins, he may can retry after a certain time.
	 * 
	 * This method checks the dependencies to allow login for locked users and returns BOOL
	 * 	- account must be activated
	 *  - lock reason must be TOO_MANY_LOGIN_FAILS
	 *  - MINUTES_TO_ALLOW_LOCKED_USER_TO_LOGIN must not be NULL
	 * 
	 * @author Marco Eberhardt
	 * @version 1.0
	 * 
	 * @param int $locked
	 * @param int $locked_at
	 * @param string $locked_reason
	 * @param int $activated
	 * 
	 * @return boolean
	 */
	private function allow_locked_user($locked, $locked_at, $locked_reason, $activated)
	{
		if ($locked == 1 && $activated == 1 && $locked_reason === E_SYSTEM_LOCK_REASONS::TOO_MANY_LOGIN_FAILS && self::MINUTES_TO_ALLOW_LOCKED_USER_TO_LOGIN !== null)
		{
			$locked_till = ($locked_at + (MINUTES_TO_ALLOW_LOCKED_USER_TO_LOGIN * 60) );
			if (time() >= $locked_till){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * try to authenticate with the user credentials
	 * 
	 * @param string $username		>> loginname
	 * @param string $password		>> password
	 * 
	 * @return BASE_Result 			>> contains the userdata-array on success
	 * 
	 */
	function authenticate($username, $password)
	{
		$this->db
		->select(TBL_USER.'.*, '.TBL_USER.".created_at AS user_created_at, ".TBL_CLIENTS.".*, ".TBL_CLIENTS.".created_at AS client_created_at ")
		->from( TBL_USER )
		->join( TBL_CLIENTS, TBL_CLIENTS.".client_id = ".TBL_USER.".client_id AND ".TBL_CLIENTS.".deleted = '0' ", "inner")
		->where('username', $username)
		->where(TBL_USER.'.deleted', "0")
		->limit(1);
		
		$query = $this->db->get();
		
		write2Debugfile(self::DEBUG_FILENAME, "authenticate:\n".$this->lastQuery()."\n".print_r($query->result_array(), true), false);
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
		
		$numRows	= $query->num_rows();
		$data		= array();
		$error 		= lang("user_not_found"); // default error
		
		foreach ($query->result_object() as $row)
		{
			if ($row->locked == 0 && $row->activated == 1 || $this->allow_locked_user($row->locked, $row->locked_at, $row->locked_reason, $row->activated) )
			{
				$login_attempts 		= $row->failed_logins;
				
				$user_password 			= $row->password;
				$user_salt 				= $row->salt;
				
				if (hash("sha256", $user_salt . APP_SALT_SEPERATOR . $password) == $user_password)
				{
					$error 	= "";
					
					$data 	= array(
						E_SESSION_ITEM::SESSION_ID => session_id(),
						E_SESSION_ITEM::IS_ROOT => ($row->client_id == $this->config->item('root_client_id') ? true:false ),
						E_SESSION_ITEM::JS_ENABLED => $_SESSION[E_SESSION_ITEM::JS_ENABLED],
						E_SESSION_ITEM::LOGGED_IN => true,
						E_SESSION_ITEM::LOGGED_IN_AT => time(),
							
						E_SESSION_ITEM::CLIENT_ID => $row->client_id,
						E_SESSION_ITEM::CLIENT_NAME => $row->client_name,
						E_SESSION_ITEM::CLIENT_DESC => $row->client_desc,
						E_SESSION_ITEM::CLIENT_EMAIL => $row->client_email,
						E_SESSION_ITEM::CLIENT_FAX => $row->client_fax,
						E_SESSION_ITEM::CLIENT_PHONE => $row->client_phone,
						E_SESSION_ITEM::CLIENT_STREET => $row->client_street,
						E_SESSION_ITEM::CLIENT_HOUSE_NR => $row->client_house_nr,
						E_SESSION_ITEM::CLIENT_ZIPCODE => $row->client_zipcode,
						E_SESSION_ITEM::CLIENT_LOCATION => $row->client_location,
						E_SESSION_ITEM::CLIENT_COUNTRY => $row->client_country,
						E_SESSION_ITEM::CLIENT_LOGO => $row->client_logo,
						E_SESSION_ITEM::CLIENT_CREATED_AT => $row->client_created_at,
							
						E_SESSION_ITEM::USER_ID => $row->user_id,
						E_SESSION_ITEM::USERNAME => $row->username,
						E_SESSION_ITEM::USER_EMAIL => $row->email,
						E_SESSION_ITEM::USER_PHONE => $row->phone,
						E_SESSION_ITEM::USER_FIRSTNAME => $row->firstname,
						E_SESSION_ITEM::USER_LASTNAME => $row->lastname,
						E_SESSION_ITEM::USER_STREET => $row->street,
						E_SESSION_ITEM::USER_HOUSE_NUMBER => $row->house_number,
						E_SESSION_ITEM::USER_ZIPCODE => $row->zipcode,
						E_SESSION_ITEM::USER_LOCATION => $row->location,
						E_SESSION_ITEM::USER_COUNTRY => $row->country,
						E_SESSION_ITEM::USER_LANGUAGE => $row->language,
						E_SESSION_ITEM::CREATED_AT => $row->user_created_at,
						E_SESSION_ITEM::LAST_LOGIN => $row->last_login,

						E_SESSION_ITEM::USER_PERMISSIONS => array(),
						E_SESSION_ITEM::USER_MENU => array(),
					);
					
					$permissions = self::loadPermissions($row->client_id, $row->user_id);
					if ($permissions->getError() == ""){
						$data[E_SESSION_ITEM::USER_PERMISSIONS] = $permissions->getData();
					}
					
					$menu = self::loadMenu($row->client_id, $row->user_id, $permissions->getData());
					if ($menu->getError() == ""){
						$data[E_SESSION_ITEM::USER_MENU]= $menu->getData();
					}
					
					// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
					self::post_login_actions($row->user_id);
				}
				else
				{	// Handle wrong password...
					
					if ( (1+$login_attempts) >= $this->config->item("login_attempts_till_lock"))
					{	
						// Lock the user
						$result = $this->BASE_Update(TBL_USER, array("locked"=>1, "locked_at"=>time(), "locked_by"=>"system - too many failed login attempts"), array("user_id"=>$row->user_id));
						
						if ($result->getError() != ""){
							log_message(E_LOGLEVEL::ERROR, " - error lock user ".$row->user_id.":\n".$result->getError());
							// $error = $result->getError();
						}
						
						$error	= lang("msg_login_failed");
						//$error	= lang("msg_user_locked");
						$data	= array();
					}
					else 
					{ 
						// increase failed login attempts  
						$result = $this->BASE_Update(TBL_USER, array("failed_logins"=>(1+$login_attempts) ), array("user_id"=>$row->user_id));
						
						if ($result->getError() != ""){
							log_message(E_LOGLEVEL::ERROR, " - error increase failed login counter ".$row->user_id.":\n".$result->getError());
							// $error = $result->getError();
						}
						
						//$error	= lang("msg_user_wrong_password");
						$error	= lang("msg_login_failed");
						$data	= array();
					}
				}
			}
			else 
			{
				if ($row->locked == 1)
				{
					$error	= sprintf(lang("msg_user_locked"), format_timestamp2datetime($row->locked_at) );
					$data	= array();
				}
				else if ($row->activated == 0)
				{
					$error	= sprintf(lang("msg_user_not_activated_yet") );
					$data	= array();
				}
			}
		}
		$result = new BASE_Result($data, $error, "", ($error == '' ? E_STATUS_CODE::SUCCESS : E_STATUS_CODE::ERROR) );
		
		write2Debugfile(self::DEBUG_FILENAME, "auth result error[$error] ".print_r($result, true));
		
		return $result;
	}
	
	/**
	 * creates a new user entry and stores it's assigned roles and entities.
	 * Uses transactions
	 *
	 * @version 1.0
	 *
	 * @param string $client_id	>> client identifier
	 * @param array $data		>> user data
	 * @param array $roles		>> user assigned roles
	 * @param array $entities	>> user assigned entities
	 *
	 * @return BASE_Result
	 */
	function create($client_id, $data, $roles, $entities=null)
	{
		write2Debugfile(self::DEBUG_FILENAME, "client_id[".$client_id."] create user-".print_r($data, true));
	
		$data["client_id"] = $client_id;
	
		if ($data["user_id"] == ""){
			$data["user_id"] 	= BASE_Model::generateUID(TBL_USER, "user_id", "", false, 25);
		}
	
		$address = array(
			"client_id"=>$data["client_id"] ,
			"user_id"=>$data["user_id"],
			"firstname"=>$data["firstname"],
			"lastname"=>$data["lastname"],
			"country"=>$data["country"],
		);
	
	
		$queries = array(
			$this->getInsertString(TBL_USER, $data)
		);
	
		if (is_array($roles) && count($roles) > 0)
		{
			foreach ($roles as $index=>$role_id)
			{
				$queries[] = $this->getInsertString(TBL_USER_ROLES,
					array(
						"client_id"=>$client_id,
						"user_id"=>$data["user_id"],
						"role_id"=>$role_id
					)
				);
			}
		}
		
		$return = $this->BASE_Transaction($queries);
	
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".implode("\n", $queries)."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * load all users for a given client ID, utilizing the datatables library
	 *
	 * @param string $client_id		>> client identifier
	 * @param array $columns		>> array with the views column definition
	 * @param bool $btnEdit 		>> add edit button
	 * @param bool $btnDel 			>> add delete button
	 * @param bool $includeDeleted	>> show deleted entries
	 *
	 * @return BASE_Result >> containing an array or null
	 */
	function datatable($client_id, $user_id, $columns, $btnEdit=false, $btnDel=false, $includeDeleted=false)
	{
		write2Debugfile(self::DEBUG_FILENAME, "\ndatatable\n\n");
		
		$this->load->library('Datatables');
		$this->load->helper('datatable');
	
		$fields = prepare_fields($columns, $this->listFields(TBL_USER), array() );

			
		$this->datatables->select(TBL_USER.".user_id, activated,activated_at, ".$fields );
		$this->datatables->from(TBL_USER);
		$this->datatables->where(TBL_USER.".client_id", $client_id);

		$this->datatables->edit_column('username', '$1', "callback_build_buttons(user_id, username, admin, users, $btnEdit, $btnDel, 0, 0, 1)");
		$this->datatables->edit_column('locked', '$1' , 'callback_locked(user_id,locked)');
		$this->datatables->edit_column('activated', '$1' , 'callback_activated(user_id,activated,activated_at)');
		$this->datatables->edit_column('deleted', '$1' , 'callback_deleted(user_id, deleted) ');
		$this->datatables->edit_column('last_login', '$1' , 'format_timestamp2datetime(last_login) ');
		$this->datatables->edit_column('created_at', '$1' , 'format_timestamp2datetime(created_at) ');
	
		if ($includeDeleted === false){
			$this->datatables->where(TBL_USER.".deleted", "0");
		}
	
		$result 		= $this->datatables->generate();
		$result_json 	= json_decode($result);
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".print_r($this->db->queries, true)."\n\n".print_r(json_decode($result), true));
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * Load data for a given user ID
	 *
	 * @version 1.0
	 * @param string $client_id
	 * @param string $user_id
	 * @param bool $includeDeleted
	 *
	 * @return BASE_Result >> containing the userdata or null
	 */
	function load($client_id, $user_id=null, $includeDeleted=false)
	{
		$fields = array("client_id", "user_id", "username", "email", "phone", "firstname", "lastname", "street", "house_number", "zipcode",
			"location", "country", "language", "created_at", "locked", "locked_at", "locked_by", "deleted", "deleted_at", "deleted_by", 
			"failed_logins", "last_login"
		);
	
		$where = array(
			"client_id"=>$client_id
		);
	
		if ($user_id != null){
			$where["user_id"] = $user_id;
		}
		if ($includeDeleted === false){
			$where["deleted"] = "0";
		}
	
		$return = $this->BASE_Select(TBL_USER, $where, $fields, array(), ($user_id != null ? "1":""));
	
		write2Debugfile(self::DEBUG_FILENAME, "load client_id[$client_id] user_id[$user_id]\n".$this->lastQuery()."\n".print_r($return->data, true) );
		return $return;
	}
	
	/**
	 * load menu entries for the user and creates an assoc array to build the menu/sidemenu
	 *
	 * @param string $cient_id		>> client identifier
	 * @param string $user_id		>> user identifier
	 * @param array $permissions 	>> users permissions
	 *
	 * @return BASE_Result
	 */
	function loadMenu($client_id, $user_id, $permissions=null)
	{
		if ($permissions == null)
		{
			$permissions = self::loadPermissions($client_id, $user_id);
			if ($permissions->getError() != ""){
				return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
			}
	
			$permission = $permissions->getData();
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->db
		->select("*")->from(TBL_APP_MENU)
		->where("is_visible = '1' AND (right_id IS NULL OR right_id IN ('".implode("', '", array_keys((array)$permissions) ) ."') )")
		->order_by("menu_id_parent ASC, sort_order ASC");
	
		$query = $this->db->get();
	
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
			
		$data_obj		= $query->result_object();
		$menu_array		= array();
		$activeFound	= false;
	
		foreach ($data_obj as $key => $value)
		{
			$value->items	= array(); // create a new entry
			$value->active 	= "";
			if ($this->uri->uri_string == $value->menu_ref && $activeFound == false)
			{
				$value->active = "active";
				$activeFound = true;
			}
				
			if ($value->menu_id_parent == null)
			{
				if (!array_key_exists($value->menu_id, $menu_array))
				{
					$menu_array[$value->menu_id] 			= array();
					$menu_array[$value->menu_id]["items"] 	= array();
				}
				$menu_array[$value->menu_id]["menu_id"] 	= $value->menu_id;
				$menu_array[$value->menu_id]["menu_label"] 	= $value->menu_label;
				$menu_array[$value->menu_id]["menu_icon"] 	= $value->menu_icon;
				$menu_array[$value->menu_id]["menu_ref"] 	= $value->menu_ref;
				$menu_array[$value->menu_id]["right_id"] 	= $value->right_id;
				$menu_array[$value->menu_id]["active"]		= $value->active;
			}
			else
			{
				if (!array_key_exists($value->menu_id_parent, $menu_array)) {
					$menu_array[$value->menu_id_parent] 			= array();
					$menu_array[$value->menu_id_parent]["items"] 	= array();
				}
				$menu_array[$value->menu_id_parent]["items"][] 	= $value;
				$menu_array[$value->menu_id_parent]["active"]	= $value->active;
			}
		}
	
			
		$return = new BASE_Result($menu_array, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
	
		write2Debugfile(self::DEBUG_FILENAME, "loadMenu\n".$this->lastQuery()."\nreturn-".print_r($return, true)."\n\n");
		return $return;
	
	}
	
	/**
	 * Load the password history for the specified user
	 * 
	 * @author Marco Eberhardt
	 * @version 1.0
	 * 
	 * @param string $user_id
	 * @return BASE_Result
	 */
	function load_password_history($user_id)
	{
		$this->db
		->select(
			TBL_USER_PW_HISTORY.".user_id, ".
			TBL_USER_PW_HISTORY.".password, ".
			TBL_USER_PW_HISTORY.".created_at")
		->from(TBL_USER_PW_HISTORY)
		->where("user_id", $user_id);
			
		$query = $this->db->get();
		
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
		
		$history = array();
		foreach ($query->result_array() as $value) {
			$value["password"] = decrypt_string($value["password"]);
			$history[] = $value;
		}
			
		//write2Debugfile("load_password_history.log", $this->lastQuery());
		return new BASE_Result($history, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * Load the password blacklist.
	 * The blacklist includes also personal values from the user (username, address, mail) 
	 * 
	 * @author Marco Eberhardt
	 * @version 1.0
	 * 
	 * @param string $user_id
	 * @return BASE_Result
	 */
	function load_password_blacklist($user_id)
	{
		$result_blacklist 	= $this->BASE_Select(TBL_APP_PW_BLACKLIST)->data;
		$result_user		= $this->BASE_Select(TBL_USER, array("user_id"=>$user_id), array("username", "email", "firstname", "lastname", "street", "zipcode", "location"), array(), 1)->data;
		
		$blacklist 			= array_remap($result_blacklist, "entry_id", "blacklisted");
		foreach ((array)$result_user as $value) 
		{
			if (strlen($value) >= 4){	// We simply cannot add short words or single chars to the blacklist. otherwise the policy will reject nearly everything
				$blacklist[] = $value;
			}
		}
		return new BASE_Result($blacklist, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * Retrieve user permissions as assoc array
	 * 
	 * @param string $client_id >> client identifier
	 * @param string $user_id	>> user identifier
	 * 
	 * @return BASE_Result
	 */
	function loadPermissions($client_id, $user_id)
	{
		$return = new BASE_Result();

		$fields = array(
			TBL_ROLES_RIGHTS.".role_id",
			TBL_ROLES_RIGHTS.".right_id",
			TBL_RIGHTS.".right_name",
			TBL_RIGHTS.".right_desc",
			TBL_RIGHTS.".active"
		);
		
		if ($client_id != "" && $user_id != "")
		{
			$this->db
			->select( implode(", ", $fields) )
			->from(TBL_USER_ROLES)
			
			->join(	TBL_ROLES_RIGHTS, 
					TBL_ROLES_RIGHTS.'.client_id = '.$client_id.' AND '.
					TBL_ROLES_RIGHTS.'.role_id = '.TBL_USER_ROLES.'.role_id ', 
					'inner')
			
			->join(	TBL_RIGHTS, 
					TBL_RIGHTS.'.client_id = '.$client_id.' AND '.
					TBL_RIGHTS.'.right_id = '.TBL_ROLES_RIGHTS.'.right_id AND '. 
					TBL_RIGHTS.'.active = 1 ', 
					'inner')
			
			->join(	TBL_ROLES,
				TBL_ROLES.'.client_id = '.$client_id.' AND '.
				TBL_ROLES.'.role_id = '.TBL_USER_ROLES.'.role_id AND '.
				TBL_ROLES.'.deleted = 0 ',
				'inner')
				
			->where(TBL_USER_ROLES.'.user_id', $user_id)
			->group_by( implode(", ", $fields)); 
			
			$query = $this->db->get();
			
			if (! $query){
				return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
			}
			
			$numRows	= $query->num_rows();
			$data_obj	= $query->result_object();
			$data		= array();
			
			// add virtual permission "is_root" to identify root user
			if ($client_id == $this->config->item('root_client_id')){
				$data[E_PERMISSIONS::IS_ROOT] = (object)array("role_id"=>"none", "right_id"=>"is_virtual_permission", "right_name"=>"root_user", "active"=>1);
			}
			
			foreach ($data_obj as $value) {
				$data[$value->right_id] = $value;
			}
			
			$return = new BASE_Result($data, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
			write2Debugfile(self::DEBUG_FILENAME, "loadPermissions\n".$this->lastQuery()."\n".print_r($return, true));
			
		}
		return $return;
	}
	
	/**
	 * load user assigned roles
	 * 
	 * @param string $client_id		>> 
	 * @param string $user_id		>>
	 * @param bool $incPermissions	>> including permissions 
	 * @return BASE_Result
	 */
	function loadRoles($client_id, $user_id, $incPermissions=false)
	{
		$where 	= array(
			"client_id"=>$client_id,
			"user_id"=>$user_id
		);
		$result = $this->BASE_Select(TBL_USER_ROLES, $where, "role_id", array(), "", "", false, true);
		
		
		$return = array();
		
		$this->db->select("role_id")->from(TBL_USER_ROLES)->where("client_id", $client_id)->where("user_id", $user_id);
		
		$query = $this->db->get();
		if (! $query){
			return new BASE_Result(array(), $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
		
		foreach ($query->result_array() as $key => $value) {
			$return[] = $value["role_id"];
		}
		
		if ($incPermissions === true)
		{
		}
		write2Debugfile(self::DEBUG_FILENAME, "loadRoles\n".$this->lastQuery()."\n".print_r($return, true));
		return new BASE_Result($return, "", array(), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * update existing userdata, assigned roles 
	 * @version 1.0
	 *
	 * @param string $client_id
	 * @param string $user_id
	 * @param array $data
	 * @param array $roles
	 *
	 * @return BASE_Result
	 */
	function update($client_id, $user_id, $data, $roles=null)
	{
		write2Debugfile(self::DEBUG_FILENAME, "- update client_id[".$client_id."] user[$user_id] \nroles-".print_r($roles, true)."\nuserdata-".print_r($data, true));

		if (isset($data["password"]) && $data["password"] != "")
		{
			// convert the clear password to SHA256
            $data["salt"]		= BASE_Model::generateUID(TBL_USER, "salt", "", false, 8);
            $data["password"] 	= hash("sha256", $data["salt"] . APP_SALT_SEPERATOR . $data["password"]);
		}

		$queries = array($this->getUpdateString(TBL_USER, $data,  array("client_id" => $client_id, "user_id" => $user_id)));

		if (is_array($roles) && count($roles) > 0)
		{
			$queries[] = "DELETE FROM ".TBL_USER_ROLES." WHERE client_id = '".$this->db->escape_str($client_id)."' AND user_id = '".$this->db->escape_str($user_id)."' ";

			foreach ($roles as $index => $role_id)
			{
				$role_data = array(
					"client_id"=>$data["client_id"],
					"user_id"=>$data["user_id"],
					"role_id"=>$role_id
				);

				$queries[] = $this->getInsertString(TBL_USER_ROLES, $role_data);
			}
		}

		$return = $this->BASE_Transaction($queries);

		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".implode("\n", $queries)."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * Set user account activated
	 *
	 * @param string $user_id 		>> User identifier
	 * @param int $activation_state	>> 0 or 1
	 * 
	 * @return BASE_Result 			>> contains the userdata-array on success
	 *
	 */
	function update_activation_state($user_id, $activation_state)
	{
		return $this->BASE_Update(TBL_USER, array("activated"=>$activation_state), array("user_id"=>$user_id));
	}
	
	/**
	 * Delete (set deleted flag) a user and all its related data
	 *
	 * @version 1.0
	 *
	 * @param string $client_id
	 * @param string $user_id
	 * @param string $deleted_by
	 *
	 * @return BASE_Result
	 */
	function remove($client_id, $user_id, $deleted_by)
	{
		$data = array(
			"deleted" => 1,
			"deleted_by" => $deleted_by,
			"deleted_at" => time()
		);
		$return = $this->BASE_Update(TBL_USER, $data, array("client_id"=>$client_id, "user_id"=>$user_id));
	
		write2Debugfile(self::DEBUG_FILENAME, "\ndelete user\n".print_r($return, true));
		return $return;
	}
	
	/**
	 * Here is the place for actions after successfull login.
	 * - Save the new last login timestamp and reset the counter for failed logins
	 * 
	 * @access private >> called by ::authenticate()-method
	 *
	 * @param string $user_id
	 * @return void
	 */
	private function post_login_actions($user_id)
	{
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Save the new last login timestamp and reset the counter for failed logins
		$result = $this->BASE_Update(TBL_USER, array("last_login"=>time(), "failed_logins"=>0), array("user_id"=>$user_id));
		if ($result->getError() != ""){
			log_message(E_LOGLEVEL::ERROR, " - error while saving last login time for user [".$user_id."]\n".$result->getError());
		}
	}
}