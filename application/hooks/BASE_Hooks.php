<?php

/**
 * BASE Hooks - This file is meant to hold our hooks 
 * 
 * @see config/hooks
 * 
 * @category hooks
 * @package application\hooks\BASE_Hooks
 * 
 * @version 1.0
 */
class BASE_Hooks
{
	const DEBUG_FILENAME = "BASE_Hooks.log";
	
	protected $ci;
	
	function __construct()
	{
		$this->ci =& get_instance();
	}
}

?>