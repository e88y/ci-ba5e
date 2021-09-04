<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 *
 */

if ( ! function_exists('buildSettingsDropdown'))
{
	/**
	 * Generates the markup of settings dropdown in the top menu
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @version 1.0
	 * 
	 * @param array $settings >> 
	 * 	$settings = array(
			array(
				"href"=>"#", 
				"enabled"=>true, 
				"label"=>"Change theme", 
				"title"=>"click here to change the theme", 
				"onclick"=>null, 
				"data-toggle"=>"modal", 
				"data-target"=>"#mdl_changeTheme"
			),
			array(
				"divider"=>true
			),
			array(
				"href"=>base_url()."admin/logout", 
				"enabled"=>$this->session->userdata(E_SESSION_ITEM::USER_LOGED_IN), 
				"label"=>lang("log_out"), 
				"title"=>($this->session->userdata(E_SESSION_ITEM::USER_LOGED_IN) == true ? 'click her to log off' : 'you are not logged in'), 
				"onclick"=>null
			)
		);
	 *
	 * @return string
	 */
	function buildSettingsDropdown($settings)
	{
		$defaultSettings = "";
	
		if (is_array($settings) && count($settings) > 0)
		{
			write2Debugfile("menu_helper.log", "buildSettingsDropdown\nsettings-".print_r($settings, true));
			//<span class="hidden-xs">Einstellungen</span>
			$defaultSettings = '
			<li class="dropdown">
				<a href="'.base_url("admin/users/settings").'" class="dropdown-toggle" data-toggle="dropdown">'.E_ICONS::COGS.'
			
				</a>
				
				<ul class="dropdown-menu" role="menu">';
				
			foreach ($settings as $key=>$setting)
			{
				if (isset($setting["divider"])){
					$defaultSettings .= '<li class="divider"></li>';
				}
				else
				{
					// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
					$onClick = "";
					if ($setting["onclick"] != ""){
						$onClick = 'onclick="'.$setting["onclick"].'"';
					}
	
					// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
					$class = "";
					if ($setting["enabled"] == false){
						$class = 'class="disabled"';
					}
	
					// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
					$title = "";
					if ($setting["title"] != ""){
						$title = 'title="'.$setting["title"].'"';
					}
	
					$defaultSettings .= '<li '.$class.'><a href="'.$setting["href"].'" '.$onClick.' '.$title.'>'.$setting["label"].'</a></li>';
				}
			}
			$defaultSettings .= '</ul></li>';
		}
	
		return $defaultSettings;
	}
}

if ( ! function_exists('buildLogoutButton'))
{
	/**
	 * Generates the markup for the logout button in the top menu
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @version 1.0
	 *
	 * @return string
	 */
	function buildLogoutButton()
	{
		$lbl 		= E_ICONS::SIGN_OUT.'<span class="hidden-xs hidden-sm">&nbsp;'.lang("log_out").'</span>';
		$btn_logout = '<li><a href="'.base_url("admin/logout").'">'.$lbl.'</a></li>';
		return $btn_logout;
	}
}


if ( ! function_exists('buildMenuItems'))
{
	/**
	 * Note that this is NOT the permission based menu. Only default items from 'app_nav'.
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @version 1.0
	 *
	 * @param array $menuItems
	 * @return string
	 */
	function buildMenuItems($menuItems)
	{
		write2Debugfile("menu_helper.log", " - buildMenuItems items-\n".print_r($menuItems, true));
		//$menu = '<ul class="nav navbar-nav">';
		$menu = "";
		if ($menuItems && count($menuItems) > 0)
		{
			$CI =& get_instance();
				
			$uri_string = $CI->uri->uri_string;
			$ex_uri = explode("/", $uri_string);
				
			$domain_uri = strstr($CI->uri->uri_string, '/', true);
			write2Debugfile("menu_helper.log", "URI[".$uri_string."] URI-DOMAIN[".$domain_uri."]\nexploded-uri-".print_r($ex_uri, true)."\nStart loop...\n===");
				
				
			foreach ($menuItems as $key => $value)
			{
				$item_uri = $value->item_ref;
				if ( substr($item_uri, (strlen($item_uri)-1), (strlen($item_uri)) ) == '/' ){
					$item_uri = substr($item_uri, 0, (strlen($item_uri)-1));
				}
	
				$ex_itemUri = explode("/", $item_uri);
				$diff 		= array_diff_assoc($ex_uri, $ex_itemUri);
	
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				$active = "";
				if ($domain_uri == "home" || $domain_uri == "")
				{
					if (count($ex_uri) == count($ex_itemUri) && count($diff) == 0 ){
						$active = ' class="active"';
					}
				}
				else {
					if ($ex_uri[0] == $ex_itemUri[0]){
						$active = ' class="active"';
					}
				}
	
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				$menu.= '
				<li'.$active.'>
					<a href="'.base_url().$value->item_ref.'" >
						<span class="hidden-xs hidden-sm hidden-md"> '.lang( substr($value->item_label, 1) ).'</span>
						<span class="visible-xs visible-sm visible-md">'.$value->item_icon.'</span></a>
				</li>';
	
				write2Debugfile("menu_helper.log", "\n-> item [$key] active[$active]\ndiff-".print_r($diff, true)."\nitem-".print_r($ex_itemUri, true));
			}
		}
		//$menu .= '</ul>';
		write2Debugfile("menu_helper.log", "\nResult:\n".$menu);
		return  $menu;
	}
}

