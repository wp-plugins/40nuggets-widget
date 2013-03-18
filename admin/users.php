<?php
	require_once(dirname(__FILE__) . '/pagination.php');
	$p = isset($_GET["p"]) ? $_GET["p"] : 1;
	$c = isset($_GET["c"]) ? $_GET["c"] : PAGE_SIZE;

	$plugin = new Fortynuggets_Plugin ();	

	//response to bulk action
	if (isset($GLOBALS['MY_REQUEST']["action"]) &&
		$GLOBALS['MY_REQUEST']["action"] == "unsubscribe" &&
		count($GLOBALS['MY_REQUEST']["users"]) > 0){
		
		$json["emails"] = $GLOBALS['MY_REQUEST']["users"];
		$data_string = json_encode($json);  
		$response = $plugin->apiCall('batch_unsubscribe', "POST", $data_string);
		
		if (isset($response->error)){
			$class = "error";
			$code = isset($response->error->code) ? " (Error:{$response->error->code})" : "";
			$message = "Oops, Something went wrong...$code";
		}else{
			$class = "updated";
			$message = count($GLOBALS['MY_REQUEST']["users"]) . " users were removed from mailing list";
		}
		
		echo "<div id='message' class='$class'>
				<p><strong>$message</strong></p>
			</div>";
	}

	$response = $plugin->apiCall("clients/me/users?fields=id^email^name^stats&page=$p&count=$c");
	$users = $response->users;
	$last_page = $response->paging->last_page;
	
?>


<style>
	.column-stats{ 
		width:135px; 
		text-align:center !important;
	}

</style>

<?php echo '<div style="position:absolute;top:0;right:0;"><a href="widgets.php"><img src="' . plugins_url( 'images/widget.png' , __FILE__ ) . '" ></a></div>';?>

<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Audience
	<a href="?page=40Nuggets-add-new-user" class="add-new-h2">Add New</a>
	</h2>
	<br class='clear'>
	
	<form action="?page=<?php echo $_GET["page"];?>" method="POST">
	<div class='tablenav'>
		<?php echo pagination ($p, $last_page);?>
		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="unsubscribe">Delete</option>
			</select>
			<input type="submit" name="" id="doaction" class="button action" value="Apply">
		<span class='description aligncenter'>
			Heads up: your audience is live all the time and any new contacts you add will begin receiving your eNewsletter if you have active posts.
		</span>
		</div>
	</div>


	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column" id="cb"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>				
				<th scope="col" width="*" class="manage-column column-title"><span>Name</span><span class="sorting-indicator"></span></th>
				<th scope="col" class="manage-column column-stats"><a id="delivered" title="How many users received this Newsletter."><span>Delivered</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="or" title="Open Rate (Unique): The unique open rate measures one open per recipient and is expressed as a percentage of the total number of delivered Newsletters."><span>Open Rate</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="tor" title="Total Open Rate: Measures how many times the Newsletter has been opened, either by the original recipients or by those to whom he forwards the Newsletter."><span>Tot. Open Rate</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="ctr" title="Click-Through Rate: Measures the percentage of users that clicked on this specific Newsletter at least once. This statistic counts only one click per user."><span>CTR</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="ctor" title="Click-to-Open Rate: Measures the percentage of opened Newsletters that recorded clicks."><span>CTOR</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="total-clicks" title="How many click throughs this Newsletter generated"><span>Clicks</span><span class="sorting-indicator"></span></a></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column" id="cb"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>				
				<th scope="col" width="*" class="manage-column column-title"><span>Name</span><span class="sorting-indicator"></span></th>
				<th scope="col" class="manage-column column-stats"><a id="delivered" title="How many users received this Newsletter."><span>Delivered</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="or" title="Open Rate (Unique): The unique open rate measures one open per recipient and is expressed as a percentage of the total number of delivered Newsletters."><span>Open Rate</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="tor" title="Total Open Rate: Measures how many times the Newsletter has been opened, either by the original recipients or by those to whom he forwards the Newsletter."><span>Tot. Open Rate</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="ctr" title="Click-Through Rate: Measures the percentage of users that clicked on this specific Newsletter at least once. This statistic counts only one click per user."><span>CTR</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="ctor" title="Click-to-Open Rate: Measures the percentage of opened Newsletters that recorded clicks."><span>CTOR</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-stats"><a id="total-clicks" title="How many click throughs this Newsletter generated"><span>Clicks</span><span class="sorting-indicator"></span></a></th>
			</tr>
		</tfoot>
		<tbody id="statsRows">
			<?php
			if (count($users) == 0){
				echo "
					<tr>
						<td class='manage-column column-stats' colspan='7' style='height:75px;vertical-align:middle;'>
							<p class='description'>
								You have not uploaded or added any contacts yet - you should get on that...
							</p>
						</td>
					</tr>
					";
			}else foreach ($users as $user){
				$i++;
				$user = $user->user;
				$alternate = ($i%2) ? "" : "alternate";
				echo "
					<tr class='$alternate'>
						<th scope='row' class='check-column'><label class='screen-reader-text' for='cb-select-{$user->id}'>Select Test post</label><input id='cb-select-{$user->id}' type='checkbox' name='users[]' value='{$user->email}'></th>
						<td>{$user->email}</td>
						<td class='column-stats'>{$user->stats->delivered_to}</td>
						<td class='column-stats'>{$user->stats->or}%</td>
						<td class='column-stats'>{$user->stats->tor}%</td>
						<td class='column-stats'>{$user->stats->ctr}%</td>
						<td class='column-stats'>{$user->stats->ctor}%</td>
						<td class='column-stats'>{$user->stats->total_clicks}</td>
					</tr>
					";
			}
			?>
		</tbody>
	</table>	
	</form>
</div>

	