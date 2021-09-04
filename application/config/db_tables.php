<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Definition of all table names
 */
define("DATABASE_NAME", "ci_base_core");
define('TABLE_PREFIX', 'app_');
define('MODEL_PREFIX', 'model_');

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: APP :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_APP', 'app');											                    // application version etc.
define('TBL_APP_SETTINGS', TABLE_PREFIX.'_settings');									// application settings
define('TBL_APP_SESSIONS', TABLE_PREFIX.'_sessions');				                    // sessions table
define('TBL_APP_MENU', TABLE_PREFIX.'_menu');						                    // menu items
define('TBL_APP_NAV', TABLE_PREFIX.'_nav');												// top navigation items (no permissions required)

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: CLIENTS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_CLIENTS', TABLE_PREFIX.'clients');						                    // application clients
define('TBL_DOMAIN_CONFIG', TABLE_PREFIX.'_domain_config');						    	// custom, domain depending configs  

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: APP_LOG :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_LOG_ACCESS', TABLE_PREFIX.'log__access');				                    // log controller access 
define('TBL_LOG_DATABASE', TABLE_PREFIX.'log__database');			                    // log database actions 
define('TBL_LOG_TASKS', TABLE_PREFIX.'log__tasks');					                    // log sheduled task executions 
define('TBL_LOG_EMAIL', TABLE_PREFIX.'log__email');					                    // log sent emails

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: ROLES & RIGHTS ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_ROLES', TABLE_PREFIX.'roles');							                    // roles table (permission groups)
define('TBL_ROLES_RIGHTS', TABLE_PREFIX.'roles__rights');			                    // permissions 2 roles relation table

define('TBL_RIGHTS', TABLE_PREFIX.'_rights');						                    // permissions table

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: TEMPLATES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_TEMPLATES_EMAIL', TABLE_PREFIX.'templates__mails');		                    // templates for emails
define('TBL_TEMPLATES_EMAIL', TABLE_PREFIX.'templates__fax');		                    // templates for fax

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: USER ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_USER', TABLE_PREFIX.'user');                                                // application users
define('TBL_USER_ROLES', TABLE_PREFIX.'user__roles');				                    // roles 2 user relation table

// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: GENERIC MODELS ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
define('TBL_CONTINENS', MODEL_PREFIX."_continents");						            // continents table
define('TBL_CONTINENS_L18N', MODEL_PREFIX."_continents_l18n");				            // continents localizations
define('TBL_COUNTRIES', MODEL_PREFIX."_countries");						                // country table
define('TBL_COUNTRIES_L18N', MODEL_PREFIX."_countries_l18n");				            // country localizations
define('TBL_LOCALES', TABLE_PREFIX."locales");							                // available localizations / supported application languages
define('TBL_LOCALES_L18N', TABLE_PREFIX."locales__l18n");							// actually the translations

/* End of file db_tables.php */
/* Location: ./application/config/db_tables.php */