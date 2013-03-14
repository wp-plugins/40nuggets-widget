<?php 

	$plugin = new Fortynuggets_Plugin ();		
	$nuggetId = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$url = "https://40nuggets.com/dashboard/clean_preview.php?id=$nuggetId";	
	$result = $plugin->httpCall($url, $method, $data_string);
	
 ?>
 
 <div class="wrap">
	<div id="icon-edit-pages" class="icon32"></div>
	<h2>Preview</h2>
	<br class='clear'>
	<p class="description">Heads up: this is a sample preview - not everyone will receive the exact same content as we personalize each Newsletter to every individual.</p>
	<br class='clear'>

	<?php echo $result; ?>
</div>