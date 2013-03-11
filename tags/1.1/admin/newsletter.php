<?php
	
	require_once(dirname(__FILE__) . '/pagination.php');
	$p = isset($_GET["p"]) ? $_GET["p"] : 1;
	$c = isset($_GET["c"]) ? $_GET["c"] : PAGE_SIZE;
	$nugget_status = isset($_GET["nugget_status"]) ? $_GET["nugget_status"] : 2;
	$searchQuery = isset($_POST["searchQuery"]) ? $_POST["searchQuery"] : "";
	
	$plugin = new Fortynuggets_Plugin ();	
	
	//response to bulk action
	if (isset($_REQUEST["action"])){
		switch ($_REQUEST["action"]){
			case "bulk_trash":
				$nuggets = $_POST["nuggets"];
				if (count($nuggets) == 0) break;
				$failed_calls = 0;
				foreach ($nuggets as $nugget){
					$response = $plugin->trash_nugget($nugget);

					if (isset($response->error)){
						$failed_response = $response;
						$failed_calls++;
						error_log("Error when trashing: {$response->error}");
					}else{
						//exclude this nugget
						$post_id = $plugin->get_post_id($nugget);
						$plugin->exclude_post ($post_id);
					}
				}
				
				if ($failed_calls >0){
					$plugin->show_response ($failed_response);
				}else{
					$plugin->show_response ($response);
				}
				
				break;
			
			case "bulk_pending":
				$nuggets = $_POST["nuggets"];
				if (count($nuggets) == 0) break;
				$failed_calls = 0;
				foreach ($nuggets as $nugget){
					$response = $plugin->unpublish_nugget($nugget);

					if (isset($response->error)){
						$failed_response = $response;
						$failed_calls++;
					}
				}
				
				if ($failed_calls >0){
					$plugin->show_response ($failed_response);
				}else{
					$plugin->show_response ($response);
				}
				
				break;
			
			case "bulk_include":
				$posts = $_POST["nuggets"];
				if (count($posts) == 0) break;
				$failed_calls = 0;
				foreach ($posts as $post_id){
					$plugin->include_post($post_id);
					$nugget_id = $plugin->get_nugget_id($post_id);
					$response = $plugin->publish_nugget($nugget_id);

					if (isset($response->error)){
						$failed_response = $response;
						$failed_calls++;
					}
				}
				
				if ($failed_calls >0){
					$plugin->show_response ($failed_response);
				}else{
					$plugin->show_response ($response);
				}
				
				break;
			
			case "bulk_active":
				$nuggets = $_POST["nuggets"];
				if (count($nuggets) == 0) break;
				$failed_calls = 0;
				foreach ($nuggets as $nugget){
					$response = $plugin->publish_nugget($nugget);

					if (isset($response->error)){
						$failed_response = $response;
						$failed_calls++;
					}
				}
				
				if ($failed_calls >0){
					$plugin->show_response ($failed_response);
				}else{
					$plugin->show_response ($response);
				}
				
				break;
			
			case "single_trash": 
				$nugget_id = $_GET["id"];
				$response = $plugin->trash_nugget($nugget_id);
				if (!isset($response->error)){
					//exclude this nugget
					$post_id = $plugin->get_post_id($nugget_id);
					$plugin->exclude_post ($post_id);
				}

				$plugin->show_response ($response);
				break;	
			
			case "single_pending": 
				$nugget_id = $_GET["id"];
				$response = $plugin->unpublish_nugget($nugget_id);

				$plugin->show_response ($response);
				break;	
			
			case "single_include": 
				$post_id = $_GET["id"];
				$plugin->include_post($post_id);
				$nugget_id = $plugin->get_nugget_id($post_id);
				$response = $plugin->publish_nugget($nugget_id);

				$plugin->show_response ($response);
				break;	
			
			case "single_active": 
				$nugget_id = $_GET["id"];
				$response = $plugin->publish_nugget($nugget_id);
				$plugin->show_response ($response);
				break;	
			
			case "view":
				include(dirname(__FILE__) . '/preview.php');
				return;
				break;
			
			case "edit":
				$post_id = $_GET["post_id"];
				echo "<script type='text/JavaScript'>window.location='post.php?post=$post_id&action=edit';</script>";
				break;
		}
	}

	if ($nugget_status !=999){
		$response = $plugin->apiCall("clients/me/nuggets?state=$nugget_status&page=$p&count=$c&fields=id^title^image^link^stats&search=".urlencode($searchQuery));
		$nuggets = $response->nuggets;
		$last_page = $response->paging->last_page;
	}else{
		$excludes = $plugin->get_excluded_posts();
		$nuggets = array();
		foreach ($excludes as $exclude){
			$post = get_post($exclude);
			$data = (object)array(
						"id" => $post->ID,
						"title" => $post->post_title,
						"link" => get_permalink($post->id)
						);
			$nugget = (object)array("nugget" => $data);
			array_push ($nuggets,$nugget);
		}
		$last_page = 1;
	}
	
	
	//calculate counters								
	$response = $plugin->apiCall("clients/me/stats");
	$num_active = $response->stats->num_published;
	$num_pending = $response->stats->num_pending;
	$excludes = $plugin->get_excluded_posts();
	$num_excluded = count ($excludes);
	
	//check import status
	$import_progress = $plugin->import_nuggets();
