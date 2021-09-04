<?php
/**
 * Role model
 * 
 * @author Marco Eberhardt
 * @category Model
 * @package application\models\role_model
 * @version 1.0
 */
class Role_model extends BASE_Model 
{
	const DEBUG_FILENAME = "role_model.log";
	
	/**
	 * Constructor for the role model
	 */
	function __construct()
	{	
		write2Debugfile(self::DEBUG_FILENAME, "Role_model", false);
	}


	/**
	 * Add missing rights when saving the role (edit or create)
	 * @param string $client_id
	 * @param array $rights
	 * @param array &$queries Queries for transaction
	 *
	 */
	function getMissingRightsQueries($client_id, $rights, &$queries)
	{
		if (is_array($rights) && count($rights) > 0)
		{
			//if not root_client => add missing rights from root
			if ($client_id != $this->config->item("root_client_id"))
			{
				//saved rights of the client:
				$where = array(
					"client_id"=>$client_id
				);
					
				$client_rights_result = $this->BASE_Select(TBL_RIGHTS, $where);
	
				$client_rights = array();
				foreach ($client_rights_result->getData() as $right)
				{
					$client_rights[] = $right->right_id;
				}
	
				//root rights
				$where 	= array(
					"client_id"=>$this->config->item("root_client_id"),
					"active"=>1,
					"is_root_right" => 0
				);
					
				$root_rights_result = $this->BASE_Select(TBL_RIGHTS, $where);
				$root_rights = array();
				foreach ($root_rights_result->getData() as $right)
				{
					$root_rights[] = $right->right_id;
				}
	
				//compare and select missing rights for client:
				$add_rights = array();
				foreach ($root_rights as $right_id)
				{
					if (in_array($right_id, $rights) && !in_array($right_id, $client_rights))
					{
						$add_rights[] = $right_id;
					}
				}
	
	
				//add missing rights
				if (count($add_rights) > 0)
				{
					$this->db->select("*");
					$this->db->from(TBL_RIGHTS);
					$this->db->where("client_id",$this->config->item("root_client_id") );
					$this->db->where("active",1 );
					$this->db->where("is_root_right",0 );
					$this->db->where_in("right_id",$add_rights );
					$query = $this->db->get();
	
					if (! $query){
						return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
					}
					$rights_to_add = $query->result_object();
					foreach ($rights_to_add as $key => $right)
					{
						$right->client_id = $client_id;
						$queries[] = $this->getInsertString(TBL_RIGHTS, $right);
					}
				}
			}
		}
	}
		
	/**
	 * creates a new role entry and stores it's assigned rights
	 * Because there are multiple inserts to run, this method utilizes transactions and perform a rollback on error
	 *
	 * @version 1.2
	 * 
	 * @param string $client_id
	 * @param array $data
	 * @param array $rights
	 * 
	 * @return BASE_Result
	 */
	function create($client_id, $data, $rights)
	{
		write2Debugfile(self::DEBUG_FILENAME, "client_id[".$client_id."] create role-".print_r($data, true));
		if ($data["role_id"] == ""){
			$data["role_id"] 	= BASE_Model::generateUID(TBL_ROLES, "role_id", "", false, 10);
		}
	
		$data["client_id"] = $client_id;
		
		$queries = array( $this->getInsertString(TBL_ROLES, $data) );
		
		if (is_array($rights) && count($rights) > 0)
		{
			foreach ($rights as $index=>$right_id)
			{
				$queries[] = $this->getInsertString(TBL_ROLES_RIGHTS,
					array(
						"client_id"=>$client_id,
						"role_id"=>$data["role_id"],
						"right_id"=>$right_id
					)
				);
			}
			$this->getMissingRightsQueries($client_id, $rights, $queries);
		}
	
		$return = $this->BASE_Transaction($queries);
	
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".implode("\n", $queries)."\nreturn-".print_r($return, true));
		return $return;
	}
	
	/**
	 * load all roles for a given client id, utilizing the datatables library
	 * 
	 * @param string $client_id
	 * @param array $columns
	 * @param bool $btnEdit
	 * @param bool $btnDel
	 * @param bool $static_permission
	 * @param string $includeDeleted
	 * 
	 * @return BASE_Result >> containing an array or null
	 */
	function datatable($client_id, $columns, $btnEdit=false, $btnDel=false, $static_permission=false, $includeDeleted=false)
	{
		$this->load->library('Datatables');
		$this->load->helper('datatable');
		
		$fields = prepare_fields($columns, $this->listFields(TBL_ROLES), array("is_static"));
		$this->datatables->select("role_id, ".$fields);
		$this->datatables->from(TBL_ROLES);
		$this->datatables->where("client_id", $client_id);
		
		$this->datatables->edit_column('role_name', '$1', "callback_build_role_buttons(role_id, role_name, roles, $btnEdit, $btnDel, $static_permission, is_static)");
		$this->datatables->edit_column('created_at', '$1' , 'format_timestamp2datetime(created_at) ');
		$this->datatables->edit_column('role_desc', '$1' , 'callback_translate_if_static(is_static,role_desc) ');
		$this->datatables->edit_column('is_static', '$1', 'callback_integer2checkbox(role_id,is_static) ');
		
		if ($includeDeleted === false){
			$this->datatables->where("deleted", "0");
		}
	
		$result = $this->datatables->generate();
	
		write2Debugfile(self::DEBUG_FILENAME, "\n".$this->datatables->last_query()."\n\n".print_r(json_decode($result), true));
		
		return new BASE_Result($result, "", json_decode($result), E_STATUS_CODE::SUCCESS);
	}
	
