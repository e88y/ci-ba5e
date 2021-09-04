<?php 
	
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<p><?php echo $page_alerts; ?></p>
		<div class="jumbotron">
			<h1>Logout abgeschlossen</h1>
			<p>Sie wurden erfolgreich abgemeldet. Sie waren <?php echo $data["online_time"]; ?> Minuten bei uns.</p>
			<p>
				<a class="btn btn-primary btn-lg" href="<?php echo base_url("admin/login"); ?>">Hier gehts's zur Anmeldung </a>
			</p>
		</div>
	</div>
</div>