?>


<style>
	.column-stats{ 
		width:80px; 
		text-align:center !important;
	}
</style>

<div class="wrap">
	<div id="icon-edit-pages" class="icon32"></div>
	<h2>Newsletter
	<?php if ($searchQuery) echo "<span class='subtitle'>Search results for '$searchQuery'</span>"; ?>
	</h2>
			<?php if ($import_progress < 100){
			echo "<div style='width:300px;margin:auto;text-align:center;'>
					<span class='description'>Processing your site, please hang in there...</span>
					<div style='height:20px;border-color:#e6db55;background-color:#ffffe0;-webkit-border-radius:3px;border-radius:3px;border-width:1px;border-style:solid;'>
						<div style='width:$import_progress%;height:20px;line-height:20px;background-color:green;color:#ffffff;font-weight:bold;'>
						$import_progress%
						</div>
					</div>
				  </div>";
		}?>

		
	<ul class="subsubsub">
		<li class="active"><a href="?page=<?php echo $_GET["page"];?>&nugget_status=2"<?php if ($nugget_status == 2) echo ' class="current"';?>>Active <span class="count">(<?php echo number_format($num_active);?>)</span></a> |</li>
		<li class="pending"><a href="?page=<?php echo $_GET["page"];?>&nugget_status=1"<?php if ($nugget_status == 1) echo ' class="current"';?>>Pending Approval <span class="count">(<?php echo number_format($num_pending);?>)</span></a> |</li>
		<li class="excluded"><a href="?page=<?php echo $_GET["page"];?>&nugget_status=999"<?php if ($nugget_status == 999) echo ' class="current"';?>>Excluded <span class="count">(<?php echo number_format($num_excluded);?>)</span></a></li>
	</ul>

	<form action="<?php echo "?page={$_GET["page"]}&nugget_status=$nugget_status";?>" method="POST">
	
	<?php if ($nugget_status != 999){?>
		<p class="search-box">
			<label class="screen-reader-text" for="nugget-search-input">Search</label>
			<input type="search" id="nugget-search-input" name="searchQuery" value="<?php echo $searchQuery;?>">
			<input type="submit" name="" id="search-submit" class="button" value="Search">
		</p>
	<?php } ?>
	
	<div class='tablenav'>
		<?php echo pagination ($p, $last_page);?>

		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected">Bulk Actions</option>
				<?php if ($nugget_status != 999) {
					if ($nugget_status != 1) {?>
						<option value="bulk_pending" class="hide-if-no-js">Move to Pending</option>
					<?php } ?> 
					<?php if ($nugget_status != 2) {?>
						<option value="bulk_active" class="hide-if-no-js">Activate</option>
					<?php } ?> 
						<option value="bulk_trash" class="hide-if-no-js">Exclude</option>
				<?php }else{ ?> 
						<option value="bulk_include" class="hide-if-no-js">Include</option>				
				<?php } ?> 
			</select>
			<input type="submit" name="" id="doaction" class="button action" value="Apply">
		</div>
	</div>
	
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column" id="cb" style="vertical-align:middle;"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>				
				<th scope="col" class="manage-column column-title"><span>Post Title</span><span class="sorting-indicator"></span></th>
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
				<th scope="col" class="manage-column column-cb check-column" id="cb" style="vertical-align:middle;"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
				<th scope="col" class="manage-column column-title"><span>Post Title</span><span class="sorting-indicator"></span></th>
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
			if (count($nuggets) == 0){
				switch ($nugget_status){
					case 1: 	$message = "You have no pending posts or social content awaiting review"; break;
					case 2: 	$message = "Either you have no published posts on your site or we are still processing your site. Hang tight..."; break;
					case 999: 	$message = "You have not excluded any posts from your Newsletter content"; break;
				}
				echo "
					<tr>
						<td class='manage-column column-stats' colspan='7' style='height:75px;vertical-align:middle;'>
							<p class='description'>
								$message
							</p>
						</td>
					</tr>
					";
			}else foreach ($nuggets as $nugget){
				$i++;
				$nugget = $nugget->nugget;
				$alternate = ($i%2) ? "" : "alternate";
				$post_id = url_to_postid($nugget->link);

				echo "
					<tr class='$alternate'>
						<th scope='row' class='check-column'><label class='screen-reader-text' for='cb-select-{$nugget->id}'>Select Test post</label><input id='cb-select-{$nugget->id}' type='checkbox' name='nuggets[]' value='{$nugget->id}'></th>
						<td>
							<strong><a class='row-title' href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=view&id={$nugget->id}'>{$nugget->title}</a></strong>
							<div class='row-actions'>
								<span class=edit'>
									<a href='?page={$_GET["page"]}&action=edit&post_id={$post_id}' title='Edit Original Post'>Edit</a> | 
								</span>
					";
				if ($nugget_status != 999){ 

					if ($nugget_status != 2){ 				
						echo "		
									<span class='active'>
										<a href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=single_active&id={$nugget->id}' title='Activate Post'>Activate</a> | 
									</span>";
					}
					
					if ($nugget_status != 1){ 				
						echo "		
									<span class='pending'>
										<a href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=single_pending&id={$nugget->id}' title='Move Post To Pending'>Pending</a> | 
									</span>";
					}				
					
					echo "		
									<span class='trash'>
										<a class='submitdelete' title='Exclude Post from Newsletter' href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=single_trash&id={$nugget->id}'>Exclude</a> | 
									</span>
						";
				}else{
					echo "		
									<span class='include'>
										<a title='Include Post In Newsletter' href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=single_include&id={$nugget->id}'>Include</a> | 
									</span>
						";
				}
				
				echo "			
								<span class='view'>
									<a href='?page={$_GET["page"]}&nugget_status=$nugget_status&action=view&id={$nugget->id}' title='View as Newsletter'>View</a>
								</span>
							</div>
						</td>
						<td class='column-stats'>{$nugget->stats->delivered_to}</td>
						<td class='column-stats'>{$nugget->stats->or}</td>
						<td class='column-stats'>{$nugget->stats->tor}</td>
						<td class='column-stats'>{$nugget->stats->ctr}</td>
						<td class='column-stats'>{$nugget->stats->ctor}</td>
						<td class='column-stats'>{$nugget->stats->total_clicks}</td>
					</tr>
					";
			}
			?>
		</tbody>
	</table>
	</form>
</div>