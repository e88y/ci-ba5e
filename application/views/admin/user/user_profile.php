<?php 
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);
	
	$all_locales 	= $data["available_languages"];
	$all_countries 	= $data["available_countries"];
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create user object
	$user = new T_User($data["user"]);
	
	$user_id = "";
	if ($user->user_id != "")
	{
		$user_id = encrypt_string($user->user_id);
	}
	
	if (! array_key_exists($user->language, $all_locales)){
		$user->language = "EN"; // DEFAULT
	}
	
	if ($user->country == "" || $user->country == null){
		$user->country = $this->getSessionItem(E_SESSION_ITEM::CLIENT_COUNTRY);
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the user form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$fi_username = new HTML_FormItem(lang("username"), "fi_username", "username", array(), E_REQUIRED::YES);
	$fi_username->setValidationState( form_error('username') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_username->addComponent(new HTML_Input("i_username", "username", E_INPUTTYPE::TEXT, lang("username"), $user->username, "", "", E_ENABLED::NO) );
	
	$fi_firstname = new HTML_FormItem(lang("firstname"), "fi_firstname", "firstname", array(), E_REQUIRED::YES);
	$fi_firstname->setValidationState( form_error('firstname') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_firstname->addComponent(new HTML_Input("i_firstname", "firstname", E_INPUTTYPE::TEXT, lang("firstname"), $user->firstname) );
	
	$fi_lastname = new HTML_FormItem(lang("lastname"), "fi_lastname", "lastname", array(), E_REQUIRED::YES);
	$fi_lastname->setValidationState( form_error('lastname') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_lastname->addComponent(new HTML_Input("i_lastname", "lastname", E_INPUTTYPE::TEXT, lang("lastname"), $user->lastname) );
	
	$fi_country	= new HTML_FormItem(lang("country"), "fi_country", "country", array(), E_REQUIRED::YES);
	$fi_country->setValidationState( form_error('country') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_country->addComponent( new HTML_Select("i_country", "country", HTML_Select::buildOptions($all_countries, "iso_2", "country_label", $user->country, "all", false), false, "", E_VISIBLE::YES ) );
	
	$fi_locale	= new HTML_FormItem(lang("language"), "fi_locale", "locale");
	$fi_locale	->setValidationState( form_error('locale') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_locale->addComponent( new HTML_Select("i_locale", "locale", HTML_Select::buildOptions($all_locales, "locale_code", "locale_label", $user->language, "all", false), false, "", E_VISIBLE::YES ) );
	
	$fi_phone = new HTML_FormItem(lang("phone"), "fi_phone", "phone", array(), E_REQUIRED::YES);
	$fi_phone->setValidationState( form_error('phone') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_phone->addComponent(new HTML_Input("i_phone", "phone", E_INPUTTYPE::TEXT, lang("phone"), $user->phone) );
	
	$fi_email = new HTML_FormItem(lang("email"), "fi_email", "email", array(), E_REQUIRED::YES);
	$fi_email->setValidationState( form_error('email') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_email->addComponent(new HTML_Input("i_email", "email", E_INPUTTYPE::TEXT, lang("email"), $user->email) );
	
	$btn_togglePassword = new HTML_Button("btn_toggle_password", "btn_toggle_password", "Passwort ändern", E_COLOR::STANDARD, E_SIZES::MD, E_ICONS::USER_SECRET, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array("btn-block"), array("data-toggle"=>"collapse", "data-target"=>"#fi_password, #fi_password_repeat"));
	$fi_change_password = new HTML_FormItem("", "fi_change_password", "password", array(), E_REQUIRED::NO);
	$fi_change_password->addComponent($btn_togglePassword);
	
	$fi_password = new HTML_FormItem(lang("password"), "fi_password", "password", array(), E_REQUIRED::YES);
	$fi_password->setValidationState( form_error('password') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_password->addComponent(new HTML_Input("i_password", "password", E_INPUTTYPE::PASSWORD, lang("password"), "" ) );
	
	if ($user->user_id != null && $data["js_enabled"] == 1)
	{
		$fi_password->setClasses(array("collapse"))->setRequired(E_REQUIRED::NO);
	}
	
	$fi_password_repeat = new HTML_FormItem(lang("password_repeat"), "fi_password_repeat", "password_repeat", array(), E_REQUIRED::YES);
	$fi_password_repeat->setValidationState( form_error('password_repeat') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_password_repeat->addComponent(new HTML_Input("i_password_repeat", "password_repeat", E_INPUTTYPE::PASSWORD, lang("password_repeat"), "") );
	
	if ($user->user_id != null  && $data["js_enabled"] == 1)
	{
		$fi_password_repeat->setClasses(array("collapse"))->setRequired(E_REQUIRED::NO);
	}
	
	$btn_submit = new HTML_Button("bt_submit", "submit_user", lang("save"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_submit->setType(E_BUTTON_TYPES::SUBMIT)->setValue(1)->setAttributes(array("form"=>"form_user")); // since we place this button outside the form
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_user"))->setValue(1)->setType(E_BUTTON_TYPES::RESET);
	
	$fi_submit 	= new HTML_FormItem("", "fi_submit", "submit");
	$fi_submit->addComponent( $btn_submit );
	
	$hidden_user_id 	= new HTML_Input("i_user_id", "user_id", E_INPUTTYPE::HIDDEN, lang("user_id"), ($user->user_id != "" ? $user_id : "")) ;
	$hidden_save		= new HTML_Input("i_save", "save", E_INPUTTYPE::HIDDEN, lang("save"), 1) ;
	$hidden_kp_ser_no 	= new HTML_Input("i_kp_ser_no", "kp_ser_no", E_INPUTTYPE::HIDDEN, lang("kp_ser_no"), $user->kp_ser_no) ;
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	//$form_user = new HTML_Form("form_userdata", "form_userdata", "#", lang("data_personal"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_user = new HTML_Form("form_userdata", "form_userdata", base_url("admin/profile"), lang("data_personal"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	
	$pnl_user = new HTML_Panel("form_userdata", lang("user_informations"));
	$pnl_user->setCollapsed(false);
	$pnl_user->setCollapseable(TRUE);
	if (!is_null($user->kp_ser_no) && $user->kp_ser_no > 0)
	{
		$form_user->addFormItem($fi_kp_ser_no);
	}
	$form_user
	->addFormItem($fi_username)
	->addFormItem($fi_firstname)
	->addFormItem($fi_lastname)
	->addFormItem($fi_country)
	->addFormItem($fi_locale)
	->addFormItem($fi_email)
	->addFormItem($fi_phone);
	
	if ($user->user_id != null && $data["js_enabled"] == 1){
		$form_user->addFormItem($fi_change_password);
	}else{
		$form_user->addFormItem(HTML_FormItem::buildLegendItem(lang("change_password")));
	}
	
	$form_user
	->addFormItem($fi_password)
	->addFormItem($fi_password_repeat)
	->addFormItem($hidden_user_id)
	->addFormItem($hidden_save);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the roles form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$user_roles = array();
	if (isset($data["user_roles"]) && is_array($data["user_roles"]))
	{
		foreach ($data["user_roles"] as $key => $user_role) {
			$user_roles[$user_role] = $user_role;
		}
	}
	
	$toggle_all	= "";
	$roles_li 	= "";
	if (isset($data["available_roles"]) && is_array($data["available_roles"]))
	{
		$roles_li = '<ul id="checked-list-box" name="roles" class="list-group checked-list-box disabled">';
		
		foreach ($data["available_roles"] as $key => $value) 
		{
			$checked = (array_key_exists($value->role_id, $user_roles) ? E_CHECKED::YES : E_CHECKED::NO);
			
			$role_name = $value->role_name;
			if ($value->is_static == 1){
				$role_name = lang($value->role_name);
			}
			
			$cb = new HTML_Checkbox("cb_".$value->role_id, "role[]", $role_name, $checked, $value->role_id, E_ENABLED::NO, E_INLINE::NO, E_VISIBLE::YES);
			
			$roles_li .= '
			<li id="li_'.$value->role_id.'" class="list-group-item">
				'.$cb->generateHTML().'
			</li>';
		}
		$roles_li .= '</ul>';
	}
	
	$fi_roles = new HTML_FormItem(lang("roles"), "fi_roles", "roles", array(), E_REQUIRED::YES);
	$fi_roles->setValidationState( form_error('role') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_roles->addComponent($roles_li);
	
	$form_roles = new HTML_Form("form_user_roles", "form_user_roles", "#", lang("assigned_roles"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form_roles->addFormItem($fi_roles);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the user settings form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$fi_locked	= new HTML_FormItem(lang("user_locked"), "fi_locked", "locked");
	$fi_locked->setValidationState( form_error('locked') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_locked->addComponent( new HTML_Toggle("i_locked", "locked", ($user->locked != "1" ? E_SELECTED::NO : E_SELECTED::YES), "", 1, E_SIZES::SM, E_ICONS::LOCK, E_ICONS::UNLOCK, E_COLOR::DANGER, E_COLOR::PRIMARY) );
	
	$themes		= array();
	foreach (E_THEMES::getConstants() as $key => $value) {
		$themes[] = array(
			"label"=>$key,
			"key"=>$value		
		);
	}

	$fi_theme = new HTML_FormItem(lang("theme"), "fi_theme", "theme");
	$fi_theme->setValidationState( form_error('theme') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_theme->addComponent( new HTML_Select("i_theme", "theme", HTML_Select::buildOptions($themes, "key", "label", $user->theme, "all", false), false, "", E_VISIBLE::YES ) );
	
	$form_settings = new HTML_Form("form_user_settings", "form_user_settings", "#", lang("settings"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form_settings
	->addFormItem($fi_locked)
	;
	// ->addFormItem($fi_theme)
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the avatar upload form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$src_avatar = HTML_Image::generatePlaceholderSVG(120, 120, lang("no_avatar"));
	if ($user->avatar != "")
	{
		$path		= $this->config->item("root_path") . $this->config->item("upload_folder") . $user->client_id ."/user_files/". $user->user_id ."/avatar/" . $user->avatar;
		$src_avatar	= HTML_Image::generateDataURIFromImage(append_to_filename($path, "_thumb"));
	
	}
	
	$img = new HTML_Image("img_avatar", "img_avatar", $src_avatar, "", 120, 120);
	
	$hidden_avatar	= new HTML_Input("i_avatar", "avatar", E_INPUTTYPE::HIDDEN, lang("avatar"), $user->avatar) ;
	
	
	$fi_avatar = new HTML_FormItem(lang("user_avatar"), "fi_avatar", "avatar");
	$fi_avatar->addComponent( $img );
	if ($data["js_enabled"] == 1){
		$fi_avatar->setVisible(false);
	}
	
	$fi_avatar_upload = new HTML_FormItem(lang("user_avatar_upload"), "fi_avatar_ul", "upload[]");
	$fi_avatar_upload->addComponent('<input id="input_upload" name="upload[]" class="btn btn-block btn-default" type="file">' );
	
	$form_upload_avatar = new HTML_Form("form_upload_avatar", "form_upload_avatar", "#", lang("data_avatar"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	
	$form_upload_avatar
	->addFormItem($fi_avatar)
	->addFormItem($fi_avatar_upload)
	->addFormItem($hidden_avatar);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together 
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form = new HTML_Form("form_user", "form_user", base_url("admin/profile"), "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	//$form->setAttributes(array("enctype"=>"multipart/form-data"));
	
	$form->addFormItem(
		'<div class="col-xs-12">'.$page_alerts.'</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">'.
			$form_user->generateHTML().'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">'.
			$form_roles->generateHTML().
			$form_settings->generateHTML().'
		</div>'
	);
	
	/**
		
		$form_upload_avatar->generateHTML().'
	 */
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_user", lang("profile"), "", "", E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
	$panel->setContent($form->generateHTML());
	$panel->setFooter($btn_submit->generateHTML()."&nbsp;".$btn_reset->generateHTML());
	
?>
<div class="row button-row">
	<div class="col-xs-12 ">
		<?php 
			echo $btn_reset->generateHTML()."&nbsp;";
			echo $btn_submit->generateHTML();
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
		<?php echo $form->generateHTML(); ?>
	</div>
	<div class="visible-lg col-lg-2">
	</div>
</div>