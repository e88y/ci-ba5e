<?php
	$page_alerts 	= buildPageAlerts($error, $success, $warning, $info);
	
	//echo nl2br(print_r($data, true));
	
	$confirm = "";
	if (isset($data["confirmed"]) && $data["confirmed"] == 0 && $error == "")
	{
		$btn_delete 	= new HTML_Button("remove".$data["client"]->client_id, "confirmed", ucfirst(lang("delete")), E_COLOR::DANGER, E_SIZES::STANDARD, E_ICONS::TRASH_WHITE);
		$btn_delete->setAttributes(array("form"=>"form_delete_client", "value"=>true))->setValue(1);
		$btn_delete->setType(E_BUTTON_TYPES::SUBMIT);
		
		$btn_cancel 	= new HTML_Button("cancel", "cancel", lang("cancel"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::CANCEL);
		$btn_cancel->setAnchor(base_url("root/clients"));
		
		$fi_name 	= new HTML_FormItem(lang("client").":", "fi_clientname", "clientname", HTML_FormItem::buildFormControl($data["client"]->client_name));
		$fi_cn		= new HTML_FormItem(lang("customer_number").":", "fi_customer_number", "customer_number", HTML_FormItem::buildFormControl($data["client"]->customer_number));
		
		$form = new HTML_Form("form_delete_client", "form_delete_client", "#");
		$form
		->addFormItem($fi_name->generateHTML(true))
		->addFormItem($fi_cn->generateHTML(true))
		;
		
		$dlg_confirm 	= new HTML_Dialog("dlg_confirm_delete", "dlg_confirm_delete", lang("client_sure_delete"), $form->generateHTML(), $btn_cancel->generateHTML()."&nbsp;".$btn_delete->generateHTML(), E_COLOR::DANGER, E_VISIBLE::YES );
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
	<div class="hidden-xs col-sm-2 col-md-4">
	</div>
	<div class="col-xs-12 col-sm-8 col-md-4">
	</div>
	<div class="hidden-xs col-sm-2 col-md-4">
	</div>
</div>