<?php

add_action('init', 'recommendation_sidebar_widget_register');


function recommendation_sidebar_widget_control_gen($widget_title, $num_of_recommendations, $show_images) {

	//set defaults
	$widget_title = isset($widget_title) ? $widget_title : "You Might Also Like";
	$num_of_recommendations = isset($num_of_recommendations) ? $num_of_recommendations : 5;
	$show_images = isset($show_images) ? $show_images : true;
	
	echo "
		<p>
		   <label for='widget_title'>Title:
			  <input class='widefat'
				 id='widget_title' 
				 name='widget_title'
				 type='text' 
				 value='$widget_title.'/>
		   </label>
		</p>
		<p>
		   <label for='num_of_recommendations'># of recommendations:
			  <input class='widefat'
				 id='num_of_recommendations' 
				 name='num_of_recommendations'
				 type='text' 
				 value='$num_of_recommendations'
			  />
		   </label>
		</p>
		<p>
			<input class='checkbox' type='checkbox'
				id='show_images' name='show_images'";
echo 			$show_images ? "checked='checked'" : "";
echo "			/>
		   <label for='show_images'>Show Images</label>
		</p>
		<input type='hidden'
			  id='recommendation-sidebar-widget-submit' name='recommendation-sidebar-widget-submit'
			  value='1'/>
	";
}

function recommendation_sidebar_widget_control() {
   $options = $newoptions = get_option('recommendation_sidebar_widget');

   if ($_POST['recommendation-sidebar-widget-submit']) {
         $newoptions['widget_title'] = strip_tags(stripslashes($_POST['widget_title']));
         $newoptions['num_of_recommendations'] = strip_tags(stripslashes($_POST['num_of_recommendations']));
         $newoptions['show_images'] = $_POST['show_images'];
   }

   if ($options != $newoptions) {
      $options = $newoptions;
      update_option('recommendation_sidebar_widget', $options);
   }

   recommendation_sidebar_widget_control_gen(
      $options['widget_title'], $options['num_of_recommendations'], $options['show_images']);
}

function recommendation_sidebar_widget_content_gen($args) {
   extract($args);

   $options = get_option('recommendation_sidebar_widget');
   
   $title = empty($options['widget_title']) ? 'You Might Also Like' : $options['widget_title'];
	//TODO: get client id from the right option
   $client_id = empty($options['client_id']) ? '40NM-xxxx-x' : $options['client_id'];
   $num_of_recommendations = empty($options['num_of_recommendations']) ? 5 : $options['num_of_recommendations'];
   $show_images = empty($options['show_images']) ? true : $options['show_images'];
	
   echo $before_widget ;
/*
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
*/
   echo $before_title . $title . $after_title;

   echo "<ul id='fortynm-recommendation-sidebar'>";

	//TODO: get data from json and replace num_of_recommendations with the amount of nuggets in the json
	for ($i=0; $i<$num_of_recommendations ; $i++){
		echo "
			<li>
				<a href='http://40nuggets.com'>";
if ($show_images) echo "<img src='http://40nuggets.com/dashboard/images/logo.png' width='50px' height='50px' alt='' />";
echo "					This is the title
				</a>
			</li>
			";
	}
	
   echo "</ul>";
	
   echo $after_widget;
}

function recommendation_sidebar_widget_register() {
	if (!function_exists('wp_register_sidebar_widget')) {
		return;
	}

   
	wp_register_sidebar_widget(
		'40nm-recommendation-sidebar',        	// your unique widget id
		'40Nuggets Recommendations',  // widget name
		'recommendation_sidebar_widget_content_gen',// callback function
		array(                  			// options
			'description' => 'Show personalized recommendations from your content'
		)
	);
	
	wp_register_widget_control(
		'40nm-recommendation-sidebar',        	// your unique widget id
		'40Nuggets Recommendations',  // widget name
		'recommendation_sidebar_widget_control',	// callback function
		array(                  			// options
			'description' => 'Show personalized recommendations from your content'
		)
	);	  
}


?>