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
		//restore cookie
		$options = $this->get_options();
		if (isset($options->cookie)){
			$cookie = dirname(__FILE__) . '/fortynuggets.fnm';
			file_put_contents($cookie, $options->cookie);
		}

		$this->freeze_client(false);
	}

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function deactivate() {
		$this->freeze_client(true);

		//store cookie
		$options = $this->get_options();
		$cookie = dirname(__FILE__) . '/fortynuggets.fnm';
		$options->cookie = file_get_contents($cookie);
		$this->save_options($options);
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
        if (strpos(strtolower($_GET["page"]),"40nuggets") !== false) { //add action only to 40Nuggets pages
			add_action('admin_menu', wp_enqueue_media);
		}

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37
		//add_action('publish_post', array(&$this, 'run_when_post_published'), 10, 2);
		
        // Adding scripts & styles to all pages
            if (is_admin()) {
				//Add script on WP Admin Panel
			}else{
				wp_enqueue_script('jquery');
                
				$options = $this->get_options();
				if ($options->site_track || !isset($options->site_track)){
					wp_enqueue_script('40nm-tracking', plugins_url('/js/track.js', __FILE__));
				}
				//pass key to js
				$options = $this->get_options();
				wp_localize_script('40nm-tracking', '_40nmcid', $options->api_key);
			}

		// Create $GLOBALS['MY_REQUEST']
		// http://php.net/manual/en/function.get-magic-quotes-gpc.php
		$GLOBALS['MY_REQUEST'] = $_REQUEST;

		if (get_magic_quotes_gpc()) {
			$GLOBALS['MY_REQUEST'] = $this->stripslashes_deep($GLOBALS['MY_REQUEST']);
		}

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }
	
	protected function stripslashes_deep($value){
		$value = is_array($value) ?
		array_map(array($this, 'stripslashes_deep'), $value) :   
		stripslashes($value);

		return $value;
	}
	
	protected function otherInstall() {
	    //create new account and other stuff
	}

	public function login($email, $password){
		
		$data = array(
					"email" => $email,
					"password" => $password,
					);
		$json["client"] = $data;
		$data_string = json_encode($json);   
		
		$response = $this->apiCall("login", "POST", $data_string);
		if (isset($response->profile)){
			$response = $this->apiCall("clients/me");
			if (isset($response->client)){
				$data["id"] = $response->client->id;
				$data["api_key"] = $response->client->api_key;
				$this->save_options($data);

				return true;
			}
		}
		
		return false; 
	}

	public function logout(){
		//delete cookie
		$cookie = dirname(__FILE__) . '/fortynuggets.fnm';
		file_put_contents($cookie, "");

		//cleanup data
		$data["id"] = "";
		$data["api_key"] = "";
		$this->save_options($data);
		
		return true;
	}

	public function create_client(){
		//check if already logged in
		$options = $this->get_options ();
		if ($options->id) return true;
		
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
		
		$response = $this->apiCall('public/clients', "POST", $data_string);
		$response = $this->login($email, $password);
						
		return $response;
	}

	protected function freeze_client($state){
		$options = $this->get_options();
		$data["client"] = array(
						"is_frozen" => $state,
						);
		$data_string = json_encode($data);	
		$response = $this->apiCall("clients/{$options->id}", "PUT", $data_string);
						
		return $response;
	}

	public function apiCall($api, $method="GET", $data_string=""){

		$url = 'http://localhost/api/'.$api;  
		$result = $this->httpCall($url, $method, $data_string);
		
		$json = json_decode($result);
		
		if (isset($json->error)){
			switch ($json->error->code){
				case 403005: 
					echo "<script type='text/JavaScript'>window.location='?page=40Nuggets-login';</script>";
					break;
			}
		}
		
		return $json;
	}
	
	public function httpCall($url, $method=null, $data_string=null){

		//error_log ("Calling:$method $url With data $data_string");
	
		$cookie = dirname(__FILE__) . '/fortynuggets.fnm';
		$savedVersion = $this->getVersionSaved();
		
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17 WordPressPlugin/{$savedVersion}");
		
		if (isset($method)) 		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
		if (isset($data_string)) 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		
		$result = curl_exec($ch);
				
		curl_close($ch);
		
		return $result;
	}
	
	public function save_options ($options){
		update_option('40nm-options', base64_encode(json_encode($options)));
	}
	
	public function get_options (){
		return json_decode(base64_decode(get_option('40nm-options')));
	}	

	public function show_response ($response, $success=null, $error=null){
		
		if (isset($response->error)){
			$class = "error";
			$code = isset($response->error->code) ? " ({$response->error->message})" : "";
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
	
}