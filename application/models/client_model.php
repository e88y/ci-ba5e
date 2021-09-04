<?php
/**
 * Client model
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\client_model
 * @version 1.1
 */
class Client_model extends BASE_Model 
{
	const DEBUG_FILENAME = "client_model.log";
	
	/**
	 * Constructor for the client model
	 */
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "Client model", false);
	}
	
	/**
	 * count all clients
	 *
	 * @param bool $includeDeleted
	 * @return int
	 */
	function count_clients($includeDeleted=false)
	{
		$where = array();
		if ($includeDeleted === false){
			$where["deleted"] = 0;
		}
	
		$count = $this->count(TBL_CLIENTS, $where);
	
		write2Debugfile(self::DEBUG_FILENAME, "count system clients >> result[$count]" );
		return $count;
	}
	
	/**
	 * store a new client entry
	 * 
	 * @param array $data
	 * 
	 * @return BASE_Result
	 */
	function create($data)
	{
		$root_clientID = $this->config->item("root_client_id");
		
		if ($data["client_id"] == ""){
			$data["client_id"] 	= BASE_Model::generateUID(TBL_CLIENTS, "client_id", "", true, 12);
		}
		
		$client_id = $data["client_id"]; 
		
		write2Debugfile(self::DEBUG_FILENAME, "client_id[".$data["client_id"]."] create client-".print_r($data, true));

		$queries = array( 
			$this->getInsertString(TBL_CLIENTS, $data) 
		);
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// create the initial user with administrator role assigned
		$password_plain 	= $this->config->item("default_password");
		$salt				= random_string("alnum", 8);
		$password_hashed	= hash_password($salt, $password_plain);
		$user_id 			= BASE_Model::generateUID(TBL_USER, "user_id", "init_", false, 12);
		$username 			= "admin_".$data["customer_number"];
		$country			= "DE";
		$language			= "DE";
		
		$userdata = array(
			"client_id" => $client_id, "user_id" => $user_id,
			"username" => $username, "password" => $password_hashed, "salt"=>$salt, "email"=>$data["client_email"],
			"firstname"=>"Admin", "lastname"=>$data["client_name"],
			"street"=>$data["client_street"], "house_number"=>$data["client_house_nr"], "zipcode"=>$data["client_zipcode"], "location"=>$data["client_location"],
			"country"=>$country, "language"=>$language, "failed_logins"=>0, "created_at"=>time()
		);
		$queries[] = $this->getInsertString(TBL_USER, $userdata);
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// Get base roles from ROOT-CLIENT to copy  
		$roles 	= $this->BASE_Select(TBL_ROLES, array("client_id"=>"$root_clientID", "is_static"=>1, "is_root"=>0, "deleted"=>0));
		//write2Debugfile(self::DEBUG_FILENAME, "- get roles:\n".$this->lastQuery());
		
		foreach ($roles->data as $key => $role) 
		{
			$role->client_id 	= $client_id;
			$role->created_at 	= time();
			
			$queries[] = $this->getInsertString(TBL_ROLES, $role);
			$queries[] = $this->getInsertString(TBL_USER_ROLES, array("client_id"=>$client_id, "user_id"=>$user_id, "role_id"=>$role->role_id));
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$roles_rights = $this->BASE_Select(TBL_ROLES_RIGHTS, array("client_id"=>"$root_clientID", "role_id"=>$role->role_id));
			//write2Debugfile(self::DEBUG_FILENAME, "- role rights:\n".$this->lastQuery());

			foreach ($roles_rights->data as $key => $roles_right) {
				$queries[] = $this->getInsertString(TBL_ROLES_RIGHTS, array("client_id"=>$client_id, "role_id"=>$role->role_id, "right_id"=>$roles_right->right_id));
			}
		}
		write2Debugfile(self::DEBUG_FILENAME, "roles count[".count($roles->data)."]");

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// Get all rights from ROOT-CLIENT to copy
		$rights = $this->BASE_Select(TBL_RIGHTS, array("client_id"=>"0", "is_root_right"=>0));
		foreach ($rights->data as $key => $right) 
		{
			$right->client_id = $client_id;
			$queries[] = $this->getInsertString(TBL_RIGHTS, $right);
		}
		write2Debugfile(self::DEBUG_FILENAME, "rights count[".count($rights->data)."]");
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}

		
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".$queries_string);
		$return = $this->BASE_Transaction($queries);
		
		if ($return->error == ""){
			$return->messages = "initial-user: ".$username;
		}
		
		
		write2Debugfile(self::DEBUG_FILENAME, "return-".print_r($return, true));
		return $return;
	}
	
	/**
	 * load all clients utilizing the datatables library
	 *
	 * @param string $client_id		>> client identifier
	 * @param array $columns		>> the columns
	 * @param bool $btnEdit			>> include an edit button
	 * @param bool $btnDel			>> include a delete button
	 * @param bool $includeDeleted	>> show deleted entries
	 *
	 * @return BASE_Result >> containing an array or null
	 */
	function datatable($client_id, $columns, $btnEdit=false, $btnDel=false, $includeDeleted=true)
	{
		$this->load->library('Datatables');
		$this->load->helper('datatable');
		
		$btnEdit 	= intval($btnEdit);
		$btnDel 	= intval($btnDel);
		$fields 	= prepare_fields($columns, $this->listFields(TBL_CLIENTS));
		
		$this->datatables->select( $fields )->from(TBL_CLIENTS);
        $this->datatables->edit_column('client_name', '$1', "callback_build_buttons(client_id, client_name, root, clients, $btnEdit, $btnDel, 0, 0, 0)");
		$this->datatables->edit_column('deleted', '$1' , 'callback_deleted(client_id, deleted) ');
		$this->datatables->edit_column('created_at', '$1' , 'format_timestamp2datetime(created_at) ');
		
		if ($includeDeleted === false){
			$this->datatables->where("deleted", "0");
		}
		
		$includeRoot = true;
		if ($includeRoot === false){
			$this->datatables->where("client_id <> '".$this->config->item("root_client_id")."'");
		}
	
		$result 		= $this->datatables->generate();
		$result_json 	= json_decode($result);
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".$this->datatables->last_query()."\n\n".print_r(json_decode($result), true));
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * check if client_name is already used
	 *
	 * @param string $name
	 * @return boolean
	 */
	function exists_name($name)
	{
		$where = array(
				"client_name"=>$name
		);
	
		$return = $this->BASE_Select(TBL_CLIENTS, $where, array("client_id"));
		if ($return->getData()){
			return true;
		}
		return false;
	}

	/**
	 * Update client data 
	 * 
	 * @param string $client_id
	 * @param array $data
	 * @return BASE_Result 
	 */
	function update($client_id, $data)
	{
		write2Debugfile(self::DEBUG_FILENAME, "- update client_id[".$client_id."] data-".print_r($data, true));
		
        /*
		$queries = array( 
			$this->getUpdateString(TBL_CLIENTS, $data,  array("client_id" => $client_id)) 
		);
			
		$return = $this->BASE_Transaction($queries);*/
        $return = $this->BASE_Update(TBL_CLIENTS, $data, array("client_id"=>$client_id));
		//write2Debugfile("zzzz_client.txt", $this->lastQuery()."\n\n\n".print_r($data,true));
		//write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries executed".print_r($queries, true)."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * Load specific or all clients. 
	 *  
	 * @param string $client_id 	>> client id
	 * @param bool $includeDeleted	>> 
	 * @return BASE_Result
	 */
	function load($client_id=null, $includeDeleted=true)
	{
		$fields = array("*");
		$where 	= array();
        
        if($client_id != null)
        {
    		if ($client_id != $this->config->item("root_client_id") || $this->user_id == 0){
    			$where 	= array("client_id"=>$client_id);
    		}
        }
		if ($includeDeleted === false){
			$where["deleted"] = "0";
		}
		
		$return = $this->BASE_Select(TBL_CLIENTS, $where, $fields, array());
		
		write2Debugfile(self::DEBUG_FILENAME, " - load clientID[$client_id]\n".$this->lastQuery()."\n".print_r($return, true) );
		return $return;
	}
	
	
	/**
	 * delete (or set deleted flag) a client and all its related data
	 *
	 * @version 1.2
	 *
	 * @param string $client_id		>> client you want to delete
	 * @param string $deleted_by	>> username
	 *
	 * @return BASE_Result
	 */
	function remove($client_id, $deleted_by)
	{
		if ($client_id == $this->config->item("root_client_id")){
			return new BASE_Result(false, lang("msg_you_cannot_delete_this_entry"));
		}
		
		$data = array(
			"deleted" => 1,
			"deleted_by" => $deleted_by,
			"deleted_at" => time()
		);
		
        /*
		$queries = array(
			$this->getUpdateString(TBL_CLIENTS, $data, array("client_id"=>$client_id)),
			$this->getUpdateString(TBL_USER, $data, array("client_id"=>$client_id)),
			$this->getUpdateString(TBL_ROLES, $data, array("client_id"=>$client_id)),
			$this->getUpdateString(TBL_RIGHTS, $data, array("client_id"=>$client_id))
		);
		
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}*/
        
         
		$return1 = $this->BASE_Update(TBL_USER, $data, array("client_id"=>$client_id));
		$return2 = $this->BASE_Update(TBL_CLIENTS, $data, array("client_id"=>$client_id));
		
		//$return = $this->BASE_Transaction($queries);
		
		//write2Debugfile(self::DEBUG_FILENAME, "remove client. ".count($queries)." queries -\n".$queries_string."\nreturn-".print_r($return, true));
		
		return $return2;
	}

    /**
     * Load all the countries of users that share a client ID
     * @return array
     */
    function load_countries_by_client_users($langauge){

        $where 	= array(
            "deleted"=>0,
        );

        $fields = array(
            TBL_USER.".client_id",
            TBL_USER.".country",
            TBL_COUNTRIES_L18N.".country_name",
        );

        $this->db
            ->select( implode(", ", $fields) )
            ->from(TBL_USER)
            ->join(TBL_COUNTRIES_L18N, TBL_COUNTRIES_L18N.'.country_code = '.TBL_USER.'.country ', 'inner')
            ->where(TBL_COUNTRIES_L18N.".locale_code = '".$langauge."'")
            ->group_by( implode(", ", array(TBL_USER.".client_id", TBL_USER.".country")));
        $query = $this->db->get();

        $data_obj	= $query->result_object();
        $data		= array();
        foreach ($data_obj as $key => $value) {
            $data[$value->client_id][] = array("iso2"=>$value->country, "country_name" => $value->country_name);
        }

        write2Debugfile(self::DEBUG_FILENAME, " TEST-\n".$this->lastQuery()."\n".print_r($data, true) );

        return $data;
    }
	
	
}
/**
 * datatable callback function for the 'deleted' column
 *
 * @author _BA5E
 * @category Model
 * @package application\models\clients_model
 * @since 1.2
 * @version 1.2
 *
 * @param string $id
 * @param string $deleted
 *
 * @return string
 */
function callback_deleted_client($id, $deleted)
{
	list($deleted, $deleted_at, $deleted_by) = explode("#", $deleted);
	$com = ($deleted != 1 ?  E_ICONS::SQUARE_WHITE : E_ICONS::CHECK_SQUARE_WHITE." ".format_timestamp2date($deleted_at, '') );
	return $com;
}
