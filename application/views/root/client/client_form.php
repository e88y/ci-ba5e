<?php 
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create client object
	$client = new T_Client($data["client"]);	

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the client form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$i_customer_number = new HTML_Input("i_customer_number", "customer_number", E_INPUTTYPE::TEXT, lang("customer_number"), $client->customer_number, "", "");
	$fi_customer_number = new HTML_FormItem(lang("customer_number"), "fi_client_id", "customer_number", array(), E_REQUIRED::YES);
	$fi_customer_number->setValidationState( form_error('customer_number') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_customer_number->addComponent($i_customer_number);
	
	
	$fi_name = new HTML_FormItem(lang("client"), "fi_name", "name", array(), E_REQUIRED::YES);
	$fi_name->setValidationState( form_error('client_name') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_name->addComponent(new HTML_Input("i_name", "name", E_INPUTTYPE::TEXT, lang("client"), $client->client_name) );
	
	$fi_desc = new HTML_FormItem(lang("desc"), "fi_client_name", "client_desc", array(), E_REQUIRED::NO);
	$fi_desc->setValidationState( form_error('desc') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_desc->addComponent(new HTML_TextArea("i_desc", "desc", $client->client_desc, lang("desc") ) );
	
	$fi_email = new HTML_FormItem(lang("email"), "fi_email", "email", array(), E_REQUIRED::YES);
	$fi_email->setValidationState( form_error('email') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_email->addComponent(new HTML_Input("i_email", "email", E_INPUTTYPE::TEXT, lang("email"), $client->client_email) );
	
	$fi_street = new HTML_FormItem(lang("street"), "fi_street", "street", array(), E_REQUIRED::YES);
	$fi_street->setValidationState( form_error('street') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_street->addComponent(new HTML_Input("i_street", "street", E_INPUTTYPE::TEXT, lang("street"), $client->client_street) );
	
	$fi_house_nr = new HTML_FormItem(lang("house_nr"), "fi_house_nr", "house_nr", array(), E_REQUIRED::YES);
	$fi_house_nr->setValidationState( form_error('house_nr') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_house_nr->addComponent(new HTML_Input("i_house_nr", "house_nr", E_INPUTTYPE::TEXT, lang("house_nr"), $client->client_house_nr) );
	
	$fi_zipcode = new HTML_FormItem(lang("zipcode"), "fi_zip", "zipcode", array(), E_REQUIRED::YES);
	$fi_zipcode->setValidationState( form_error('zipcode') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_zipcode->addComponent(new HTML_Input("i_zip", "zipcode", E_INPUTTYPE::TEXT, lang("zipcode"), $client->client_zipcode) );
	
	$fi_location = new HTML_FormItem(lang("location"), "fi_location", "location", array(), E_REQUIRED::YES);
	$fi_location->setValidationState( form_error('location') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_location->addComponent(new HTML_Input("i_location", "location", E_INPUTTYPE::TEXT, lang("location"), $client->client_location) );
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$btn_submit = new HTML_Button("bt_submit", "submit_client", lang("save"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array() );
	$btn_submit->setAttributes(array("form"=>"form_clients"))->setValue(1)->setType(E_BUTTON_TYPES::SUBMIT);
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_clients"))->setType(E_BUTTON_TYPES::RESET);
	
	$fi_submit 	= new HTML_FormItem("", "fi_submit", "submit");
	$fi_submit->addComponent($btn_submit);
	
	$hidden_client_id 		= new HTML_Input("i_client_id", "client_id", E_INPUTTYPE::HIDDEN, lang("client_id"), $client->client_id) ;
	$hidden_customer_number	= new HTML_Input("i_customer_number", "customer_number_orig", E_INPUTTYPE::HIDDEN, lang("customer_number"), $client->customer_number) ;
	$hidden_client_name 	= new HTML_Input("i_client_name_orig", "clientname_orig", E_INPUTTYPE::HIDDEN, lang("client_id"), $client->client_name) ;
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form_client = new HTML_Form("form_client_data", "form_client_data", "#", lang("data_common"));
	$form_client
	->addFormItem($fi_customer_number)
	->addFormItem($fi_name)
	->addFormItem($fi_desc)
	->addFormItem($fi_email)
	->addFormItem($fi_street)
	->addFormItem($fi_house_nr)
	->addFormItem($fi_zipcode)
	->addFormItem($fi_location)
	->addFormItem($hidden_client_id)
	->addFormItem($hidden_client_name)
	->addFormItem($hidden_customer_number)
	;
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build settings form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$fi_deleted	= new HTML_FormItem(lang("deleted"), "fi_deleted", "deleted");
	$fi_deleted->setValidationState( form_error('deleted') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_deleted->addComponent( new HTML_Toggle("i_deleted", "deleted", ($client->deleted != "1" ? E_SELECTED::NO:E_SELECTED::YES), "", 1, E_SIZES::SM, E_ICONS::DELETE, E_ICONS::CIRCLE_WHITE, E_COLOR::DANGER, E_COLOR::STANDARD) );
	
	$fi_deletedBy = new HTML_FormItem(lang("deleted_by"), "fi_deleted_by", "deleted_by", array(), E_REQUIRED::NO);
	$fi_deletedBy->setValidationState( form_error('deleted_by') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_deletedBy->addComponent(new HTML_Input("i_deleted_by", "deleted_by", E_INPUTTYPE::TEXT, lang("deleted_by"), $client->deleted_by) );
	
	$fi_deletedAt = new HTML_FormItem(lang("deleted_at"), "fi_deleted_at", "deleted_at", array(), E_REQUIRED::NO);
	$fi_deletedAt->setValidationState( form_error('deleted_at') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_deletedAt->addComponent(new HTML_Input("i_deleted_at", "deleted_at", E_INPUTTYPE::TEXT, lang("deleted_at"), $client->deleted_at) );
	
	$all_themes = E_THEMES::getConstants();
	$themes		= array();
	foreach ($all_themes as $key => $value) {
		$themes[] = array(
				"label"=>$key,
				"key"=>$value
		);
	}
	$fi_theme = new HTML_FormItem(lang("theme"), "fi_theme", "theme");
	$fi_theme->setValidationState( form_error('theme') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE );
	$fi_theme->addComponent( new HTML_Select("i_theme", "theme", HTML_Select::buildOptions($themes, "key", "label", $client->client_theme, "all", false), false, "", E_VISIBLE::YES ) );
	
	$form_settings = new HTML_Form("form_client_settings", "form_client_settings", "#", lang("settings"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form_settings
	->addFormItem($fi_theme)
	->addFormItem($fi_deleted);
	
	if ($client->deleted == 1)
	{
		$form_settings
		->addFormItem($fi_deletedAt)
		->addFormItem($fi_deletedBy);
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the logo-upload form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$src_logo = HTML_Image::generatePlaceholderSVG(120, 120, lang("no_logo"), "#CFCFCF", 12);
	if ($client->client_logo != "")
	{
		$path		= $this->config->item("root_path") . $this->config->item("upload_folder") . "/client_logos/". $client->client_id ."/";
		$src_logo	= HTML_Image::generateDataURIFromImage(append_to_filename($path, "_thumb"));
	}
	
	$img 			= new HTML_Image("img_logo", "img_logo", $src_logo, "", 120, 120);
	$hidden_logo	= new HTML_Input("i_logo", "logo", E_INPUTTYPE::HIDDEN, lang("logo"), $client->client_logo) ;
	
	$fi_logo = new HTML_FormItem(lang("logo"), "fi_logo", "logo");
	$fi_logo->addComponent( $img );
	if ($data["js_enabled"] == 1){
		$fi_logo->setVisible(false);
	}
	
	$fi_logo_upload = new HTML_FormItem(lang("client_logo_upload"), "fi_logo_ul", "upload[]");
	$fi_logo_upload->addComponent('<input id="input_upload" name="upload[]" class="btn btn-block btn-default" type="file">' );
	
	$form_upload_logo = new HTML_Form("form_upload_logo", "form_upload_logo", "#", lang("logo"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_upload_logo
	->addFormItem($fi_logo)
	->addFormItem('<input id="input_upload" name="upload[]" class="btn btn-block btn-default" type="file">')
	->addFormItem($hidden_logo);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$hidden_save = new HTML_Input("i_save", "save", E_INPUTTYPE::HIDDEN, lang("save"), 1) ;
	
	$form = new HTML_Form("form_clients", "form_client_complete", base_url('root/clients/').($client->client_id == '' ? 'create':'edit'), "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form->addFormItem($hidden_save);
	$form->addFormItem(
		$page_alerts.'
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			'.$form_client->generateHTML(true).'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			'.$form_settings->generateHTML(true).'
			'.$form_upload_logo->generateHTML(true).'
		</div>'
	);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_client", ($client->client_id != "" ? lang("client_edit"):lang("client_create")) , "", date("d.m.Y"), E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
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
	
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-2">
		
	</div>
</div>
