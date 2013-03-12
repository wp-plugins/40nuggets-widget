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

} 


/**
 * General submenu
 */
add_action('admin_menu', 'FortyNuggets_create_submenu_page'); 
function FortyNuggets_create_submenu_page() {
	//fix bug: http://wordpress.org/support/topic/top-level-menu-duplicated-as-submenu-in-admin-section-plugin
	add_submenu_page(
		'40Nuggets',					// Register this submenu with the menu defined above
		'',						// The text to the display in the browser when this menu item is active
		'',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_general_page_display' 	// The function used to render the menu for this page to the screen
	);
	
	//General
	add_submenu_page(
		'40Nuggets',					// Register this submenu with the menu defined above
		'40Nuggets General Settings',						// The text to the display in the browser when this menu item is active
		'General',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-general',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_general_page_display' 	// The function used to render the menu for this page to the screen
	);

	//Newsletter
	add_submenu_page(
		'40Nuggets',					// Register this submenu with the menu defined above
		'Newsletter',						// The text to the display in the browser when this menu item is active
		'Newsletter',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-newsletter',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_newsletter_page_display' 	// The function used to render the menu for this page to the screen
	);

	//Analytics
	add_submenu_page(
		'40Nuggets',					// Register this submenu with the menu defined above
		'Audience',						// The text to the display in the browser when this menu item is active
		'Audience',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-users',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_users_page_display' 	// The function used to render the menu for this page to the screen
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

	//Import
	add_submenu_page(
		'',					// Register this submenu with the menu defined above
		'Import',						// The text to the display in the browser when this menu item is active
		'Import',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-import',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_import_page_display' 	// The function used to render the menu for this page to the screen
	);

	//Add User
	add_submenu_page(
		'',					// Register this submenu with the menu defined above
		'Add New User',						// The text to the display in the browser when this menu item is active
		'Add New User',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-add-new-user',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_add_new_user_page_display' 	// The function used to render the menu for this page to the screen
	);

	//Test
	add_submenu_page(
		'',					// Register this submenu with the menu defined above
		'Test',						// The text to the display in the browser when this menu item is active
		'Test',						// The text for this menu item
		'administrator',			// Which type of users can see this menu
		'40Nuggets-test',			// The unique ID - the slug - for this menu item
		'FortyNuggets_submenu_test_page_display' 	// The function used to render the menu for this page to the screen
	);

}

function FortyNuggets_submenu_general_page_display() {
	require_once(dirname(__FILE__) . '/general.php');	
}

function FortyNuggets_submenu_users_page_display() {
	require_once(dirname(__FILE__) . '/users.php');	
}

function FortyNuggets_submenu_newsletter_page_display() {
	require_once(dirname(__FILE__) . '/newsletter.php');	
}

function FortyNuggets_submenu_login_page_display() {
	require_once(dirname(__FILE__) . '/login.php');	
}

function FortyNuggets_submenu_import_page_display() {
	require_once(dirname(__FILE__) . '/import.php');	
}

function FortyNuggets_submenu_add_new_user_page_display() {
	require_once(dirname(__FILE__) . '/add_new_user.php');	
}

function FortyNuggets_submenu_test_page_display() {
	require_once(dirname(__FILE__) . '/test.php');	
}

?>