	/**
	 * Load data for a given role_id
	 *
	 * @param string $client_id 	>> client id
	 * @param string $role_id		>> role id
	 * @param bool $includeDeleted	>> 
	 *
	 * @return BASE_Result
	 */
	function load($client_id, $role_id=null, $includeDeleted=false)
	{
		$fields = "*";
		$where 	= array(
			"client_id"=>$client_id
		);
	
		if ($role_id != null){
			$where["role_id"] = $role_id;
		}
		if ($includeDeleted === false){
			$where["deleted"] = "0";
		}
	
		$order_by = array("role_name"=>"asc");
		
		$return = $this->BASE_Select(TBL_ROLES, $where, $fields, $order_by);
	
		write2Debugfile(self::DEBUG_FILENAME, " - load client_id[$client_id] role_id[$role_id]\n".$this->lastQuery()."\n".print_r($return, true) );
		return $return;
	}
	
	/**
	 * Load rights for client
	 *
	 * @param string $client_id 	>> client id
	 * @param string $right_id		>> specific right/permission
	 * 
	 * @return BASE_Result
	 */
	function loadRights($client_id, $right_id=null)
	{
		$where = TBL_RIGHTS.".active = 1 AND ".TBL_RIGHTS.".client_id = '".$client_id."'";
		
		if ($client_id != $this->config->item("root_client_id"))
		{
			$where .= " AND ".TBL_RIGHTS.".is_root_right = '0'";
		}
		
		if ($right_id != null){
			$where .= " AND ".TBL_RIGHTS.".right_id = '".$right_id."'";
		}
		
		$this->db->select("*");
		$this->db->from(TBL_RIGHTS);
		$this->db->where($where);
		$this->db->order_by(TBL_RIGHTS.".is_root_right", "asc");
		$this->db->order_by(TBL_RIGHTS.".group_token", "desc");
		$this->db->order_by(TBL_RIGHTS.".right_name", "asc");
		$query = $this->db->get();

		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
		$return = new BASE_Result($query->result_object(), $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
		write2Debugfile(self::DEBUG_FILENAME, " - loadRights clientID[$client_id] ID[$right_id]\n".$this->lastQuery()."\n".print_r($return, true) );
		return $return;
	}
	
	/**
	 * load rights for a specific role
	 *
	 * @param string $client_id	>> client id
	 * @param string $role_id	>> role id
	 * @return BASE_Result 		>> containing an array with the rights or null
	 *
	 */
	function loadRoleRights($client_id, $role_id)
	{
		$fields = array(TBL_RIGHTS.".client_id",
			TBL_RIGHTS.".right_id",
			TBL_RIGHTS.".right_name",
			TBL_RIGHTS.".right_desc",
			TBL_RIGHTS.".is_root_right",
			TBL_RIGHTS.".active"
		);
	
		$this->db
		->select(implode(", ", $fields))
		->from( TBL_ROLES_RIGHTS )
		->join( TBL_RIGHTS, 
				TBL_RIGHTS.".client_id = '".$client_id."' AND ".
				TBL_RIGHTS.".right_id = ".TBL_ROLES_RIGHTS.".right_id AND ".
				TBL_RIGHTS.".active = '1'  ", "inner")
				
		->where(TBL_ROLES_RIGHTS.'.client_id', $client_id)
		->where(TBL_ROLES_RIGHTS.'.role_id', $role_id)
		->order_by(TBL_RIGHTS.'.is_root_right, '.TBL_RIGHTS.'.group_token, '.TBL_RIGHTS.'.right_name ');
	
		$query = $this->db->get();
	
		if (! $query){
			return new BASE_Result(null, $this->generateErrorMessage(), null, E_STATUS_CODE::DB_ERROR);
		}
	
		$numRows	= $query->num_rows();
		$data_obj	= $query->result_object();
		$data		= array();
		foreach ($data_obj as $key => $value) {
			$data[] = $value->right_id;
		}
	
		$return = new BASE_Result($data, $this->generateErrorMessage(), null, E_STATUS_CODE::SUCCESS);
		//write2Debugfile(self::DEBUG_FILENAME, "loadRoleRights ".count($data)." rights loaded");
		return $return;
	}
	
	/**
	 * delete (or set deleted flag) a role and all its related data (uses transaction)
	 *
	 * @param string $client_id
	 * @param string $role_id
	 * @param string $deleted_by
	 * 
	 * @return BASE_Result
	 */
	function remove($client_id, $role_id, $deleted_by)
	{
		$data = array(
			"deleted" => 1,
			"deleted_by" => $deleted_by,
			"deleted_at" => time()
		);
		$return = $this->BASE_Update(TBL_ROLES, $data, array("client_id"=>$client_id, "role_id"=>$role_id));
	
		write2Debugfile(self::DEBUG_FILENAME, "\ndelete role\n".print_r($return, true));
		return $return;
	}

	/**
	 * update an existing role and it's rights
	 * uses transactions
	 * 
	 * @param string $client_id
	 * @param string $role_id
	 * @param array $data
	 * @param array $rights
	 * 
	 * @return BASE_Result 
	 */
	function update($client_id, $role_id, $data, $rights)
	{
		$queries = array(
			$this->getUpdateString(TBL_ROLES, $data,  array("client_id" => $client_id, "role_id" => $role_id))
		); 
		
		if (is_array($rights) && count($rights) > 0)
		{
			$queries[] = "DELETE FROM ".TBL_ROLES_RIGHTS." WHERE client_id = '".$this->db->escape_str($client_id)."' AND role_id = '".$this->db->escape_str($role_id)."' ";
			
			foreach ($rights as $index=>$right_id)
			{
				$right_data = array(
					"client_id"=>$data["client_id"],
					"role_id"=>$data["role_id"],
					"right_id"=>$right_id
				);
		
				$queries[] = $this->getInsertString(TBL_ROLES_RIGHTS, $right_data);
			}
			
			$this->getMissingRightsQueries($client_id, $rights, $queries);
		}
		
		$queries_string = "";
		foreach ($queries as $key => $query) {
			$queries_string .= "\n".$query.";";
		}
		
		$return = $this->BASE_Transaction($queries);
		
		write2Debugfile(self::DEBUG_FILENAME, count($queries)." queries -\n".$queries_string."\nreturn-".print_r($return, true));
		return $return;
	}
} // End of Class

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Place your very custom callback functions here.  
// ..:: You can find common callbacks in the datatable_helper, so have a look there first 
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * Datatable callback function for the 'name' and 'desc' column.
 * Since static roles have a translatable name beginning with '#', we call the lang()-method for this entries.
 * All others will be displayed as they have been saved.
 *
 * @category Model
 * @package application\models\role_model
 * @version 1.0
 *
 * @param int $static
 * @param string $str
 *
 * @return string
 */
function callback_translate_if_static($static, $str)
{
	if ($static == 1){
		return lang($str);
	}
	return $str;
}

/**
 * Create buttons (delete & edit) for role datatable.
 * This is custom because of static attribute
 *
 * @category helper
 * @package application\helpers\datatable_helper
 * @version 1.0
 *
 * @param string $id
 * @param string $name
 * @param string $class
 * @param bool $btn_edit		>> add edit button
 * @param bool $btn_delete		>> add delete button
 * @param bool $translate		>> if true, the name will be translated
 * @param bool $encrypt			>> if true, the id will be encrypted
 * @return string
 */
function callback_build_role_buttons($id, $name, $class, $btn_edit=true, $btn_delete=true, $static_permission=false, $is_static=false, $encrypt=false)
{
	write2Debugfile("callback_build_role_buttons.log", "\nid[$id] name[$name] class[$class] edit[$btn_edit] delete[$btn_delete] hasPermission4Static[$static_permission] isStatic[$is_static] encrypt[$encrypt]");
	if ($encrypt == true){
		$id = encrypt_string($id);
	}

	if ($is_static){
		$name=lang($name);
	}
	
	$buttons 	= "";

	if ($btn_delete){
		
		if ($is_static && $static_permission == false){
			$buttons .= '<label class="dtbt_remove btn btn-xs btn-danger disabled"><i class="fa fa-trash" title="\''.$name.'\'&nbsp;'.lang("delete").'"></i></label>&nbsp;';
		}
		else{
			$buttons .= '<a href="'.base_url().'admin/'.$class.'/remove/'.$id.'" onclick="$.'.$class.'.remove(\''.$id.'\')" class="dtbt_remove btn btn-xs btn-danger"><i class="fa fa-trash" title="\''.$name.'\'&nbsp;'.lang("delete").'"></i></a>&nbsp;';
		}
		
	}

	if ($btn_edit){
		$buttons .= '<a href="'.base_url().'admin/'.$class.'/edit/'.$id.'" onclick="$.'.$class.'.edit(\''.$id.'\')" class="dtbt_edit btn btn-xs btn-primary"><i class="fa fa-pencil" title="\''.$name.'\'&nbsp;'.lang("edit").'"></i></a>&nbsp;';
	}

	return $buttons."&nbsp;".$name;
}
