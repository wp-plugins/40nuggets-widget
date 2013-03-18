jQuery(document).ready(function($){
	var _custom_media = true,
		_orig_send_attachment = wp.media.editor.send.attachment;
	
	jQuery('#select_from_media_library').click(function(e) {
           
		   var for_element = jQuery(this).attr("data-for");

		   // Create the media frame.
            frame = wp.media.frames.customBackground = wp.media({
				// Set title
				title: jQuery(this).attr("data-title"),
                
				// Set modal library
				library: {
                    type: jQuery(this).attr("data-library-type"),
                },
				
				// Set buton text
                button: {
                    text: jQuery(this).attr("data-button-text"),
                }
            });

            // When an image is selected, run a callback.
            frame.on( 'select', function() {
                // Grab the selected attachment.
                var attachment = frame.state().get('selection').first();
				jQuery("#"+for_element).val(attachment.attributes.url);
			});

            // Finally, open the modal.
            frame.open();			
	});

});
