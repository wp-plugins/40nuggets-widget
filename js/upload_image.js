jQuery(document).ready(function() {
	var fnm_upload = false;

	jQuery('#upload_image_button').click(function() {
		fnm_upload = true;
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	jQuery('#upload_file_button').click(function() {
		fnm_upload = true;
		tb_show('', 'media-upload.php?type=file&amp;TB_iframe=true');
		return false;
	});
	
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html) {
		if (fnm_upload) {
			imgurl = jQuery('img',html).attr('src');
			jQuery('#upload_image').val(imgurl);

			fileurl = jQuery(html).attr('href');
			jQuery('#upload_file').val(fileurl);

			tb_remove();
			formfield = '';

		}else{
			window.original_send_to_editor(html);
		}
	}	
});