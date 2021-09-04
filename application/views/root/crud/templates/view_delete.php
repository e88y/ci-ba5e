<?php 

	$str = '<?php
	$page_alerts 	= buildPageAlerts($error, $success, $warning, $info);
	
	$confirm = "";
	if (isset($data["confirmed"]) && $data["confirmed"] == 0 && $error == "")
	{
		$btn_delete 	= new HTML_Button("remove".$data["'.$classname.'"]->'.$classname.'_id, "confirmed", ucfirst(lang("delete")), E_COLOR::DANGER, E_SIZES::STANDARD, E_ICONS::TRASH_WHITE);
		$btn_delete->setAttributes(array("form"=>"form_delete_'.$classname.'", "value"=>true))->setValue(1)->setType(E_BUTTON_TYPES::SUBMIT);
		
		$btn_cancel 	= new HTML_Button("cancel", "cancel", lang("cancel"), E_COLOR::STANDARD, E_SIZES::STANDARD, E_ICONS::CANCEL);
		$btn_cancel->setAnchor(base_url("admin/'.$classname.'"));
		
		$fi_name = new HTML_FormItem(lang("'.$db_name_field.'").":", "fi_'.$db_name_field.'", "'.$db_name_field.'", HTML_FormItem::buildFormControl($data["'.$classname.'"]->'.$db_name_field.'));
		
		$form = new HTML_Form("form_delete_'.$classname.'", "form_delete_'.$classname.'", "#");
		$form
		->addFormItem($fi_name->generateHTML(true))
		->addFormItem(new HTML_Input("'.$classname.'_id", "'.$classname.'_id", E_INPUTTYPE::HIDDEN, "", $data["'.$classname.'"]->'.$classname.'_id ))
		;
		
		$dlg_confirm 	= new HTML_Dialog("dlg_confirm_'.$classname.'", "dlg_confirm_'.$classname.'", lang("'.$classname.'_sure_delete"), $form->generateHTML(false), $btn_cancel->generateHTML()."&nbsp;".$btn_delete->generateHTML(), E_COLOR::DANGER, E_VISIBLE::YES );
		$dlg_confirm->setDismissable(E_DISMISSABLE::NO);
		
		$confirm = $dlg_confirm->generateHTML(false);
	}
	
?>
<div class="row">
	<div class="col-xs-12">
		<?php echo $page_alerts.$confirm;?>
	</div>
</div>';

	echo $str;
?>