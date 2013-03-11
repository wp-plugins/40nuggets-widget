<?php
	$blog_name = get_option('blogname');
	$blog_name = "this is the BEST web@#$site Ever created #10 check us@!#%!";
	$username = preg_replace("/[^a-zA-Z0-9]/", "", $blog_name);
	$username = substr($username,0,20);
	$username = strtolower($username);
	echo "$username@40nuggets.com";
 ?>