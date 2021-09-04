<?php 
	
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);

	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create role object	
	$locale = new T_Locale($data["locale"]);

	//echome($data["locale"]);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the role form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$locale_code 	= new HTML_Input("i_locale_code_disabled", "locale_code_disabled", E_INPUTTYPE::TEXT, lang("locale_code"), $locale->locale_code, "", "", E_ENABLED::NO);
	$fi_locale_code = new HTML_FormItem(lang("locale_code"), "fi_locale_code_disabled", "locale_code", array(), E_REQUIRED::YES);
	$fi_locale_code->setValidationState( form_error('locale_code') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_locale_code->addComponent( $locale_code );
	
	$locale_group 		= new HTML_Input("i_locale_group", "locale_group", E_INPUTTYPE::TEXT, lang("group"), $locale->group_token);
	$fi_locale_group 	= new HTML_FormItem(lang("group"), "fi_locale_group", "locale_group", array(), E_REQUIRED::YES);
	$fi_locale_group->setValidationState( form_error('locale_group') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_locale_group->addComponent( $locale_group );
	
	$locale_token		= new HTML_Input("i_locale_token", "locale_token", E_INPUTTYPE::TEXT, lang("id"), $locale->locale_id);
	$fi_locale_token 	= new HTML_FormItem(lang("id"), "fi_locale_token", "locale_token", array(), E_REQUIRED::YES);
	$fi_locale_token->setValidationState( form_error('locale_token') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_locale_token->addComponent( $locale_token );
	
	$locale_text	= new HTML_TextArea("i_locale_text", "locale_text", $locale->text, lang("text"), E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$fi_locale_text	= new HTML_FormItem(lang("text"), "fi_locale_text", "locale_text", array(), E_REQUIRED::YES);
	$fi_locale_text->setValidationState( form_error('locale_text') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_locale_text->addComponent( $locale_text );
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$btn_submit = new HTML_Button("bt_submit", "submit_locale", lang("save"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_submit->setAttributes(array("form"=>"form_locale"))->setValue(1)->setType(E_BUTTON_TYPES::SUBMIT);
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_locale"))->setType(E_BUTTON_TYPES::RESET);
	
	$hidden_save		= new HTML_Input("i_save", "save", E_INPUTTYPE::HIDDEN, lang("save"), 1) ;
	$hidden_locale_code	= new HTML_Input("i_locale_code", "locale_code", E_INPUTTYPE::HIDDEN, lang("locale_code"), $locale->locale_code) ;
	$hidden_locale_id 	= new HTML_Input("i_locale_id", "locale_id_orig", E_INPUTTYPE::HIDDEN, lang("locale_id"), $locale->locale_id) ;
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form_locale = new HTML_Form("form_locale", "form_locale", base_url('root/locales/').($locale->locale_id == '' ? 'create':'edit'), lang("data_common"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_locale
	->addFormItem($fi_locale_code)
	->addFormItem($fi_locale_group)
	->addFormItem($fi_locale_token)
	->addFormItem($fi_locale_text)
	->addFormItem($hidden_save)
	->addFormItem($hidden_locale_id)
	->addFormItem($hidden_locale_code)
	;

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: output panel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_locales", ($locale->locale_id != "" ? lang("locale_edit"):lang("locale_create")) , "", date("d.m.Y"), E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
	$panel->setContent($page_alerts.$form_locale->generateHTML());
	$panel->setFooter($btn_submit->generateHTML().'&nbsp;'.$btn_reset->generateHTML());
	
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
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
		<?php echo $form_locale->generateHTML(); ?>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
		
	</div>
</div>
<div class="row">
	<?php // echo nl2br(print_r($data, true));?>
</div>
