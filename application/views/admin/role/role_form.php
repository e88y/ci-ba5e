<?php 
	
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create role object	
	$role = new T_Role($data["role"]);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the role form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$i_rolename = new HTML_Input("i_role_name", "role_name", E_INPUTTYPE::TEXT, lang("role_name"), $role->role_name);
	if ($role->is_static){
		$i_rolename->setValue(lang($role->role_name));
		$i_rolename->setEnabled(false);
	}
	$fi_rolename = new HTML_FormItem(lang("name"), "fi_role_name", "role_name", array(), E_REQUIRED::YES);
	$fi_rolename->setValidationState( form_error('role_name') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_rolename->addComponent( $i_rolename );
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$i_roledesc = new HTML_TextArea("i_role_desc", "role_desc", $role->role_desc, lang("role_desc"), E_VISIBLE::YES);
	if ($role->is_static)
	{
		$i_roledesc->setText(lang($role->role_desc));
		$i_roledesc->setEnabled(false);
	}
	$fi_roledesc = new HTML_FormItem(lang("desc"), "fi_role_desc", "role_desc", array(), E_REQUIRED::NO);
	$fi_roledesc->setValidationState( form_error('role_desc') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_roledesc->addComponent( $i_roledesc );
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$toggle = new HTML_Toggle("toggle_is_static", "is_static", ($role->is_static == 1 ? E_SELECTED::YES : E_SELECTED::NO), lang("static"), 2, E_SIZES::SM, E_ICONS::CHECK_SQUARE_WHITE."&nbsp;".lang("static"), E_ICONS::SQUARE_WHITE."&nbsp;".lang("non_static"), E_COLOR::PRIMARY, E_COLOR::STANDARD, E_ENABLED::NO, E_INLINE::YES);
	$fi_static = new HTML_FormItem("", "fi_role_static", "is_static", array(), E_REQUIRED::NO);
	$fi_static->setValidationState( form_error('role_static') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_static->addComponent( $toggle );
	$fi_static->setVisible(false);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$btn_submit = new HTML_Button("bt_submit", "submit_role", lang("save"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_submit->setAttributes(array("form"=>"form_role")); 
	$btn_submit->setType(E_BUTTON_TYPES::SUBMIT);
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_role"));
	$btn_reset->setType(E_BUTTON_TYPES::RESET);
	
	
	$fi_submit 	= new HTML_FormItem("", "fi_submit_role", "submit_role");
	$fi_submit->addComponent( $btn_submit );
	
	$hidden_role_id 	= new HTML_Input("i_role_id", "role_id", E_INPUTTYPE::HIDDEN, lang("role_id"), $role->role_id) ;
	$hidden_rolename 	= new HTML_Input("i_role_name_orig", "name_orig", E_INPUTTYPE::HIDDEN, lang("role_name"), $role->role_name) ;
	$hidden_is_static 	= new HTML_Input("i_role_static", "static", E_INPUTTYPE::HIDDEN, lang("static"), $role->is_static) ;
	$hidden_permission 	= new HTML_Input("i_permission", "permission", E_INPUTTYPE::HIDDEN, lang("permission"), 1) ;
	$hidden_save		= new HTML_Input("i_save", "save", E_INPUTTYPE::HIDDEN, lang("save"), 1) ;
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form_role_data = new HTML_Form("form_role_data", "form_role_data", "#", lang("data_common"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_role_data
	->addFormItem($fi_rolename)
	->addFormItem($fi_roledesc)
	->addFormItem($fi_static)
	->addFormItem($hidden_role_id)
	->addFormItem($hidden_rolename)
	->addFormItem($hidden_is_static)
	->addFormItem($hidden_save);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the rights form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$role_rights = array();
	if (isset($data["role_rights"]) && is_array($data["role_rights"]))
	{
		foreach ($data["role_rights"] as $key => $right_id) {
			$role_rights[$right_id] = $right_id;
		}
	}
	
	$toggle_all	= "";
	$rights_li 	= "";
	if (isset($data["available_rights"]))
	{
		$toggle_all = new HTML_Toggle("toggle_all_rights", "toggle_all_rights", E_SELECTED::NO, lang("check_all"), 2, E_SIZES::SM, E_ICONS::CHECK_SQUARE_WHITE, E_ICONS::SQUARE_WHITE, E_COLOR::PRIMARY, E_COLOR::STANDARD, E_ENABLED::YES, E_INLINE::YES);
	
		$disabled = "";
		if ($role->is_static){
			$disabled = "disabled";
		}
			
		$rights_li  .= '<ul id="checked-list-box" name="roles" '.$disabled.' class="list-group checked-list-box '.$disabled.'">';
		foreach ($data["available_rights"] as $key => $value)
		{
			$checked = (array_key_exists($value->right_id, $role_rights) ? E_CHECKED::YES : E_CHECKED::NO);
	
			$cb = new HTML_Checkbox("cb_".$value->right_id, "right[]", lang($value->right_name), $checked, $value->right_id, ($role->is_static ? E_ENABLED::YES:E_ENABLED::YES), E_INLINE::NO, E_VISIBLE::YES);
	
			$rights_li .=
			'<li id="'.$value->right_id.'" class="list-group-item">
					'.$cb->generateHTML().'
				</li>';
		}
		$rights_li  .= '</ul>';
	}
	$fi_toggle_all = new HTML_FormItem("", "fi_toggle_all_rights", "toggle_all_rights");
	$fi_toggle_all->addComponent($toggle_all);
	
	$fi_rights = new HTML_FormItem(lang("rights"), "fi_rights", "rights", array(), E_REQUIRED::YES);
	$fi_rights->setValidationState( form_error('rights') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_rights->addComponent($rights_li);
	
	$form_rights = new HTML_Form("form_role_rights", "form_role_rights", "#", lang("assigned_rights"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form_rights
	->addFormItem($fi_toggle_all)
	->addFormItem($fi_rights);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form = new HTML_Form("form_role", "form_role", base_url('admin/roles/').($role->role_id == '' ? 'create':'edit'), "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	//$form->setAttributes(array("enctype"=>"multipart/form-data"));
	$form->addFormItem(
		$page_alerts.' 
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">'.
			$form_role_data->generateHTML(true).'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-8">'.
			$form_rights->generateHTML(true).'
		</div>'
	);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_roles", ($role->role_id != "" ? lang("role_edit"):lang("role_create")) , "", date("d.m.Y"), E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
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
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<?php echo $form->generateHTML(); ?>
	</div>
</div>

