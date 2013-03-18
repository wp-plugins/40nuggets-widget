<?php
	$plugin = new Fortynuggets_Plugin ();	
	$email = get_option('admin_email');

	if( isset($GLOBALS['MY_REQUEST']['redeem-account']) ) {
		//login
		$password = $GLOBALS['MY_REQUEST']["password"];
		
		if ($plugin->login($email, $password)){
			echo "
			<script type='text/javascript'>
				window.location = '?page=40Nuggets';
			</script>";
			exit;
		}else{
			echo "<div id='message' class='error'>
				<p align='center'><strong>Login Failed</strong></p>
				</div>";
		}
	}else if( isset($_GET['reset']) ) {
		//reset password
		$client["client"] = array("email" => $email);
		$data_string = json_encode($client);  

		$response = $plugin->apiCall("forgot_password", "POST", $data_string);
		$plugin->show_response ($response, "We've sent an e-mail with further instructions to $email.");
	}
?>

    <div class="wrap">
	<form method="POST" action="">
    <table class="form-table">
      <tbody>
		<tr><th><h3>Redeem your Account</h3></th><tr>
        <tr valign="top">
          <th scope="row">
            Registered E-mail
          </th>
          <td>
            <?php echo $email;?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="password">Password</label>
          </th>
          <td>
            <input name="password" type="password" id="password" value="" class="regular-text code" />
			<br/><a href="?page=<?php echo $_GET["page"];?>&reset=true">Forgot your password?</a>
          </td>
        </tr>
      </tbody>
    </table>
	
	<p class="submit">
		<input type="hidden" name="redeem-account" />
		<input class="button-primary" type="submit" name="login" value=" <?php _e( 'Redeem' ); ?> " />
	</p>
	</form>
	</div>
