<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Logout controller
 *
 * @author Marco Eberhardt
 * @category logout
 * @package application\controllers\admin\logout
 * @version 1.0
 */
class Logout extends BASE_Controller 
{
	const DEBUG_FILENAME = "logout.log";
	
	/**
	 * Constructor for the logout controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		write2Debugfile(self::DEBUG_FILENAME, "admin/logout", false);
	}
 	
	/**
	 * destroy the session
	 */
    public function index()
    {
    	write2Debugfile(self::DEBUG_FILENAME, " - destroying the session".print_r($this->session, true));
    	
    	$this->hasBreadcrump	= false;
    	$this->hasSidebar		= false;

    	$this->setViewData("online_time", number_format( (time() - $this->getSessionData(E_SESSION_ITEM::LOGGED_IN_AT)) / 60), 2);
    	
    	$this->session->sess_destroy();
    	
    	write2Debugfile(self::DEBUG_FILENAME, " - session after ".print_r($this->session, true));
    	
    	$this->render('admin/logout', E_RENDERMODE::FULLPAGE);
    }
}