<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Profile controller for logged in user
 *
 * @author Marco Eberhardt
 * @category users
 * @package application\controllers\admin\profile
 * @version 1.0
 */
class Profile extends BASE_Controller 
{
	const DEBUG_FILENAME = "users.log";
	
	/**
	 * array containing all available roles
	 * @var array
	 */
	private $available_roles 		= null;
	
	/**
	 * array containing all available countries
	 * @var array
	 */
	private $available_countries	= null;
	
	/**
	 * array containing all available entities
	 * @var array
	 */
	private $available_entities	= null;
	
	/**
	 * Constructor for the users controller
	 */
	function __construct()
	{
		parent::__construct(true);
		
		$this->load->library("value_objects/T_User.php");
		$this->load->model("user_model");
		$this->load->model("role_model");
		
    	$this->hasBreadcrump 	= true;
    	$this->javascript		= array("profile.js");
    	
    	$this->addPlugins(
    		E_PLUGIN::DATATABLES,
    		E_PLUGIN::BS_TOGGLE,
    		E_PLUGIN::SELECT2,
    		E_PLUGIN::FILE_INPUT
    	);
    	 
		$this->available_roles 		= $this->role_model->load($this->client_id)->data;				// All available roles
		$this->available_countries	= $this->app_model->getCountries($this->loaded_language)->data;	// db-cache is on for this one
		
		write2Debugfile(self::DEBUG_FILENAME, "admin/profile\n", false);
	}
	
	/**
	 * default entry point. leads to the show method
	 */
	public function index() {
		self::show();
	}
	
	/**
	 * 
	 * @param string $rendermode
	 * @return boolean
	 */
	public function save($rendermode="FULLPAGE")
	{
		//$post 	= $this->input->post();
		$saved	= false;
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Set validation rules
		if ($this->input->post("password") != "")
		{	// set password rules
			$this->form_validation->set_rules('password', 'lang:password', 'trim|required|matches[password_repeat]|min_length[8]');
			$this->form_validation->set_rules('password_repeat', 'lang:password_repeat', 'trim');
		}
	
		$this->form_validation->set_rules('country', 		'lang:country', 'required|exact_length[2]|validate_existance['.TBL_COUNTRIES.', country_code, '.lang('unknown_country').']');
		$this->form_validation->set_rules('locale', 		'lang:language', 'required|exact_length[2]|validate_existance['.TBL_LOCALES.', locale_code, '.lang("unknown_locale").']');
		$this->form_validation->set_rules('email', 			'lang:email', 'trim|required|valid_email|max_length[255]');
		$this->form_validation->set_rules('phone', 			'lang:phone', 'trim|max_length[100]');
		$this->form_validation->set_rules('firstname', 		'lang:firstname', 'trim|required|min_length[1]|max_length[255]');
		$this->form_validation->set_rules('lastname', 		'lang:lastname', 'trim|required|min_length[1]|max_length[255]');
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->form_validation->run() )
		{
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation passed...", true);
			
			$data = array(
				"client_id" => $this->client_id,
				"user_id" => $this->user_id,
				"email" => $this->input->post("email", true),
				"phone" => $this->input->post("phone", true),
				"firstname" => $this->input->post("firstname", true),
				"lastname" => $this->input->post("lastname", true),
				"zipcode" => $this->input->post("zipcode", true),
				"country" => $this->input->post("country", true),
				"language" => $this->input->post("locale", true),
			);
			
			if ($this->input->post("password") != ""){
				$data["password"] = $this->input->post("password", true);
			}
			write2Debugfile(self::DEBUG_FILENAME, "data-".print_r($data, true), true);
	
			$result = $this->user_model->BASE_Update(TBL_USER, $data, array("client_id"=>$this->client_id, "user_id"=>$this->user_id)); 
			
			if ($result->error == "")
			{
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				// update session data
				$this->setSessionItem(E_SESSION_ITEM::USER_FIRSTNAME, $data["firstname"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_LASTNAME, $data["lastname"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_EMAIL, $data["email"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_PHONE, $data["phone"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_ZIPCODE, $data["zipcode"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_COUNTRY, $data["country"]);
				$this->setSessionItem(E_SESSION_ITEM::USER_LANGUAGE, $data["language"]);
			}
		}
		else
		{
			$result = new BASE_Result(null, validation_errors(), $this->form_validation->error_array() );
			write2Debugfile(self::DEBUG_FILENAME, "\n - form validation failed...\n".validation_errors(), true);
		}
			
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: set the view data
		$this->setData($result);
		if ($result->error == "")
		{
			$this->setViewSuccess(lang("user_has_been_saved"));
			$saved = true;
		}
		write2Debugfile(self::DEBUG_FILENAME, "\nthis->data\n".print_r($this->data, true));
	
		
		
		return $saved;
	}
	
	/**
	 * Show profile overview
	 * 
	 * @param string $rendermode
	 */
	public function show($rendermode="FULLPAGE")
	{
		if (E_RENDERMODE::isValidValue(strtoupper($this->input->post("rendermode")))){
			$rendermode = strtoupper($this->input->post("rendermode"));
		}
		
		if (is_array($this->input->post()) && $this->input->post("save") == 1 )
		{	
			self::save(true);
		}
		
		$result_user 		= $this->user_model->load( $this->client_id, $this->user_id);
		$user_roles 		= $this->user_model->loadRoles($this->client_id, $this->user_id);
		
		$this->setViewData("user", $result_user->data );
		$this->setViewData("user_roles", $user_roles->data );
		$this->setViewData("available_countries", $this->available_countries);
		$this->setViewData("available_languages", $this->available_languages);
		$this->setViewData("available_roles", $this->available_roles);
		
		$this->render("admin/user/user_profile", $rendermode);
	}
	
}