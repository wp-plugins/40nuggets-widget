<?php

require_once(dirname(__FILE__) . '/admin/menu.php');
include_once('Fortynuggets_LifeCycle.php');

class Fortynuggets_Plugin extends Fortynuggets_LifeCycle {
	
    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
        );
    }

    public function getPluginDisplayName() {
        return '40Nuggets';
    }

    protected function getMainPluginFileName() {
        return 'fortynuggets.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('fnm');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
		
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('fnm');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function activate() {
		$this->freeze_client(false);
	}

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function deactivate() {
		$this->freeze_client(true);
    }

    /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addSettingsSubMenuPage() {
		//Overriding with an empty implementation
		//see menu.php for menues
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37
		add_action('publish_post', array(&$this, 'run_when_post_published'), 10, 2);
		add_action('trashed_post', array(&$this, 'run_when_post_trashed'));
		add_action('publish_to_pending', array(&$this, 'run_when_post_unpublish'));
		add_action('publish_to_draft', array(&$this, 'run_when_post_unpublish'));
		add_action('publish_to_future', array(&$this, 'run_when_post_unpublish'));
		
		// Run import nuggets script
        add_action('wp_head', array(&$this, 'import_nuggets'));

        // Adding scripts & styles to all pages
            if (!is_admin()) {
				wp_enqueue_script('jquery');
                
				$options = $this->get_options();
				if ($options->site_track || !isset($options->site_track)){
					wp_enqueue_script('40nm-tracking', plugins_url('/js/track.js', __FILE__));
				}
				//pass key to js
				$options = $this->get_options();
				wp_localize_script('40nm-tracking', '_40nmcid', $options->api_key);
			}else{
				//media upload
				add_action('admin_init', array(&$this, 'fix_thickbox_action_button'));
				wp_enqueue_script('media-upload');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('my-upload', plugins_url('/js/upload_image.js', __FILE__), array('jquery','media-upload','thickbox'));
				wp_enqueue_style('thickbox');
			}

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }

	protected function otherInstall() {
	    //create new account and other stuff
		$this->create_client();
	}

	public function login($email, $password){
		
		$data = array(
						"email" => $email,
						"password" => $password,
						);
		$json["client"] = $data;
		$data_string = json_encode($json);   
		
		$response = $this->apiCall("login", "POST", $data_string);
		
		if (isset($response->client)){
			$data["id"] = $response->client->id;
			$data["api_key"] = $response->client->api_key;
			$this->save_options($data);

			return true;
		}
		
		return false; 
	}

	protected function create_client(){
		//create new user
		$email = get_option('admin_email');
		$password = substr(sha1(time() . "thisisagooddaytosavelives"), 0, 8);
		$data = array(
						"email" => $email,
						"password" => $password,
						"title" => get_option('blogname'),
						"url" => get_option('home'),
						"subscription_type" => 1,
						"frequency" => 1,
						);
		$json["client"] = $data;
		$data_string = json_encode($json);	
		
		$this->apiCall('clients', "POST", $data_string);
		
		//TODO: Check if client created successfully, what happens if he already signed up?
		
		$this->login($email, $password);
						
		return $response;
	}

	protected function freeze_client($state){
		$options = $this->get_options();
		$data["client"] = array(
						"is_freeze" => $state,
						);
		$data_string = json_encode($data);	
		$response = $this->apiCall("clients/{$options->id}", "PUT", $data_string);
						
		return $response;
	}

	public function get_recommendations($user, $url, $count){
		//TBD: send user cookie!
		$url = urlencode($url);
		$response = $this->apiCall("clients/me/users/me/recommendations?url=$url&count=$count");
		$nuggets = $response->nuggets;
		
		return $nuggets;
	}

	//return import progress in percentage
	public function import_nuggets(){

		if (!$this->did_collect_all_Nuggets()){
			$options = $this->get_options();

			$count_posts = wp_count_posts();
			$published_posts = $count_posts->publish;		
			$collected_nuggets = isset ($options->nuggetizer_offset) ? $options->nuggetizer_offset : 0;
			$progress  = round($collected_nuggets/$published_posts*100);

			$this->collect_Nuggets();
		}else{
			$progress = 100;
		}
		
		return $progress;
	}
	
	public function did_collect_all_Nuggets(){
		$options = $this->get_options();
		return isset($options->collect_all_Nuggets);
	}
	
	protected function collect_Nuggets(){
		$options = $this->get_options();
		$offset = $options->nuggetizer_offset;
		$count = 1;
		
		global $post;
		$args = array(     
					'post_status'  	=> 'publish',
					'numberposts' 	=> $count,
					'offset' 		=> $offset
					);
		$myposts = get_posts($args);
		foreach($myposts as $post){
			$this->add_post_as_nugget($post);
		}
		
		if (count($myposts) == 0){
			$options->collect_all_Nuggets = "YES";
		}else{
			unset($options->collect_all_Nuggets);
		}
		
		
		$options->nuggetizer_offset += $count;
		$this->save_options($options);

	}
	
	protected function get_post_images($post){
		$images = array();
		
		//TODO: support "Featured Thumbnail"
		
		$doc = new DOMDocument();
		@$doc->loadHTML($post->post_content);
			
		$tags = $doc->getElementsByTagName('img');

		foreach ($tags as $tag) {
			array_push ($images,$tag->getAttribute('src'));
		}

		return $images;
	}

	public function get_blog_logo(){
		$images = array();
				
		$doc = new DOMDocument();
		@$doc->loadHTML(get_header());
			
		$tags = $doc->getElementsByTagName('img');

		foreach ($tags as $tag) {
			array_push ($images,$tag->getAttribute('src'));
		}
		
		return $images[0];
	}
	

	public function get_nugget_id($post){
		$url = urlencode(get_permalink($post));
		$response = $this->apiCall("clients/me/nuggets?url=$url");
		$nuggets = $response->nuggets;
		$nugget = $nuggets[0]->nugget;
		
		return $nugget->id;
	}
	
	public function get_post_id($nugget_id){
		$response = $this->apiCall("nuggets/$nugget_id");
		$nugget = $response->nugget;

		return url_to_postid($nugget->link);
	}
	
	public function run_when_post_published ($post_id, $post){
		if ($this->is_excluded($post->id)) return;

		// Abort if is post updated and not published at first time.
		if ($post->post_date == $post->post_modified){ 	//new post
			$this->add_post_as_nugget($post);
		}else{											//update post
			$this->update_post_as_nugget($post);
		}		
	}

	protected function add_post_as_nugget($post){
		if ($this->is_excluded($post->id)) return;

		$options = $this->get_options();
		setup_postdata($post);
		$images = $this->get_post_images($post);
		$image = $images[0];
		$is_displayable_thumbnail = isset($image);
		if (!isset($image)) $image = "";
			
		$nugget = array(
						"title" => $post->post_title,
						"image" => $image,
						"link" => get_permalink($post->id),
						"author" => get_the_author(),
						"date" => get_the_date("d-m-Y"),
						"body" => $this->get_the_content_with_formatting(),
						"client" => $options->id,
						"state" => 2,
						"is_displayable_thumbnail" => $is_displayable_thumbnail,
						);
		$json["nugget"] = $nugget;
		$data_string = json_encode($json);  
		$this->apiCall('nuggets?is_short_body=true', "POST", $data_string);
		//TODO: check if new nugget created successfully
		wp_reset_postdata(); 
	}

	protected function update_post_as_nugget($post){
		if ($this->is_excluded($post->id)) return;

		//check if nugget exists
		$nugget_id = $this->get_nugget_id($post);
		if (!isset($nugget_id)){
			$this->add_post_as_nugget($post);
			return;
		}
	
		$options = $this->get_options();
		setup_postdata($post);
		$images = $this->get_post_images($post);
		$image = $images[0];
		$is_displayable_thumbnail = isset($image);
		if (!isset($image)) $image = "";

		$nugget = array(
						"title" => $post->post_title,
						"image" => $image,
						"link" => get_permalink($post->id),
						"author" => get_the_author(),
						"date" => get_the_date("d-m-Y"),
						"body" => $this->get_the_content_with_formatting(),
						"client" => $options->id,
						"is_displayable_thumbnail" => $is_displayable_thumbnail,
						);
		$json["nugget"] = $nugget;
		$data_string = json_encode($json); 
		$this->apiCall("nuggets/$nugget_id?is_short_body=true", "PUT", $data_string);
		wp_reset_postdata(); 
	}
		
	public function run_when_post_unpublish ($post){
		if ($this->is_excluded($post->id)) return;
		
		$nugget_id = $this->get_nugget_id($post);		
		$this->unpublish_nugget ($nugget_id);
	}

	public function run_when_post_trashed ($post){
		if ($this->is_excluded($post->id)) return;

		setup_postdata($post);

		$nugget_id = $this->get_nugget_id($post);				
		$this->trash_nugget($nugget_id);

		wp_reset_postdata(); 
	}
	
	public function unpublish_nugget ($nugget_id){
		$nugget = array(
						"state" => 1,
						);
		$json["nugget"] = $nugget;
		$data_string = json_encode($json);  
		
		$this->apiCall("nuggets/$nugget_id", "PUT", $data_string);
	}
	
	public function publish_nugget ($nugget_id){
		$nugget = array(
						"state" => 2,
						);
		$json["nugget"] = $nugget;
		$data_string = json_encode($json);  
		
		$this->apiCall("nuggets/$nugget_id", "PUT", $data_string);
	}
	
	public function trash_nugget ($nugget_id){
		$nugget = array(
						"state" => 3,
						);
		$json["nugget"] = $nugget;
		$data_string = json_encode($json);  
		
		$this->apiCall("nuggets/$nugget_id", "PUT", $data_string);
	}
	
	//requests should be array of requests as defined in api
	public function batchApiCall($requests){
		$requests["requests"] = $requests;
		$data_string = json_encode($requests);  

		return $this->apiCall("batch", "POST", $data_string);
		
	}
	
	public function apiCall($api, $method="GET", $data_string=""){

		$url = 'http://40nuggets.com/api1/40nm/'.$api;  
		$result = $this->httpCall($url, $method, $data_string);
		
		if (isset($result->error)){
			//TODO: check if not logged in and redirect to login page
		}
		
		return json_decode($result);
	}
	
	public function httpCall($url, $method=null, $data_string=null){

		//error_log ("Calling:$method $url With data $data_string");
	
		$cookie = dirname(__FILE__) . '/fortynuggets.fnm';

		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		
		if (isset($method)) 		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
		if (isset($data_string)) 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		
		$result = curl_exec($ch);
				
		curl_close($ch);
		//error_log ("Result: $result");
		
		return $result;
	}
	
	public function save_options ($options){
		update_option('40nm-options', base64_encode(json_encode($options)));
	}
	
	public function get_excluded_posts (){
		$options = $this->get_options();
		return isset($options->excludes) ? (array)$options->excludes : array();
	}	

	public function get_options (){
		return json_decode(base64_decode(get_option('40nm-options')));
	}	

	public function exclude_post ($post_id){
		if ($this->is_excluded($post_id)) return;
		
		$options = $this->get_options();
		$options->excludes = isset($options->excludes) ? (array)$options->excludes : array();
		$options->excludes[$post_id] = $post_id;
		$this->save_options($options);
	}
	
	public function include_post ($post_id){

		if (!$this->is_excluded($post_id)) return;
		
		$options = $this->get_options();
		$options->excludes = isset($options->excludes) ? (array)$options->excludes : array();
		$options->excludes = array_diff($options->excludes, array($post_id));
		$this->save_options($options);
	}	

	public function is_excluded ($post_id){
		$options = $this->get_options();
		$options->excludes = isset($options->excludes) ? (array)$options->excludes : array();
		return in_array($post_id, $options->excludes);
	}

	public function show_response ($response, $success=null, $error=null){
		
		if (isset($response->error)){
			$class = "error";
			$code = isset($response->error->code) ? " (Error:{$response->error->code})" : "";
			$message = isset($error) ? $error : "Oops, Something went wrong...$code";
		}else{
			$class = "updated";
			$message = isset ($success) ? $success : "Changes Saved";			
		}

		$html = "<div id='message' class='$class'>
				<p><strong>$message</strong></p>
			</div>";

		echo $html;
	}
	
	protected function get_the_content_with_formatting () {
		$content = get_the_content();
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;

	}
	
	
	
	
	
	
	
	
	
	public function fix_thickbox_action_button() {
		global $pagenow;

		if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
			// Now we'll replace the 'Insert into Post Button' inside Thickbox
			add_filter( 'gettext', array(&$this, 'replace_thickbox_text'), 1, 3 );
		}
	}

	public function replace_thickbox_text($translated_text, $text, $domain) {
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'fnm-settings' );
			if ( $referer != '' ) { 
				switch ($_GET["type"]){
					case 'image': return __('Use this as my banner!', 'fnm' );
					case 'file': return __('Use this CSV file', 'fnm' );
				}
			}
		}
		return $translated_text;
	}

}