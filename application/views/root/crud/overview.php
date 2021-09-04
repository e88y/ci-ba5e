<?php 
	//echome($data["tables"]);

	$page_alerts = buildPageAlerts($error, $success, $warning, $info);

	$tables = array();
	foreach ($data["tables"] as $table) {
		$tables[] = array("id"=>$table, "label"=>$table);
	};
	
	$fi_tables 		= new HTML_FormItem(lang("table"), "fi_table", "table", array(), E_REQUIRED::YES);
	$fi_tables->setValidationState( form_error('table') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_tables->addComponent(new HTML_Select("i_table", "table", HTML_Select::buildOptions($tables, "id", "label", $data["selected_table"], lang("no_selection"))));
	
	$rb_target_a 	= new HTML_Radio("i_target_a", "target_folder", "app...", E_CHECKED::NO, "app", E_ENABLED::YES);
	$rb_target_o 	= new HTML_Radio("i_target_o", "target_folder", "output/...", E_CHECKED::YES, "out");
	$fi_target		= new HTML_FormItem(lang("target_folder"), "fi_target_folder", "target_folder", array(), E_REQUIRED::YES);
	$fi_target->setValidationState( form_error('target_folder') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_target->addComponent($rb_target_a)->addComponent($rb_target_o);
	
	$btn_load_table = new HTML_Button("bt_analize", "analize", lang("analize"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::CODE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array("btn-block"), array());
	$btn_load_table->setAttributes(array("form"=>"form_crud"))->setValue(1)->setType(E_BUTTON_TYPES::SUBMIT);
	
	$fi_classname 	= new HTML_FormItem(lang("classname"), "fi_classname", "classname", array(), E_REQUIRED::YES);
	$fi_classname->setValidationState( form_error('classname') != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);
	$fi_classname->addComponent(new HTML_Input("i_classname", "classname", E_INPUTTYPE::TEXT, lang("classname"), $data["selected_classname"]));
	
	$btn_generate = new HTML_Button("bt_generate", "generate", lang("generate"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::CODE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array("btn-block"), array());
	$btn_generate->setAttributes(array("form"=>"form_crud"))->setValue(1)->setType(E_BUTTON_TYPES::SUBMIT);
	
	$form_settings 	= new HTML_Form("form_settings", "form_settings", base_url('root/crud'), lang("config"), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_settings
	->addFormItem($fi_tables)
	->addFormItem($fi_classname)
	->addFormItem($fi_target)
	->addFormItem(new HTML_FormItem("", "fi_analize", "analize", $btn_load_table ))
	->addFormItem(new HTML_FormItem("", "fi_generate", "analize", $btn_generate ))
	;
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: right side
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$count_columns 	= count($data["selected_table_columns"]);
	$str 			= lang("table").": <b>".$data["selected_table"]."</b> ".lang("columns").": <b>".count($data["selected_table_columns"])."</b>";
	$tbl 			= new HTML_Datatable("tbl_columns", $data["table_columns"], $data["selected_table_columns"]);
	
	$form_analyze 	= new HTML_Form("frm_crud_tbl_view", "frm_crud_tbl_view", base_url('root/crud'), ($count_columns > 0 ? $str : lang("no_selection")), E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	$form_analyze->addFormItem($tbl);
	
	if (count($data["selected_table_columns"]) == 0 )
	{
		$form_analyze->setVisible(false);
		$tbl->setVisible(false);
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together for the panel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$form = new HTML_Form("form_crud", "form_crud", base_url('root/crud/'), "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array() );
	$form->addFormItem(
			$page_alerts.'
		<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
			'.$form_settings->generateHTML(true).'
		</div>
		<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
			'.$form_analyze->generateHTML(true).'
		</div>'
	);
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: output panel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_crud", lang("crud"), "", "", E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
	$panel->setContent( $form->generateHTML() );
?>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<?php echo $panel->generateHTML(); ?>
	</div>
</div>
<script>
	var tbl_columns_crud = <?php echo json_encode($data["table_columns"]); ?>;
</script>