<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * HOME controller (= default controller)
 * 
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\home
 * @version 1.0
 */
class Home extends BASE_Controller 
{
	const DEBUG_FILENAME = "home.log";
	
	function __construct()
	{
		parent::__construct(false);
		
		$this->hasBreadcrump	= false;
		$this->hasNav			= true;
		$this->hasSidebar		= false;
		$this->javascript		= array("home.js");
		
		write2Debugfile(self::DEBUG_FILENAME, "home-controller");
	}
	
	/**
	 * default entry point.
	 * @param string $page
	 */
	public function index($page="home")
	{
		$this->home();
	}
	
	/**
	 * Shortcut functions to about page
	 */
	public function about(){
		self::view("about");
	}
	
	/**
	 * Shortcut functions to home page
	 */
	public function home()
	{
		$this->title 		= lang("home")." | ".$this->config->item('site_title');
		$this->pageHeading 	= buildPageHeading( lang("welcome_text"), $this->config->item('site_title'));
		
		$this->render(PATH_STATIC_VIEWS . "home", E_RENDERMODE::FULLPAGE);
	}
	
	/**
	 * Called by Javascript (Ajax) to set a marker, that javascript is supported
	 * @see resources/generic/app.js
	 */
	public function js()
	{
		$this->setSessionItem(E_SESSION_ITEM::JS_ENABLED, 1);
	
		if ($this->input->post("storage") !== null){
			$this->setSessionItem(E_SESSION_ITEM::JS_LOCAL_STORAGE, $this->input->post("storage", true));
		}
	}	
	
	/**
	 * Changes the locale in the users session and redirects to the last url to see the changed locale
	 * 
	 * @param string $locale
	 * @param string $reload_uri
	 * @param E_RENDERMODE $rendermode
	 */
	public function set_locale($locale="DE", $reload_uri=null, $rendermode="FULLPAGE")
	{
		if (array_key_exists($locale, $this->available_languages))
		{
			$this->setSessionItem(E_SESSION_ITEM::USER_LANGUAGE, $locale);
			
			if ($this->client_id != "" && $this->user_id != ""){
				$this->app_model->BASE_Update(TBL_USER, array("language"=>$locale), array("client_id"=>$this->client_id, "user_id"=>$this->user_id));
			}
			
			$this->redirect_action($this->getSessionData(E_SESSION_ITEM::LAST_URL)); 
		}
	}
	
	/**
	 * set sidebar expand state in the users session
	 */
	public function set_sidebar()
	{
		$expanded 		= ($this->input->post("expanded") == "true" ? 1:0);
		$selected_item 	= ($this->input->post("selected_item"));
	
		$this->setSessionItem(E_SESSION_ITEM::SIDEBAR_EXPANDED, $expanded );
		$this->setSessionItem(E_SESSION_ITEM::SIDEBAR_SELECTED_ITEM, $selected_item );
	
		if ($this->client_id != "" && $this->user_id != ""){
			$this->app_model->BASE_Update(TBL_USER, array("sidebar_expanded"=>$expanded, "sidebar_selected_item"=>$selected_item), array("client_id"=>$this->client_id, "user_id"=>$this->user_id));
		}
	}
	
	
	/**
	 * Render contact form
	 *
	 * @param mixed $args
	 * @param E_RENDERMODE $rendermode
	 */
	public function contact($args=null, $rendermode="FULLPAGE")
	{
		write2Debugfile(self::DEBUG_FILENAME, " - home/contact\n".print_r($this->data, true));
		$this->pageHeading 	= buildPageHeading( lang("contact"), $this->config->item('site_name'));
	
		$message_sent = false;
		if (is_array($this->input->post()) && $this->input->post("submit") == 1 )
		{
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// ..:: set validation rules
			$this->form_validation->set_rules("email", 'lang:email', 'trim|required|valid_email');
			$this->form_validation->set_rules("subject", 'lang:subject', 'trim|required');
			$this->form_validation->set_rules("message", 'lang:message', 'trim|required');
				
			// use users email
			$email_from = $this->getSessionData(E_SESSION_ITEM::USER_EMAIL);
				
			$data = array(
				"client_id" => $this->client_id,
				"email" => $email_from, //$this->input->post("email"),
				"subject" => ($this->input->post("subject")),
				"message" => ($this->input->post("message"))
			);
				
			if ($this->form_validation->run() )
			{
				write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
	
				$this->load->library("base_mailer");
				$message_sent = $this->base_mailer->send_emailFromTemplate("CONTACT_FORM", "mebby1e@gmx.net", $data, array());
	
				$result = new BASE_Result(null, "");
			}
			else {
				write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n".validation_errors(), true);
				$result = new BASE_Result(null, validation_errors(), $this->form_validation->error_array() );
			}
				
			$this->setData($result);
			$this->setViewData("message", $data);
			$this->setViewData("message_sent", $message_sent );
		}
		else
		{
			$this->setViewData("message", array(
				"email"=>$this->getSessionData(E_SESSION_ITEM::USER_EMAIL),
				"subject" => ""
			));
			$this->setViewData("message_sent", $message_sent );
		}
	
		$this->render('public/contact', $rendermode);
	}
	
