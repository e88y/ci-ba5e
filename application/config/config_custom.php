<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Aditional configuration file
 *
 * This file contains additional config items for the project
 * It is auto loaded, therefore you can access it's values anytime anywhere with '$this->config->item("[value you want]")'
 *
 * @author Marco Eberhardt
 * @category config
 * @package application\config\config_custom.php
 * @version 1.0
 */

$dir		= (isset($_SERVER['DIR']) ? $_SERVER['DIR'] : '');
$port 		= ($_SERVER['SERVER_PORT'] != "80" ? ":".$_SERVER['SERVER_PORT'] : "");
$protocol 	= "http://";
if (isset($_SERVER['HTTPS']) &&
	($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
{
	$protocol = 'https://';
}

/**  
 * neccessary for file system access (e.g. uploads, downloads, etc)
 */
$config['root_path']		= $_SERVER['DOCUMENT_ROOT']."/".$dir."/" ;

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Basic site definitions
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['site_name']			= "_BA5E";
$config['site_title'] 			= 'BA5E - Where the rubber meets the road';
$config['site_description'] 	= 'A basic boilerplate for Codeigniter including Twitter Bootstrap';
$config['site_keywords']		= 'BA5E, development, base app, PHP, Codeigniter, Backend, Bootstrap, CSS3, jQuery, Application, Boilerplate, Starter, ';
$config['site_author'] 			= "e88y";
$config['site_copyright'] 		= '&copy; '.date("Y")." e88y's solutions";
$config['site_logo'] 			= PATH_IMAGES.'logos/site-logo_60x60.png';	// should match navbar height
$config['site_birthday']		= 1552431600; 			// 13.03.2019

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Layout options
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['fluid_header']			= 1;
$config['fluid_layout']			= 1;
$config['fluid_footer']			= 1;

$config['navbar_inverse']		= 0;
$config['navbar_fixed']			= 1;
$config['navbar_location']		= "top";		// <top|bottom>

$config['footer_inverse']		= 0;
$config['footer_fixed']			= 1;
$config['footer_location']		= "bottom"; 	// <top|bottom>
$config['footer_console']		= 1; 			

$config['sidebar_inverse']		= 0;
$config['sidebar_fixed']		= 1;
$config['sidebar_location']		= "left"; 		// <left|right>   !! @todo currently only right works

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Other Settings
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * the content delivery location for libs, and plugins.
 * You can add everything cared in <code>E_PLUGINS_JS</code> and <code>E_PLUGINS_CSS</code> to the BASE_Controllers plugins_js and plugins_css property
 *
 * The skeleton template view will concat the two parts to include the files.
 */
$config['cdn'] 						= $protocol.$_SERVER['SERVER_NAME'].$port."/cdn/"; 		// URL to the third party javascript libs & plugins
$config['app_version']				= "1.0";	
$config['login_attempts_till_lock'] = 3;																		// The number of failed login attempts till the user gets locked
$config['root_client_id']			= "0";	
$config['default_password']			= "base_".date("Y")."";														// Default password for initial user
$config['dbbackup_path']			= "application/backups/";
$config['upload_folder'] 			= "application/uploads/";

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Toggle functions
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['enable_debugFiles']	= 1;		// Enable/Disable <code>write2debugFile()</code>
$config['enable_consoleLog']	= 1;		// Enable/Disable <code>toConsole()</code> in JS
$config['enable_profiler']		= 0;		// Enable/Disable the Profiler. Note: The profiler will only activated in a non-production environment; Aktivating the profile will leak sensitive data  (@see BASE_Controller::profiler() )  
$config['enable_access_log']	= 0;		// Enable/Disable the controller access log
$config['enable_database_log']	= 0;		// Enable/Disable the database action log

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: E-Mails 
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['email_task_error']		= "support@base.com";
$config['email_task_finished']	= "support@base.com";
$config['email_db_error']		= "support@base.com";
$config['email_upload']			= "support@base.com";

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Encryption settings
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['encryption_settings']	= array(
	'driver'=>'openssl',
	'cipher'=>'aes-256',
	'mode'=>'ctr',
	'key'=>'#BA5E#Ixax4Y0Ox42ApkCyDhH54Fx64M'
);

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: Information about the developer
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$config['company_name'] 		= "ebby";
$config['company_contact_mail']	= 'support@e88y.de';
$config['company_homepage']		= 'www.e88y.de';
$config['company_street']		= 'Musterstraße';
$config['company_house_number']	= '1';
$config['company_zipcode']		= '127001';
$config['company_location']		= 'Localhost';
$config['company_country']		= 'DE';

/* End of file config_custom.php */
/* Location: ./application/config/config_custom.php */