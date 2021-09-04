<?php
	$page_alerts 	= buildPageAlerts($error, $success, $warning, $info);

	$btn_group = new HTML_ButtonGroup("btngrp_locales");
	foreach ($data["available_locales"] as $locale_code => $locale) {
		
		$button = new HTML_Button("btn_locale_".$locale_code, "locale", $locale_code);
		$button->anchor = base_url("root/locales/show/".$locale_code);
		$button->addAttribute("locale_code", $locale_code);
		if ($locale_code == $data["selected_locale"] ){
			$button->addClass("active");
		}
		$btn_group->addButton($button);
	}
	$fi_locales = new HTML_FormItem(lang("switch_locale"), "fi_selected_locale", "locale", array($btn_group), E_REQUIRED::NO, array(4, 8));
	
	
	$form_left = new HTML_Form("form_locale", "form_locale", "#", lang("filter"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_left
	->addFormItem($fi_locales)
	;
	
	$btn_new = new HTML_Button("btn_new", "btn_new", lang("locale_create"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::PLUS, "left", E_VISIBLE::YES, E_ENABLED::YES);
	$btn_new->anchor = base_url("root/locales/create/".$data["selected_locale"]);
	
	$btn_generate 		= new HTML_Button("btn_generate_locale", "locale_code", ' ('.$data["selected_locale"].')', E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::FLAG, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array("form"=>"form_locale_actions"));
	$btn_generate->setType(E_INPUTTYPE::SUBMIT)->setValue($data["selected_locale"]);
	
	$btn_generate_all 	= new HTML_Button("btn_generate_locales", "locale_code", lang("all"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::FLAG_CHECKERED, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array("form"=>"form_locale_actions"));
	$btn_generate_all->setType(E_INPUTTYPE::SUBMIT)->setValue("ALL");

	$fi_generateFile = new HTML_FormItem(lang("generate_locale"), "fi_generate_locale", "btn_generate_locale", array(), E_REQUIRED::NO, array(4, 8));
	$fi_generateFile->addComponent( $btn_generate->generateHTML()."&nbsp;".$btn_generate_all->generateHTML());
	
	$fi_create_new = new HTML_FormItem(lang("new"), "fi_create_new", "btn_new", array(), E_REQUIRED::NO, array(4, 8));
	$fi_create_new->addComponent( $btn_new->generateHTML());
	
	
	$form_right = new HTML_Form("form_locale_actions", "form_locale_actions", base_url("root/locales/generate"), lang("actions"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_right
	->addFormItem($fi_generateFile)
	->addFormItem($fi_create_new)
	;
	
	$form_row = 
	'<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">'.
			$form_left->generateHTML().'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">'.
			$form_right->generateHTML().'
		</div>
	</div>';
	
	
	
	$tbl 			= new HTML_Datatable("tbl_locales", $data["table_columns"], $data["table_data"]);
	$pnl 			= new HTML_Panel("pnl_locales", lang("locale"), $page_alerts.$tbl->generateHTML());
?>
<div class="row button-row">
	<div class="col-xs-12 ">
		<?php 
			echo $form_row."&nbsp;";
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php echo $tbl->generateHTML();?>
	</div>
</div> 
<script>
	var tbl_columns_locales = <?php echo json_encode($data["table_columns"]); ?>;
	var selected_locale		= "<?php echo $data["selected_locale"]; ?>";
</script>