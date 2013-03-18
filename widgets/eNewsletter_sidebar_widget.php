<?php

add_action('init', 'eNewsletter_sidebar_widget_register');

function eNewsletter_sidebar_widget_register() {
	if (!function_exists('wp_register_sidebar_widget')) {
		return;
	}
   
	wp_register_widget_control(
		'40nm-subscription-form',        	// your unique widget id
		'40Nuggets eNewsletter',  // widget name
		'eNewsletter_sidebar_widget_control',	// callback function
		array(                  			// options
			'description' => '40Nuggets eNewsletter subscription form'
		)
	);		
	
	wp_register_sidebar_widget(
		'40nm-subscription-form',        	// your unique widget id
		'40Nuggets eNewsletter',  // widget name
		'eNewsletter_sidebar_widget_content_gen',// callback function
		array(                  			// options
			'description' => '40Nuggets eNewsletter subscription form'
		)
	);
}

function eNewsletter_sidebar_widget_control() {
   $options = $newoptions = get_option('40nm_eNewsletter_sidebar_widget');

   if ($GLOBALS['MY_REQUEST']['eNewsletter-sidebar-widget-submit']) {
         $newoptions['widget_title'] = strip_tags(stripslashes($GLOBALS['MY_REQUEST']['widget_title']));
         $newoptions['button_text'] = strip_tags(stripslashes($GLOBALS['MY_REQUEST']['button_text']));
   }

   if ($options != $newoptions) {
      $options = $newoptions;
      update_option('40nm_eNewsletter_sidebar_widget', $options);
   }

   eNewsletter_sidebar_widget_control_gen($options['widget_title'], $options['button_text']);
}

function eNewsletter_sidebar_widget_control_gen($widget_title, $button_text) {
	//set defaults
	$widget_title = isset($widget_title) ? $widget_title : "Get our Newsletter";
	$button_text = isset($button_text) ? $button_text : "Join";
	
	echo "
		<p>
		   <label for='widget_title'>Title:
			  <input class='widefat'
				 id='widget_title' 
				 name='widget_title'
				 type='text' 
				 value='$widget_title'/>
		   </label>
		</p>
		<p>
		   <label for='button_text'>Button text:
			  <input class='widefat'
				 id='button_text' 
				 name='button_text'
				 type='text' 
				 value='$button_text'
			  />
		   </label>
		</p>
		<input type='hidden'
			  id='eNewsletter-sidebar-widget-submit' name='eNewsletter-sidebar-widget-submit'
			  value='1'/>
	";
}


function eNewsletter_sidebar_widget_content_gen($args) {
   extract($args);

   $options = get_option('40nm_eNewsletter_sidebar_widget');
   
   $title = empty($options['widget_title']) ? 'Join our eNewsletter' : $options['widget_title'];
   $client_id = empty($options['client_id']) ? '40NM-xxxx-x' : $options['client_id'];
   $button_text = empty($options['button_text']) ? 'Join' : $options['button_text'];

   echo $before_widget ;

   echo "
	<!-- 40Nuggets Starts -->
	<script type='text/javascript'>
	  (function() {
		var nm = document.createElement('script'); nm.type = 'text/javascript'; nm.async = true;
		nm.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '40nuggets.com/widget/js/40nm.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(nm, s);
	  })();	  
	</script>
	<!-- 40Nuggets Ends -->
   ";

   echo $before_title . $title . $after_title;

		echo "
		<div class='fortynm-contact-top'></div>
			<div class='fortynm-contact-loading' style='display:none'></div>
			<div class='fortynm-contact-message' style='display:none'></div>
			<div class='fortynm-contact-form'>
				<input type='hidden' id='fortynm-contact-form-name' class='fortynm-contact-input' name='name' tabindex='1001' />
				<input type='text' id='fortynm-contact-form-email' class='fortynm-contact-input' name='email' tabindex='1002' />
				<button onclick='fortynmSubscribe();' type='submit' class='fortynm-contact-send fortynm-contact-button' tabindex='1003'>$button_text</button>
			</div>
		<div class='fortynm-contact-bottom'><a href='http://40nuggets.com/dashboard/info.php' style='font-size:x-small' target='_blank'>Powered by 40Nuggets</a></div>
		";

   echo $after_widget;
}



?>