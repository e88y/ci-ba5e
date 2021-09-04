<?php
/**
 * BASE_Permissions - Value Object
 * 
 * This is part of the permission check. Here we assign permissions to each controller-method.
 * If a permission is set to an empty string (NULL will not work), the check will pass since there is no special permission required.
 * 
 * Each method has to be in this array or calling it will result in a permission denied error.
 * @see BASE_Controller::checkPermissions()  -> $grant_without_rule --> false
 * 
 * @author Marco Eberhardt
 * @category value object
 * @package application\core\BASE_Permissions
 * @version 1.1
 */
class BASE_Permissions
{
	/**
	 * Return the permission-mapping
	 * @todo Maybe we outsource this to the database. while developing it comes more handy here imo. 
	 *  
	 * @return array >> array-map containing all perissions for the controller-methods
	 */
	public static function getMapping()
	{
		$permission_map = array();
		
		$permission_map["admin"]["login"]["index"] 				                    = "";
		$permission_map["admin"]["login"]["ajax_authenticate"]	                    = "";
		$permission_map["admin"]["login"]["authenticate"]		                    = "";
		
		$permission_map["admin"]["logout"]["index"] 			                    = "";

		$permission_map["admin"]["overview"]["index"] 			                    = "";	
		$permission_map["admin"]["overview"]["show"] 			                    = "";
		
		// ..:: PROFILE :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["admin"]["profile"]["index"] 			                    = "";
		$permission_map["admin"]["profile"]["show"] 			                    = "";
		
		// ..:: USERS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["admin"]["users"]["index"] 				                    = E_PERMISSIONS::USER_LIST;
		$permission_map["admin"]["users"]["show"] 				                    = E_PERMISSIONS::USER_LIST;
		$permission_map["admin"]["users"]["datatable"]			                    = E_PERMISSIONS::USER_LIST;
		$permission_map["admin"]["users"]["create"] 			                    = E_PERMISSIONS::USER_CREATE;
		$permission_map["admin"]["users"]["edit"] 				                    = E_PERMISSIONS::USER_EDIT;
		$permission_map["admin"]["users"]["remove"] 			                    = E_PERMISSIONS::USER_DELETE;
		
		// ..:: ROLES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["admin"]["roles"]["index"] 				                    = E_PERMISSIONS::ROLE_LIST;
		$permission_map["admin"]["roles"]["show"] 				                    = E_PERMISSIONS::ROLE_LIST;
		$permission_map["admin"]["roles"]["datatable"]			                    = E_PERMISSIONS::ROLE_LIST;
		$permission_map["admin"]["roles"]["create"] 			                    = E_PERMISSIONS::ROLE_CREATE;
		$permission_map["admin"]["roles"]["edit"] 				                    = E_PERMISSIONS::ROLE_EDIT;
		$permission_map["admin"]["roles"]["remove"] 			                    = E_PERMISSIONS::ROLE_DELETE;
		
		// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: ROOT-Permissions ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		
		$permission_map["root"]["overview"]["show"] 			                    = E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["overview"]["index"] 			                    = E_PERMISSIONS::IS_ROOT;
		
		// ..:: CRUD-BUILDER ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["root"]["crud"]["index"] 			                   		= E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["crud"]["generate"]			                   		= E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["crud"]["datatable"]		                   		= E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["crud"]["show"]		                   				= E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["crud"]["tabledata"]                   				= E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["crud"]["analyze"]	                   				= E_PERMISSIONS::IS_ROOT;
		
		// ..:: CLIENTS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["root"]["clients"]["index"] 				                = E_PERMISSIONS::ROOT_CLIENT_LIST;
		$permission_map["root"]["clients"]["show"] 				                    = E_PERMISSIONS::ROOT_CLIENT_LIST;
		$permission_map["root"]["clients"]["datatable"]			                    = E_PERMISSIONS::ROOT_CLIENT_LIST;
		$permission_map["root"]["clients"]["create"] 			                    = E_PERMISSIONS::ROOT_CLIENT_CREATE;
		$permission_map["root"]["clients"]["edit"] 				                    = E_PERMISSIONS::ROOT_CLIENT_EDIT;
		$permission_map["root"]["clients"]["remove"] 			                    = E_PERMISSIONS::ROOT_CLIENT_DELETE;
		
		// ..:: LOCALES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$permission_map["root"]["locales"]["show"] 				                    = E_PERMISSIONS::ROOT_LOCALE_LIST;
		$permission_map["root"]["locales"]["index"] 			                    = E_PERMISSIONS::ROOT_LOCALE_LIST;
		$permission_map["root"]["locales"]["datatable"]								= E_PERMISSIONS::ROOT_LOCALE_LIST;
		$permission_map["root"]["locales"]["create"] 			                    = E_PERMISSIONS::ROOT_LOCALE_CREATE;
		$permission_map["root"]["locales"]["edit"] 				                    = E_PERMISSIONS::ROOT_LOCALE_EDIT;
		$permission_map["root"]["locales"]["remove"] 			                    = E_PERMISSIONS::IS_ROOT;
		$permission_map["root"]["locales"]["generate"] 				                = E_PERMISSIONS::IS_ROOT;
		
	     
		return $permission_map;
	}
}
?>