<?php if (ENVIRONMENT == E_ENVIRONMENT::DEVELOPMENT): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php echo buildPageAlerts($error, $success, $warning, $info); ?>
		</div>
	</div>
<?php endif; 

?>
<div class="row">
	<div class="col-xs-12">
		<div class="jumbotron">
			
			<?php echo $error; ?>
  
  			<p class="pull-right"><a class="btn btn-primary btn-lg" href="<?php echo base_url("home/contact")?>" role="button"><?php echo lang("found_a_bug") ?></a></p>
			<br><br>
		</div>
	</div>
</div>