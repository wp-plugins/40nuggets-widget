<?php
	$plugin = new Fortynuggets_Plugin ();	
	$email = $GLOBALS['MY_REQUEST']["email"];

	if( isset($GLOBALS['MY_REQUEST']['create-account']) ) {
		$plugin->create_client();
		echo "
			<script type='text/javascript'>
				window.location = '?page=40Nuggets';
			</script>";
		exit;
	}else if( isset($GLOBALS['MY_REQUEST']['redeem-account']) ) {
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
	}
?>
<div class="wrap">
	<h3>Start using 40Nuggets</h3>
	<p class="description">
		If this is your first time using 40Nuggets, let's start with creating your account.	
	</p>
	<form method="POST" action="">
		<p class="submit">
			<input type="hidden" name="create-account" />
			<input class="button-primary" type="submit" name="signup" value=" <?php _e( 'Create Account' ); ?> " />
		</p>
	</form>
	
	<p class="submit"></p>
	<form method="POST" action="">
    <h3>Login to existing account</h3>
	<table class="form-table">
      <tbody>
		<tr valign="top">
          <th scope="row">
            <label for="email">E-mail</label>
          </th>
          <td>
            <input name="email" type="text" id="email" value="<?php echo get_option('admin_email');?>" class="regular-text code" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="password">Password</label>
          </th>
          <td>
            <input name="password" type="password" id="password" value="" class="regular-text code" />
			<br/>
			<p class="description"><a href="https://40nuggets.com/dashboard/forgotPassword.php" target="_blank">Forgot your password?</a></p>
          </td>
        </tr>
      </tbody>
    </table>
	
	<p class="submit">
		<input type="hidden" name="redeem-account" />
		<input class="button-primary" type="submit" name="login" value=" <?php _e( 'Sign in' ); ?> " />
	</p>
	</form>
	</div>
