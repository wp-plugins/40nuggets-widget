<?php

	if( isset($_POST['settings-updated']) ) {
		$email = $_POST["email"];
		$password = $_POST["password"];
		
		$plugin = new Fortynuggets_Plugin ();	
		if ($plugin->login($email, $password)){
			$url = home_url();
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
	<form method="POST" action="">
    <table class="form-table">
      <tbody>
		<tr><th><h3>Switch Account</h3></th><tr>
        <tr valign="top">
          <th scope="row">
            <label for="email">Email Address</label>
          </th>
          <td>
            <input name="email" type="text" id="email" value="" class="regular-text" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="password">Password</label>
          </th>
          <td>
            <input name="password" type="password" id="password" value="" class="regular-text code" />
          </td>
        </tr>
      </tbody>
    </table>
	
	<p class="submit">
		<input type="hidden" name="settings-updated" />
		<input class="button-primary" type="submit" name="login" value=" <?php _e( 'Login' ); ?> " />
	</p>
	</form>
	</div>
