<?php 

	$page_alerts = buildPageAlerts($error, $success, $warning, $info);
	
	$hint_contact_us = new HTML_Alert("msg_contact_us", "", lang("msg_contact_us"), E_COLOR::INFO);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create value object
	$msg = new T_Pseudo($data["message"]);
	
	$hint = "";
	if ($data["message_sent"] == 1){
		$hint = new HTML_Alert("msg_form_sent", "msg_form_sent", lang("msg_contact_mail_has_been_sent"));
		$hint = $hint->generateHTML();
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the contact form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$fi_email = new HTML_FormItem(lang("email"), "fi_email", "email", array(), E_REQUIRED::YES, array(2, 10));
	$fi_email->setValidationState( form_error('email') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_email->addComponent(new HTML_Input("i_email", "email", E_INPUTTYPE::TEXT, lang("email"), $data["message"]["email"], "", "", E_ENABLED::NO) );
	
	$fi_subject = new HTML_FormItem(lang("subject"), "fi_subject", "subject", array(), E_REQUIRED::YES, array(2, 10));
	$fi_subject->setValidationState( form_error('subject') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_subject->addComponent(new HTML_Input("i_subject", "subject", E_INPUTTYPE::TEXT, lang("subject"), $data["message"]["subject"]) );
	
	$fi_message = new HTML_FormItem(lang("message"), "fi_message", "message", array(), E_REQUIRED::YES, array(2, 10));
	$fi_message->setValidationState( form_error('message') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_message->addComponent(new HTML_TextArea("i_message", "message", $data["message"]["message"], lang("message"), E_VISIBLE::YES, E_ENABLED::YES, array(), array(),array() ) );
	
	$btn_submit = new HTML_Button("bt_submit", "submit", lang("submit"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array("btn-block"), array());
	$btn_submit->setType(E_BUTTON_TYPES::SUBMIT)->setValue(1)->setAttributes(array("form"=>"form_contact")); // since we place this button outside the form
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_contact"))->setValue(1)->setType(E_BUTTON_TYPES::RESET);
	
	$fi_submit 	= new HTML_FormItem("", "fi_submit", "submit", array(), E_REQUIRED::NO, array(2, 10));
	$fi_submit->addComponent( $btn_submit );
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form_contact = new HTML_Form("form_contact_data", "form_contact_data", "#", "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_contact
	->addFormItem($fi_email)
	->addFormItem($fi_subject)
	->addFormItem($fi_message)
	->addFormItem($fi_submit);
	;
	
	$form = new HTML_Form("form_contact", "form_contact", "#", "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form->addFormItem(
			$page_alerts.'
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">'.
			$hint_contact_us->generateHTML().'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">'.
			$form_contact->generateHTML(true).'
		</div>'
	);
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_contact", E_ICONS::ENVELOPE_WHITE."&nbsp;".lang("contact"), "", "", E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
	$panel->setContent($hint . $page_alerts. $form->generateHTML());
	$panel->setFooter($btn_submit->generateHTML()."&nbsp;".$btn_reset->generateHTML());

?>
<?php //echo $hint_contact_us->generateHTML(); ?>
<div class="row">	
	<div class="col-md-12">
		<?php echo $form->generateHTML();?>
	</div>
</div>