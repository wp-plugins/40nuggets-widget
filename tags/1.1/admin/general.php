<?php 
	$plugin = new Fortynuggets_Plugin ();	
	
	require_once(dirname(__FILE__) . '/facebook_like.php'); 

	// Create a header in the default WordPress 'wrap' container
	echo '
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>40Nuggets General Settings</h2>
			<br class="clear">

		</div>';

	if( isset($_POST['settings-updated']) ) {
		$_POST["image"] = $_POST["upload_image"];
		$json["client"] = $_POST;
		$data_string = json_encode($json); 
		$options = $plugin->get_options();

		$response = $plugin->apiCall("clients/{$options->id}", "PUT", $data_string);
		$plugin->show_response ($response);
		if (!isset($response->error)){
			//save local settings
			$options = $plugin->get_options();
			$options->site_track = isset($_POST["site_track"]) ? $_POST["site_track"] : "";
			$options->freshness = isset($_POST["freshness"]) ? $_POST["freshness"] : "";
			$plugin->save_options($options);
		}
	}
	$response = $plugin->apiCall("clients/me");
	$me = $response->client;
	
	$options = $plugin->get_options();
	$site_track = isset ($options->site_track) ? $options->site_track : "on";
	$freshness = isset ($options->freshness) ? $options->freshness : "on";
?>

	<style>
	.blog_ico {width:20px; height:20px;}
	</style>

    <div class="wrap">
	<form method="POST" action="">
    <table class="form-table">
      <tbody>
		<tr><th><h3>Account Settings</h3></th><tr>
        <tr valign="top">
          <th scope="row">
            Client ID
          </th>
          <td>
			<?php echo $me->api_key;?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            Registered E-mail
          </th>
          <td>
			<?php echo $me->email;?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="subscription_type">Account Type</label>
          </th>
          <td>
			<select name="subscription_type" id="subscription_type">
			  <option value="1">Free - Standard</option>
			</select> 
			<a href="http://40nuggets.com/dashboard/pricing.php#Standard" target="_blank">(?)</a>
          </td>
        </tr>
<!--
		<tr><th><h3>Connect Your Accounts</h3></th><tr>
        <tr valign="top">
          <th scope="row">
            <label for="rss">RSS Feed</label>
          </th>
          <td>
            <input name="rss" type="text" id="rss" value="<?php echo $me->rss;?>"
            class="regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="facebook">Facebook Page</label>
          </th>
          <td>
            <input name="facebook" type="text" id="facebook" value="<?php echo $me->facebook;?>" class="regular-text code" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="twitter">Twitter Handle</label>
          </th>
          <td>
            @<input name="twitter" type="text" id="twitter" value="<?php echo $me->twitter;?>" class="regular-text code" style="width: 24em;"/>
          </td>
        </tr>
-->
		<tr><th><h3>eNewsletter</h3></th><tr>
        <tr valign="top">
          <th scope="row">
            <label for="title">Header</label>
          </th>
          <td>
            <input name="title" type="text" id="title" value="<?php echo $me->client_title;?>" class="regular-text" />
            <p class="description">This name will appear on the top of your newsletters as well as in the email subject line</p>
          </td>
        </tr>
		<tr valign="top">
			<th scope="row">
				<label for="upload_image">Banner Image</label>
			</th>
			<td>
				<input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo $me->image;?>" />
				<input id="upload_image_button" type="button" value="Select Image" />
				<p class="description">Enter a URL or upload the image you'd like to use from your computer</p>
			</td>
		</tr>        
        <tr valign="top">
          <th scope="row">
            <label for="from_email">Sender Email</label>
          </th>
          <td>
            <input disabled name="from_email" type="text" id="from_email" value="<?php if ($me->is_use_email) $me->from_email; else echo "content@40nuggets.com";?>" class="regular-text ltr" />
            <p class="description">This is the email address from which your contacts will receive your newsletter</p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="frequency">Newsletter Delivery Rate</label>
          </th>
          <td>
            <select name="frequency" id="frequency">
              <option value="1"<?php if ($me->frequency == 1) echo ' selected="selected"';?>>Optimized</option>
              <option value="2"<?php if ($me->frequency == 2) echo ' selected="selected"';?>>Twice a Week</option>
              <option value="3"<?php if ($me->frequency == 3) echo ' selected="selected"';?>>Once a Week</option>
              <option value="4"<?php if ($me->frequency == 4) echo ' selected="selected"';?>>Twice a Month</option>
              <option value="5"<?php if ($me->frequency == 5) echo ' selected="selected"';?>>Once a Month</option>
            </select>
            <p class="description">This is how often your audience will receive your newsletter
			<br/>**Optimized delivery is determined by our Brain and is based on analyzed individual contact behavior**</p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="site_track">Adapt to user behaviour on site</label>
          </th>
          <td>
            <p class="description">
				<input type="checkbox" name="site_track" id="site_track"<?php if ($site_track) echo ' checked="checked"';?>>
				Allow 40Nuggets to learn your site visitor behavior and select the most relevant content for eNewsletters (recommended)
			</p>
		</td>
        </tr>
        <tr>
          <th scope="row">
            <label for="freshness">Promote newly published content</label>
          </th>
          <td>
            <p class="description">
            <input type="checkbox" name="freshness" id="freshness"<?php if ($freshness) echo ' checked="checked"';?>>
			Tells 40Nuggets to give priority to newly published content when creating eNewsletters
  			</p>
        </td>
        </tr>
      </tbody>
    </table>
	
	<p class="submit">
		<input type="hidden" name="settings-updated" />
		<input class="button-primary" type="submit" name="Submit" value=" <?php _e( 'Save Settings' ); ?> " />
	</p>
	</form>
	</div>
  </body>
</html>
