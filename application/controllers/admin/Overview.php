<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Dashboard / overview controller
 *  
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\admin\overview
 * @version 1.0
 */
class Overview extends BASE_Controller
{
	const DEBUG_FILENAME = "overview.log";
	
	/**
	 * Constructor for the admin-overview controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		$this->javascript		= array("overview.js");
		$this->addPlugins();

		write2Debugfile(self::DEBUG_FILENAME, "admin/overview", false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index()
	{
		self::show(E_RENDERMODE::FULLPAGE);
	}
	
	/**
	 * Render the overview 
	 * 
	 * @param E_RENDERMODE $rendermode
	 */
	public function show($rendermode="FULLPAGE")
	{
		$count_users 		= $this->app_model->count(TBL_USER, array("client_id"=>$this->client_id, "deleted"=>0));
		$count_roles 		= $this->app_model->count(TBL_ROLES, array("client_id"=>$this->client_id, "deleted"=>0));
		
		$tiles_all = array(
			HTML_TileBox::tileObject(E_PERMISSIONS::USER_LIST, $count_users, lang("users"), base_url("admin/users/show"), E_ICONS::USERS, "bg-primary"),
			HTML_TileBox::tileObject(E_PERMISSIONS::ROLE_LIST, $count_roles, lang("roles"), base_url("admin/roles/show"), E_ICONS::OBJECT_GROUP, "bg-primary")
		);
		$tiles = HTML_TileBox::cleanupTilesArray($this->getSessionData(E_SESSION_ITEM::USER_PERMISSIONS), $tiles_all);
		
		write2Debugfile(self::DEBUG_FILENAME, "tiles\n".print_r($tiles, true));
		
		$this->page_heading	= buildPageHeading($this->config->item('site_title'), lang("dashboard"));
		
		$this->setViewData("tile_data", $tiles);
		
		$this->render("admin/overview", $rendermode);
	}
	
}
?>