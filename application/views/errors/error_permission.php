<?php if (ENVIRONMENT == E_ENVIRONMENT::DEVELOPMENT): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php // echo buildPageAlerts($error, $success, $warning, $info); ?>
		</div>
	</div>
<?php endif; ?>
<?php 
?>
<div class="row">
	<div class="col-xs-12">
		<div class="jumbotron">
			<h1><?php echo lang("permission_denied") ?></h1>
  			<p><?php echo lang("msg_no_permissions"); ?></p>
  
  			
			
			<?php if (ENVIRONMENT == E_ENVIRONMENT::DEVELOPMENT): ?>
			<small><?php echo lang("you_have_the_following_permissions").":"; ?>
			
			<ul>
				<?php 
				foreach ($this->session->userdata["permissions"] as $permission => $permission_object) {
					echo "<li>".lang($permission_object->right_name)."</li>";
				}
				?>
			</ul>
			</small>
			<?php endif; ?>
			
			
			<p class="pull-right"><a class="btn btn-primary btn-lg" href="<?php echo base_url("contact/found_a_bug")?>" role="button"><?php echo lang("found_a_bug") ?></a></p>
			<br><br>
		</div>
	</div>
</div>