	/**
	 * render a static view from views/pages
	 *
	 * @param string $page
	 * @param E_RENDERMODE $renderData
	 */
	public function view($page='home', $renderData="FULLPAGE")
	{
		if (! E_RENDERMODE::isValidValue($renderData)){
			$renderData = E_RENDERMODE::FULLPAGE;
		}
	
		$this->title 		= lang($page)." | ".$this->config->item('site_title');
		$this->pageHeading 	= buildPageHeading( lang($page), $this->config->item('site_title'));
	
		$viewpath = APPPATH.'/views/'.PATH_STATIC_VIEWS.'/'.$page.'.php';
		// if this function has been called manually with a non-existing page as parameter
		if ( ! file_exists($viewpath))
		{
			write2Debugfile(self::DEBUG_FILENAME, " >> 404 - file not found - ".APPPATH.'views\pages\\'.$page.'.php');
			// Whoops, we don't have a page for that!
			show_404($page);
		}
		else
		{
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			/* for custom stuff if needed */
			switch ($page)
			{
				case "imprint":
					//$this->javascript		= array("imprint.js");
					break;
	
				default :
					break;
			}
			$this->render(PATH_STATIC_VIEWS . $page, $renderData);
		}
	}
	
	
	
	
	/**
	 * If there is a view for the clients terms of service (with 'client_id' suffix) we show the view.
	 * Otherwise we check if there is a file called "tos.pdf" in the clients docs directory and pass it to the downloader.
	 *
	 * Note:
	 * The BASE_Controller recieves config items based on the subdomain and writes it into config.
	 * Therefore we should always have a value in <code>$this->config->item("client_id")</code>, even we have not yet a logged in user.
	 *
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 * @return void
	 */
	public function tos()
	{
		$view_fullpath	= APPPATH.'views/public/tos.php';
		$view			= 'public/tos.php';
		$file			= FCPATH.'resources/files/tos_'.strtolower($this->loaded_language).'.pdf';
		$dl_filename	= lang("terms_of_service")." - ".$this->loaded_language.".pdf";
		
		if (file_exists($view_fullpath))
		{	// we have a own view for it, so we show it
			$this->pageHeading = buildPageHeading( ucfirst( lang( "tos" )), $this->config->item('site_title'));
			$this->render($view, E_RENDERMODE::FULLPAGE);
		}
		else if (file_exists($file))
		{	// we have a file to download
			$this->load->library("BASE_Downloader");
			BASE_Downloader::download($file, $dl_filename, $this->client_id, false);
		}
		else{
			// @todo inform sys-admins via mail
			echo $view_fullpath;
		}
	}
	
	/**
	 * If there is a view for the clients imprint (with 'client_id' suffix) we show the view.
	 * Otherwise we check if there is a file called "imprint.pdf" in the clients docs directory and pass it to the downloader.
	 *
	 * Note:
	 * The BASE_Controller recieves config items based on the subdomain and writes it into config.
	 * Therefore we should always have a value in <code>$this->config->item("client_id")</code>, even we have not yet a logged in user.
	 *
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 * @return void
	 */
	public function imprint()
	{
		$view_fullpath	= APPPATH.'views/public/imprint.php';
		$view			= 'public/imprint.php';
		$file			= FCPATH.'resources/files/imprint_'.strtolower($this->loaded_language).'.pdf';
		$dl_filename	= lang("imprint")." - ".$this->loaded_language.".pdf";
		
		if (file_exists($view_fullpath))
		{	// we have a own view for it, so we show it
			$this->pageHeading = buildPageHeading( ucfirst( lang( "imprint" )), $this->config->item('site_title'));
			$this->render($view, E_RENDERMODE::FULLPAGE);
		}
		else if (file_exists($file))
		{	// we have a file to download
			$this->load->library("BASE_Downloader");
			BASE_Downloader::download($file, $dl_filename, $this->client_id, false);
		}
		else{
			// @todo inform sys-admins via mail
		}
	}
	
	/**
	 * If there is a view for the clients privacy policy (with 'client_id' suffix) we show the view.
	 * Otherwise we check if there is a file called "privacy.pdf" in the clients docs directory and pass it to the downloader.
	 *
	 * Note:
	 * The BASE_Controller recieves config items based on the subdomain and writes it into config.
	 * Therefore we should always have a value in <code>$this->config->item("client_id")</code>, even we have not yet a logged in user.
	 *
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 * @return void
	 */
	public function privacy()
	{
		$view_fullpath	= APPPATH.'views/public/privacy.php';
		$view			= 'public/privacy.php';
		$file			= FCPATH.'resources/files/privacy_'.strtolower($this->loaded_language).'.pdf';
		$dl_filename	= lang("privacy_policy")." - ".$this->loaded_language.".pdf";
		
		if (file_exists($view_fullpath))
		{	// we have a own view for it, so we show it
			$this->pageHeading = buildPageHeading( ucfirst( lang( "privacy_policy" )), $this->config->item('site_title'));
			$this->render($view, E_RENDERMODE::FULLPAGE);
		}
		else if (file_exists($file))
		{	// we have a file to download
			$this->load->library("BASE_Downloader");
			BASE_Downloader::download($file, $dl_filename, $this->client_id, false);
		}
		else{
			// @todo inform sys-admins via mail
		}
	}
	
}
