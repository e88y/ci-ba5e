<?php
$page_alerts = buildPageAlerts($error, $success, $warning, $info);

$confirm = "";
if (isset($data["confirmed"]) && $data["confirmed"] == 0 && $error == "")
{
	$btn_delete = new HTML_Button("remove" . $data["role"]->role_id, "confirmed", ucfirst(lang("delete")), E_COLOR::DANGER, E_SIZES::STANDARD, E_ICONS::TRASH_WHITE);
	$btn_delete->setAttributes(array(
		"form" => "form_delete_role",
		"value" => true
	))->setValue(1);
	$btn_delete->setType(E_BUTTON_TYPES::SUBMIT);
	
	$btn_cancel = new HTML_Button("cancel", "cancel", lang("cancel"), E_COLOR::STANDARD, E_SIZES::STANDARD, E_ICONS::CANCEL);
	$btn_cancel->setAnchor(base_url("admin/roles"));
	
	$fi_name = new HTML_FormItem(lang("role_name") . ":", "fi_rolename", "rolename", HTML_FormItem::buildFormControl($data["role"]->role_name));
	
	$form = new HTML_Form("form_delete_role", "form_delete_role", "#");
	$form->addFormItem($fi_name->generateHTML(true))
		->addFormItem(new HTML_Input("role_id", "role_id", E_INPUTTYPE::HIDDEN, "", $data["role"]->role_id));
	
	$dlg_confirm = new HTML_Dialog("dlg_confirm_delete", "dlg_confirm_delete", lang("role_sure_delete"), $form->generateHTML(false), $btn_cancel->generateHTML() . "&nbsp;" . $btn_delete->generateHTML(), E_COLOR::DANGER, E_VISIBLE::YES);
	$dlg_confirm->setDismissable(E_DISMISSABLE::NO);
	
	$confirm = $dlg_confirm->generateHTML(false);
}

?>
<div class="row">
	<div class="col-xs-12">
		<?php echo $page_alerts.$confirm;?>
	</div>
</div>

<div class="row">
	<div class="hidden-xs col-sm-2 col-md-4"></div>
	<div class="col-xs-12 col-sm-8 col-md-4"></div>
	<div class="hidden-xs col-sm-2 col-md-4"></div>
</div>