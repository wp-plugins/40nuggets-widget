<?php

/**
 * 40Nuggets top level
 */  
add_action('admin_menu', 'FortyNuggets_create_menu_page');
function FortyNuggets_create_menu_page() {

	add_menu_page(
		'40Nuggets Options',			// The title to be displayed on the corresponding page for this menu
		'40Nuggets',				// The text to be displayed for this actual menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets',				// The unique ID - that is, the slug - for this menu item
		'FortyNuggets_submenu_general_page_display',	// The name of the function to call when rendering the menu for this page
		'http://40nuggets.com/dashboard/images/favicon.ico'
	);

	//Login
	add_submenu_page(
		'',					// Register this submenu with the menu defined above
		'Login',						// The text to the display in the browser when this menu item is active
		'Login',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-login',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_login_page_display' 	// The function used to render the menu for this page to the screen
	);

} 

function FortyNuggets_submenu_general_page_display() {
	require_once(dirname(__FILE__) . '/general.php');	
}

function FortyNuggets_submenu_login_page_display() {
	require_once(dirname(__FILE__) . '/login.php');	
}

?>