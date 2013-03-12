<?php 
	$plugin = new Fortynuggets_Plugin ();	

	if( isset($_POST['action']) ) {
	
		switch ($_POST['action']){
			case "create_user":
				$options = $plugin->get_options();
				$email = urlencode($_POST["email"]);
				$name = urlencode($_POST["name"]);
				$api_key = $options->api_key;
				$api = "subscribe?is_dashboard=true&name=$name&email=$email&client=$api_key";
				$response = $plugin->apiCall($api);
				break;
			case "upload_csv":
				//TODO: needs API for uploading CSV
				$data = array(
								"url" => $_POST["upload_file"]
								);
				$data_string = json_encode($data);   
				$response = $plugin->apiCall("clients/me/csvs", "POST", $data_string);
				break;
		}
		
		if (isset($response->error)){
			$class = "error";
			$code = isset($response->error->code) ? " (Error:{$response->error->code})" : "";
			$message = "Oops, Something went wrong...$code";
			if ($_POST['action'] == "upload_csv"){
				$message .= "<br class='clear'/>Having trouble with those pesky CSV's? Email us at <a href='mailto:support@40nuggets.com'>support@40nuggets.com</a> and we'll get you contacts loaded up for you.";
			}else{
				$message .= "<br class='clear'/>Think Think Think";
			}
			echo "<div id='message' class='$class'>
					<p align='center'><strong>$message</strong></p>
				</div>";
		}else{
			echo "<script type='text/JavaScript'>window.location='?page=40Nuggets-users';</script>";
		}
	}
?>

<script language="javascript">
function validate(){
	var is_valid = isValidEmail(document.getElementById('email').value);
	if (!is_valid) alert ("Please enter a valid email");
	
	return is_valid;
}
function isValidEmail(email){
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;	
	return re.test(email);
}
</script>
<div class="wrap">
<div id="icon-users" class="icon32"><br></div><h2 id="add-new-user"> Add New Contact</h2>


<p>Add new contact to your eNewsletter list.</p>
<form action="" method="post" class="validate">
<input name="action" type="hidden" value="create_user">
<table class="form-table">
	<tbody>
    <tr valign="top">
		<th scope="row"><label for="email">E-mail <span class="description">(required)</span></label></th>
		<td scope="row"><input name="email" type="text" id="email" value=""></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="name">Name</label></th>
		<td scope="row"><input name="name" type="text" id="name" value="" aria-required="true"></td>
	</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="create_user" id="createusersub" class="button button-primary" value="Add New Contact" onclick="return (validate());"></p>
</form>


<br class="clear">

<h2>Upload CSV file</h2>
<p class="description">We support importing CSV files from Google, Outlook and some other apps.
<br/>We also support importing vCard from apps like Apple Address Book.</p>
<br class='clear' />
<p class="description">Please select a CSV or vCard file to upload:</p>
<form action="" method="post" name="upload_csv" id="upload_csv">
<input name="action" type="hidden" value="upload_csv">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<td>
				<input id="upload_file" type="text" size="36" name="upload_file" value="" />
				<input id="upload_file_button" type="button" value="Select File" />
			</td>
		</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="upload_csv" id="createusersub" class="button button-primary" value="Upload" onclick="if (document.getElementById('upload_file').value == '') return false;"></p>
</form>

</div>