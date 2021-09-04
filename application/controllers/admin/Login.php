<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Login controller
 * 
 * @author Marco Eberhardt
 * @category controller
 * @package application\controllers\admin\login
 * @version 1.0
 */
class Login extends BASE_Controller 
{
	const DEBUG_FILENAME = "login.log";
	
	/**
	 * Constructor for the login controller
	 */
	function __construct()
	{
		parent::__construct(false);
		
		$this->javascript		= array("login.js");
		
		$this->hasBreadcrump	= false;
		$this->hasNav			= true;
		$this->hasSidebar		= false;
		
		
		write2Debugfile(self::DEBUG_FILENAME, $this->uri->uri_string(), true);
	}
 	
    /**
     * If post is set, try to authenticate, set the session and redirect to overview first. 
     * The authenticate-method fills <code>$this->data</code>, which will passed to the view
     * 
     * @access public
     */
    public function index()
    {
    	$post = $this->input->post();
    	
    	// only if we have a post, we try to authenticate
    	if (is_array($post) && count($post) > 0 && $this->authenticate() === true )
    	{
    		write2Debugfile(self::DEBUG_FILENAME, "user has logged in with client-id[".$this->getSessionData(E_SESSION_ITEM::CLIENT_ID)."] - last-url[".$this->getSessionData(E_SESSION_ITEM::LAST_URL)."]");
    		
    		if ($this->getSessionData(E_SESSION_ITEM::LAST_URL) != "" && $this->getSessionData(E_SESSION_ITEM::LAST_URL) != $this->uri->uri_string())
    		{
    			$target = $this->getSessionData(E_SESSION_ITEM::LAST_URL);
    		}
    		else
    		{
    			// redirect to an overview-controller and skip rendering the login-view
    			$target = "admin/overview";
    			if ($this->getSessionData(E_SESSION_ITEM::CLIENT_ID) == $this->config->item("root_client_id")  ){
    				$target = "root/overview";
    			}
    		}
    		
    		write2Debugfile(self::DEBUG_FILENAME, "redirect to [$target]");
    		redirect(base_url($target));
    	}
    	
    	$this->render('admin/login', E_RENDERMODE::FULLPAGE);
    }
    
    /**
     * Authenticate via an AJAX-Request
     * This is a wrapper function to call the authenticate and perform a JSON-Rendermode
     * 
     * @access private
     */
    public function ajax_authenticate()
    {
    	$auth = $this->authenticate();
    	
    	
    	
    	$this->render($auth, E_RENDERMODE::JSON);
    }
    
    /**
     * Authentification method will call the user-model, if the form has passed through the validation
     * 
     * @access private
     * @return bool	$authenticated
     */
    private function authenticate()
    {
    	$authenticated = false;		// default return value
    	
    	// All the posts sent by the view
    	$username 	= $this->input->post("username");
    	$password 	= $this->input->post("password");

    	write2Debugfile(self::DEBUG_FILENAME, "authenticate with username: [".$username."] and password [".$password."]");
    	
    	$this->form_validation->set_rules('username', 'lang:username', 'required|min_length[5]');
    	$this->form_validation->set_rules('password', 'lang:password', 'required|min_length[5]');
    	
    	if ($this->form_validation->run()) // if the form has passed through the validation
    	{
    		// load and call the model
    		$this->load->model('user_model');
    		$result = $this->user_model->authenticate($username, $password);
    		
    		//$this->setData( $result ); // fill up the view data
    		
    		write2Debugfile(self::DEBUG_FILENAME, "auth-result\n".print_r($result, true));
    		
    		// check for model errors
    		if ($result->getError() == "") 
    		{
    			$userdata		= $result->data;

    			write2Debugfile(self::DEBUG_FILENAME, "\ncurrent-session".print_r($this->session, true));
    			// set session data
    			$this->setSessionData($userdata);
    			
    			$authenticated = true;
    			
    			
	    		// redirect to an overview-controller and skip rendering the login-view
    			$target = "admin/overview";
    			if ($this->getSessionData(E_SESSION_ITEM::CLIENT_ID) == $this->config->item("root_client_id")  ){
    				$target = "root/overview";
    			}
    			$this->setViewData("redirect_to", $target);
    			
    			write2Debugfile(self::DEBUG_FILENAME, "session\n".print_r($this->session, true));
    		}
    		else
    		{
    			$this->setData( $result );
    		}

    	}
    	else {
    		write2Debugfile(self::DEBUG_FILENAME, "authentication failed:\n".validation_errors());
    		
    		// validation failed. set the error to the views data and go ahead. the view will show the errors 
    		$this->setData( new BASE_Result(array(), validation_errors(), $this->form_validation->error_array(), E_STATUS_CODE::ERROR));
    	}
    	return $authenticated;
    }
}