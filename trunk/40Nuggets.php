<?php
/*
Plugin Name: 40Nuggets Widget
Plugin URI: http://40nuggets.com/plugins/wordpress/
Description: Adds 40Nugets signup form to your sidebar or content without touching code.
Author: 40Nuggets.com, Ltd.
Version: 1.0.2
Author URI: http://40nuggets.com
*/

/*
Copyright 2013 40Nuggets, Ltd.  (email: contact@40nuggets.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License f or more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


function simple_sidebar_widget_control_gen($widget_title, $client_id, $show_button, $button_image) {

//set default title
if (!isset($widget_title)){
	$widget_title = get_bloginfo()." Nuggets";
}
?>
<p>
   <label for="client_id"><?php _e('Cliend ID:', 'simple-sidebar-widget'); ?><font color="red"><i>(required)</i></font>
      <input class="widefat"
         id="client_id" name="client_id"
         type="text" value="<?php echo attribute_escape($client_id); ?>"
      />
   </label>
</p>
<p>
   <label for="widget_title"><?php _e('Title:', 'simple-sidebar-widget'); ?>
      <input class="widefat"
         id="widget_title" name="widget_title"
         type="text" value="<?php echo attribute_escape($widget_title); ?>"
      />
   </label>
</p>
<p>
   <label for="show_button">
      <input class="checkbox" type="checkbox"
         id="show_button" name="show_button"
         <?php echo $show_button ? 'checked="checked"' : ''; ?>
      />
      <?php _e('Use button instead of form', 'simple-sidebar-widget'); ?>
   </label>
</p>
<p>
   <label for="button-image"><?php _e('Button image', 'simple-sidebar-widget'); ?>
      <input class="widefat"
         id="button_image" name="button_image"
         type="text" value="<?php echo attribute_escape($button_image); ?>"
      />
   </label>
</p>
<input type="hidden"
      id="simple-sidebar-widget-submit" name="simple-sidebar-widget-submit"
      value="1"/>
<?php
}

function simple_sidebar_widget_control() {
   $options = $newoptions = get_option('simple_sidebar_widget');

   if ($_POST['simple-sidebar-widget-submit']) {
         $newoptions['widget_title'] = strip_tags(stripslashes($_POST['widget_title']));
         $newoptions['client_id'] = strip_tags(stripslashes($_POST['client_id']));
         $newoptions['button_image'] = strip_tags(stripslashes($_POST['button_image']));
         $newoptions['show_button'] = isset($_POST['show_button']);
   }

   if ($options != $newoptions) {
      $options = $newoptions;
      update_option('simple_sidebar_widget', $options);
   }

   simple_sidebar_widget_control_gen(
      $options['widget_title'], $options['client_id'], $options['show_button'], $options['button_image']);
}

function simple_sidebar_widget_content_gen($args) {
   extract($args);

   $options = get_option('simple_sidebar_widget');
  
   //don't show widget if no client id
   if (empty($options['client_id'])) return;
 
   $show_button = $options['show_button'] ? true : false;
   $title = __($options['widget_title'], 'simple-sidebar-widget');
   
   $client_id =empty($options['client_id']) ?
      __('40NM-xxxx-x', 'simple-sidebar-widget') :
      $options['client_id'];
	
   $button_image =empty($options['button_image']) ?
      __('images/nuggets.png', 'simple-sidebar-widget') :
      $options['button_image'];

   echo $before_widget ;

   echo "
	<!-- 40Nuggets Starts -->
	<script type='text/javascript'>
	  var _40nmcid = '".$client_id ."';
	 
	  (function() {
		var nm = document.createElement('script'); nm.type = 'text/javascript'; nm.async = true;
		nm.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '40nuggets.com/widget/js/40nm.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(nm, s);
	  })();
	  
	  function subscribe(){
		var name = jQuery('#fortynm-contact-form-name').val();
		var email = jQuery('#fortynm-contact-form-email').val();
		
		var url = 'http://40nuggets.com/api1/40nm/subscribe?name=' + name + '&email=' + email;
		url += '&url=' + encodeURIComponent(document.URL);
		url += '&client=' + _40nmcid;
		
		jQuery('.fortynm-contact-send').attr('disabled', true);

		jQuery.ajax({
			url: url,
			dataType: 'jsonp',
			jsonp: 'jsonp_callback',
			cache: false,
			success: function (data) {
				console.log('success');
				jQuery('.fortynm-contact-form').hide();
				jQuery('.fortynm-contact-message')
					.html(jQuery('<p align=\'center\'><br/></p>').append('Welcome aboard!'))
					.fadeIn(200);
			},
			error: function () {
				console.log('error');
				jQuery('.fortynm-contact-form').hide();
				jQuery('.fortynm-contact-message')
					.html(jQuery('<p align=\'center\'><br/></p>').append('Oops, we\'ve got a problem'))
					.fadeIn(200);
			}
		});		
	  }
	</script>
	<!-- 40Nuggets Ends -->
   ";

   echo $before_title . $title . $after_title;

   if (!$show_button) {	  
		echo "
		<div class='fortynm-contact-top'></div>
			<div class='fortynm-contact-loading' style='display:none'></div>
			<div class='fortynm-contact-message' style='display:none'></div>
			<div class='fortynm-contact-form'>
				<input type='hidden' id='fortynm-contact-form-name' class='fortynm-contact-input' name='name' tabindex='1001' />
				<input type='text' id='fortynm-contact-form-email' class='fortynm-contact-input' name='email' tabindex='1002' />
				<button onclick='subscribe();' type='submit' class='fortynm-contact-send fortynm-contact-button' tabindex='1006'>Join</button>
			</div>
		<div class='fortynm-contact-bottom'><a href='http://40nuggets.com/dashboard/info.php' style='font-size:x-small' target='_blank'>Powered by 40Nuggets</a></div>
		<p>&nbsp;</p>
		";

   }else{
	  echo '<a href="#" class="40nm-activate" title="Receive personalized nuggets of related content from '.get_bloginfo().'"><img src="'.$button_image.'" /></a>';
   }
   echo $after_widget;
}

function simple_sidebar_widget_register() {
   if (!function_exists('register_sidebar_widget')) {
         return;
   }

   register_sidebar_widget(__('40Nuggets Widget ', 'simple-sidebar-widget'),
      'simple_sidebar_widget_content_gen');
   register_widget_control(__('40Nuggets Widget ', 'simple-sidebar-widget'),
      'simple_sidebar_widget_control');
}

add_action('init', 'simple_sidebar_widget_register');

?>