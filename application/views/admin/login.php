<?php 
	$page_alerts 	= buildPageAlerts($error, $success, $warning, $info);
?>
<div class="row hidden-xs"><div style="height: 100px;"></div></div>

<div class="row">
	<div class="col-lg-4 col-md-2 col-sm-2"></div>

	<div class="col-lg-4 col-md-8 col-sm-8">
		
		<div class="panel panel-default">
			
			<div class="panel-body">
			
				<?php echo form_open('admin/login', array('id'=>'form_login', 'name'=>'form_login', 'class'=>'form form-horizontal'));?>
			
				<legend><?php echo lang("title_login_form")?></legend> 
				<p><?php echo $page_alerts;?></p>
						
				<div id="lbl_username" class="form-group required <?php echo (form_error('username') != "" ? E_VALIDATION_STATES::HAS_ERROR:'');?>">
					<div class="col-xs-12">
						<div class="input-group">
							<span class="input-group-addon input-group-prepend"><?php echo E_ICONS::USER_CIRCLE_WHITE; ?></span>
							<input id="i_username" name="username" value="" type="text" class="form-control" placeholder="<?php echo lang("username")?>" aria-describedby="basic-addon2">
						</div>
					</div>
				</div>
				<div id="lbl_password" class="form-group required <?php echo (form_error('password') != "" ? E_VALIDATION_STATES::HAS_ERROR:'');?>">
					<div class="col-xs-12">
						<div class="input-group">
							<span class="input-group-addon input-group-prepend"><?php echo E_ICONS::KEY; ?></span>
							<input id="i_password" name="password" value="" type="password" class="form-control" placeholder="<?php echo lang("password")?>" aria-describedby="basic-addon2">
						</div>
					</div>
				</div>
				
				<div id="lbl_submit" class="form-group">
					<div class="col-xs-12">
						<button id="bt_login" name="authenticate" type="submit" value="1" class="btn btn-primary btn-block">&nbsp;<?php echo lang("log_in");?></button>
					</div>
				</div>
						
				<?php echo form_close();?>
				
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-md-2 col-sm-2">
		<?php 
			//echome($this->session, true);
		?>
	
	</div>
</div>