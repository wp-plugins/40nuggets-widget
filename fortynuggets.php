<?php
/*
   Plugin Name: 40Nuggets Smart Auto-Newsletter Creator
   Plugin URI: http://wordpress.org/extend/plugins/40nuggets-widget/
   Version: 1.1.0
   Author: 40Nuggets
   Description: The first WP auto-newsletter creator that utilizes user-adaptive technology. It's automatic blog-to-newsletter creation that's smart, personalized to each subscriber and hassle free. 
   Text Domain: 40Nuggets
   License: GPL3
  */

/*
    "WordPress Plugin Template" Copyright (C) 2011 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see <http://www.gnu.org/licenses/>.
*/

$Fortynuggets_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Fortynuggets_noticePhpVersionWrong() {
    global $Fortynuggets_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "FortyNuggets" requires a newer version of PHP to be running.',  '40Nuggets').
            '<br/>' . __('Minimal version of PHP required: ', '40Nuggets') . '<strong>' . $Fortynuggets_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', '40Nuggets') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function Fortynuggets_PhpVersionCheck() {
    global $Fortynuggets_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Fortynuggets_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Fortynuggets_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Fortynuggets_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('40Nuggets', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
Fortynuggets_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (Fortynuggets_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('fortynuggets_init.php');
    Fortynuggets_init(__FILE__);
}
