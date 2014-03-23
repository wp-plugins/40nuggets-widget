<?php 

	$plugin = new Fortynuggets_Plugin ();	
	
	if( isset($GLOBALS['MY_REQUEST']['login']) ) {
		echo "
			<script type='text/javascript'>
				window.location = '?page=40Nuggets-login';
			</script>";
		exit;
	}
	
	require_once(dirname(__FILE__) . '/facebook_like.php'); 

	// Create a header in the default WordPress 'wrap' container
	echo '
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>40Nuggets General Settings</h2>
			<br class="clear">

		</div>';

	$response = $plugin->apiCall("clients/me");
	$me = $response->client;

	$options = $plugin->get_options();
?>


			
			
	<style>
	.blog_ico {width:20px; height:20px;}
	</style>

<script language="javascript">
jQuery(document).ready(function($){
	
	jQuery("#from_email").change(function() {
		var email = jQuery("#from_email").val().toLowerCase();
		if (!isValidEmail(email)){
			message = "<span style='color:red;'>Invalid email</span>";
			jQuery("#verify_email").html(message);
		}
	});
	
	jQuery("#from_email").keyup(function() {
		var email = jQuery("#from_email").val().toLowerCase();
		
		var message = (email.indexOf("@40nuggets.com") > 0)? "" : "<a style='cursor: pointer;text-decoration:underline;' onclick='verify_sender_email();'>Verify this address</a>";
		if (!isValidEmail(email)) message = "";
		
		jQuery("#verify_email").html(message);
	});

});

function verify_sender_email(){
	var email = jQuery("#from_email").val();
	if (!isValidEmail(email)) return;
		
	//this is hacky! :)
	jQuery("#verify_email").append("<span class='spinner' style='display:inline;'></span>");
	jQuery("#verify_email").append("<iframe id='verify_frame' src='<?php echo plugins_url( 'verify_sender.php' , __FILE__ );?>?email=" + encodeURIComponent(email) + "' frameborder='0' height='0px' seamless></iframe>");
	jQuery(".spinner").show();
	timerId = setInterval(function () {
		response = jQuery("#verify_frame").contents().find("body").html();
			
		if (response){
			clearInterval(timerId);
			jQuery("#verify_frame").remove();
			jQuery(".spinner").hide();
			
			//show success message
			message = "<span style='color:green;'>Check your inbox for Amazon SES verification</span>";
			jQuery("#verify_email").html(message);

		}
	}, 1000);
}

function isValidEmail(email){
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;	
	return re.test(email);
}
</script>
	

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
        <tr valign="top">
          <th scope="row">
            <label>Support</label>
          </th>
          <td>
            <p class="description">
				Need help or guidance? check our <a href="http://40nuggets.com/wordpress/faq.html">Frequently Asked Questions</a>
			</p>
          </td>
        </tr>
      </tbody>
    </table>
	
	<p class="submit">
		<input type="hidden" name="login" />
		<input class="button-primary" type="submit" name="Submit" value=" <?php _e( 'Switch account' ); ?> " />
	</p>
	</form>
	</div>

	