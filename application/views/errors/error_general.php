<?php if (ENVIRONMENT == E_ENVIRONMENT::DEVELOPMENT): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php //echo buildPageAlerts($error, $success, $warning, $info); ?>
		</div>
	</div>
<?php endif; 

?>
<div class="row">
	<div class="col-xs-12">
		<div class="jumbotron">
			<h1><?php echo lang("error") ?></h1>
  			<p><?php echo $error; ?></p>
  
  			<p class="pull-right"><a class="btn btn-primary btn-lg" href="<?php echo base_url("contact/found_a_bug")?>" role="button"><?php echo lang("found_a_bug") ?></a></p>
			<br><br>
		</div>
	</div>
</div>