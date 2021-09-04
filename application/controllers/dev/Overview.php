<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Dev controller 
 * 
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\tools
 * @version 1.0
 */
class Overview extends BASE_Controller 
{
	function __construct()
	{
		parent::__construct(true);
		
		$this->hasBreadcrump	= false;
		$this->hasNav			= true;
		$this->hasSidebar		= false;
		
		write2Debugfile(self::DEBUG_FILENAME, "dev-overview-controller");
	}
	
	/**
	 * default entry point.
	 * @param string $page
	 */
	public function index()
	{
		$this->show();
	}
	

	/**
	 * @param string $page
	 * @param E_RENDERMODE $renderData
	 */
	public function show($page='home', $renderData="FULLPAGE")
	{
		if (! E_RENDERMODE::isValidValue($renderData)){
			$renderData = E_RENDERMODE::FULLPAGE;
		}
		
	}
	
}