if ( ! function_exists('buildNavbarLoginForm'))
{
	/**
	 * Generates a horizontal navbar-form to login which is visible on large screens in the top menu.
	 * For smaller screens there will be a login button which redirects to the login-controller
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @version 1.0
	 * 
	 * @param bool $withRegisterButton >> also creates a registration button if true
	 * 
	 * @return string li-element like below...
		 <li>
			 <form id="navForm_login" name="navForm_login" role="form" action="#" method="POST" class="form form-horizontal navbar-form visible-lg">
				 <fieldset>
					 <div class="form-group">
						 <input id="navInput_username" name="username" type="text" value="" class="form-control" style="width:120px;" placeholder="Benutzername">
						 <input id="navInput_password" name="password" type="text" value="" class="form-control" style="width:120px;" placeholder="Passwort">
						 <button id="navBtnLogin" name="login" type="button" class="btn btn-default">&nbsp;<i class="fa fa-sign-in"></i></button>
					 </div>
				 </fieldset>
			 </form>
		 </li>
	 */
	function buildNavbarLoginForm($withRegisterButton=true)
	{
	
		// 	..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: build the register button
		$btn_register = '';
		if ($withRegisterButton === true){
			$btn_register = '<li><a href="'.base_url().'register" title="'.lang("register_now").'">'.lang("register_now").'</a></li>';
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: create the login form items and add it to the form
		$validationstate= E_VALIDATION_STATES::NONE;
		if (form_error('username') != "" || form_error('password') != ""){
			$validationstate = E_VALIDATION_STATES::HAS_ERROR;
		}
	
		$fi_username 	= new HTML_Input("ni_username", "username", E_INPUTTYPE::TEXT, lang("username"));
		$fi_username->setStyles(array("width"=>"120px"));
	
		$fi_password 	= new HTML_Input("ni_password", "password", E_INPUTTYPE::PASSWORD, lang("password"));
		$fi_password->setStyles(array("width"=>"120px"));
	
		$fi_submit		= new HTML_Button("nbtn_login", "login", "", E_COLOR::STANDARD, E_SIZES::STANDARD, E_ICONS::SIGN_IN, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array() );
		$fi_submit->setType(E_BUTTON_TYPES::SUBMIT);
		$fi_submit->setAttributes(array("form"=>"nform_login"));
		
		$form = new HTML_Form("nform_login", "nf_login", base_url("admin/login/") ); 
		$form->setDirection(true)->setClasses(array("navbar-form", E_RESPONSIVE_CLASSES::VISIBLE_LG));
		$form->addFormItem(
			'<div class="form-group '.$validationstate.'">'.
				$fi_username->generateHTML().
				$fi_password->generateHTML().' '.
				$fi_submit->generateHTML().'&nbsp;
			</div>'
		);
		$loginForm = '';
		$loginForm .= '<li>'.$form->generateHTML().'</li>';
	
		write2Debugfile("menu_helper.log", "buildNavbarLoginForm\n".$loginForm);
	
		return $loginForm;
	}
}

if ( ! function_exists('buildLocalizationsMenu'))
{
	/**
	 * Generate the markup for the localization dropdown in the top menu
	 * @see navbar.css for stylings
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @version 1.0
	 *
	 * @param array $items
	 * @return string
	 */
	function buildLocalizationsMenu($items=array())
	{
		$ci =& get_instance();
		
		$header		= '<li class="header">'.lang("switch_language").'</li>';
		$footer		= "";
		$icon		= E_ICONS::LANGUAGE;
		$iconLabel 	= '<span class="label label-'.E_COLOR::STANDARD.'">'.count($items).'</span>';
		$onClick	= "";
		$href		= "";
		$image		= "";

		write2Debugfile("menu_helper.log", "\n\nbuild localizations menu-".print_r($items, true), false);

		$list 	= '<li><ul class="menu" >';// <!-- inner menu: contains the actual data -->

		if (count($items) > 0)
		{
			write2Debugfile("menu_helper.log", "count[".count($items)."]");

			foreach ($items as $key => $item)
			{
				write2Debugfile("menu_helper.log", "processing key [$key]");
					
				if ($onClick != ""){
					$onClick = 'onclick="'.$onClick.'"';
				}
				
				$href = base_url("home/set_locale/".$item->locale_code."/");
				
				$search_flag_for = $item->locale_code;
				if ($item->locale_code == "EN"){
					$search_flag_for = "GB";
				}
				
				$img = new HTML_Image("img_".$item->locale_code, "img_".$item->locale_code, HTML_Image::generateDataURIFromImage(PATH_IMAGES."/Flags/".strtolower($search_flag_for).".png"), "", 48, 48, E_IMAGE_STYLES::CIRCLE);
				
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				$itemContent = '
				<div class="pull-left">'.$img->generateHTML().'
				</div>
				<h4>'.$item->locale_code.'</h4>
				<p>'.$item->locale_name.'</p>';
				
				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
				$list .= '<li><a href="'.$href.'" '.$onClick .' >'.$itemContent.'</a></li> <!-- end item -->';
			}
		}
		else{
			write2Debugfile("menu_helper.log", "no locales");
		}

		$list .= '</ul></li>'; // Finish inner list

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$footer = ''; //'<li class="footer"><a href="#">'.$footer.'</a></li>';
		if ($list != "") {
			$list = '<ul class="dropdown-menu">'.$header.$list.$footer.'</ul>';
		}

		$return = '
		<li class="dropdown messages-menu ">
			<a href="'.base_url("admin/users/settings/language").'" class="dropdown-toggle" data-toggle="dropdown">
				'.$icon.'&nbsp;'.$iconLabel.'
			</a>
			'.$list.'
		</li>
			';

		write2Debugfile("menu_helper.log", "menu\n".$return);
		return $return;
	}
} // END OF Localizations Menu

if ( ! function_exists('buildUserMenu'))
{
	/**
	 * Generate the users/profile dropdown menu for the top nav
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\menu_helper
	 * @since 0.2
	 * @version 0.8
	 * 
	 * @param array $userdata
	 * 
	 * @return string
	 */
	function buildUserMenu($userdata)
	{
		$lbl_client_id = new HTML_Label("lbl_client_id", "Client: ".$userdata[E_SESSION_ITEM::CLIENT_ID]." | UID: ".$userdata[E_SESSION_ITEM::USER_ID], E_COLOR::INFO, E_SIZES::MD);

		
		$src_avatar = HTML_Image::generateDataURIFromImage(FCPATH."resources/img/logos/site-logo_60x60.png");
		if ($userdata[E_SESSION_ITEM::USER_AVATAR] != "")
		{
			$path		= $this->config->item("root_path") . $this->config->item("upload_folder") . $userdata[E_SESSION_ITEM::CLIENT_ID] ."/user_files/". $userdata[E_SESSION_ITEM::USER_ID] ."/avatar/" . $userdata[E_SESSION_ITEM::USER_AVATAR];
			$src_avatar	= HTML_Image::generateDataURIFromImage($path);
		}
		
		$img_avatar	= new HTML_Image("img_user_avatar", "img_user_avatar", $src_avatar, "txt", "100%", "100%", E_IMAGE_STYLES::THUMBNAIL);
		$img_avatar->setClasses(array("pull-left"));
		
		$com = '
		<li class="dropdown user user-menu">
			<a href="'.base_url().'admin/profile" class="dropdown-toggle" data-toggle="dropdown">
				<span class="visible-xs visible-sm">'.E_ICONS::USER_SECRET.'&nbsp;</span>
				<span class="visible-md visible-lg">'.E_ICONS::USER_SECRET.'&nbsp;'.lang("profile").'&nbsp;'.E_ICONS::ANGLE_DOWN.'</span>
			</a>
			<ul class="dropdown-menu">
				<li class="user-header bg-primary text-left ">
					<div class="col-xs-4 text-center no-padding">'.
						$img_avatar->generateHTML().'
					</div>
					<div class="col-xs-6 text-left">	
						<h4>'.$userdata[E_SESSION_ITEM::USERNAME].'</h4>
						<p>'.
							$userdata[E_SESSION_ITEM::USER_FIRSTNAME].' '.$userdata[E_SESSION_ITEM::USER_LASTNAME].'
							<small>'.$userdata[E_SESSION_ITEM::USER_EMAIL].'</small>
						</p>
					</div>
					<div class="col-xs-12 text-left no-padding">
						<br>'.
						lang("last_login").": ".format_timestamp2datetime($userdata[E_SESSION_ITEM::LAST_LOGIN]).'
						<br>'.
						$lbl_client_id->generateHTML().'
					</div>
				</li>
				
				<li class="user-body">
					<p class="marquee">
						<span></span>
					</p>
					<div class="col-xs-4 text-center">
					</div>
	
					<div class="col-xs-4 text-center">
					</div>
	
					<div class="col-xs-4 text-center">
					</div>
				</li>
	
				<!-- Menu Footer-->
				<li class="user-footer">
					<div class="col-xs-6 text-center">
						<a href="'.base_url('admin/profile').'" class="btn btn-primary btn-block">'.E_ICONS::USER_SECRET."&nbsp;".lang("profile").'</a>
					</div>
					<div class="col-xs-6 text-center">
						<a href="'.base_url('admin/logout').'" class="btn btn-primary btn-block">'.E_ICONS::SIGN_OUT."&nbsp;".lang("log_out").'</a>
					</div>
				</li>
			</ul>
		</li>
		';
		return $com;
	}
} // END OF User Menu

?>