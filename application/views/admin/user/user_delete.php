<?php
	$page_alerts 	= buildPageAlerts($error, $success, $warning, $info);
	
	$confirm = "";
	if (isset($data["confirmed"]) && $data["confirmed"] == 0 && $error == "")
	{
		$btn_delete 	= new HTML_Button("remove_user", "confirmed", ucfirst(lang("delete")), E_COLOR::DANGER, E_SIZES::STANDARD, E_ICONS::TRASH_WHITE);
		$btn_delete->setAttributes(array("form"=>"form_delete_user"))->setValue(1);
		$btn_delete->setType(E_BUTTON_TYPES::SUBMIT);
		
		$btn_cancel 	= new HTML_Button("cancel", "cancel", lang("cancel"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::CANCEL);
		$btn_cancel->setAnchor(base_url("admin/users"));
		
		$fi_username 	= new HTML_FormItem(lang("username").":", "fi_username", "username", HTML_FormItem::buildFormControl($data["user"]->username));
		$fi_last_login	= new HTML_FormItem(lang("last_login").":", "fi_last_login", "last_login", HTML_FormItem::buildFormControl(format_timestamp2datetime($data["user"]->last_login)));
		
		$form = new HTML_Form("form_delete_user", "form_delete_user", "#");
		$form
		->addFormItem($fi_username->generateHTML(true))
		->addFormItem($fi_last_login->generateHTML(true))
		;
		
		$dlg_confirm 	= new HTML_Dialog("dlg_confirm_delete", "dlg_confirm_delete", lang("user_sure_delete"), $form->generateHTML(), $btn_cancel->generateHTML()."&nbsp;".$btn_delete->generateHTML(), E_COLOR::DANGER, E_VISIBLE::YES );
		$dlg_confirm->setDismissable(E_DISMISSABLE::NO);
		
		$confirm = $dlg_confirm->generateHTML(false);
	}
	
?>
<div class="row">
	<div class="col-xs-12">
		<?php echo $page_alerts . $confirm;?>
	</div>
</div>