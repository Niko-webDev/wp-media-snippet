(function($) {

    // Bail early if the data from php is not here.
    if ( typeof myUploads === 'undefined' ) {
		return false;
	}

    const   img         = $('.img_preview img'),
            addImg      = $('.img_add'),
            delImg      = $('.img_remove'),
            hidden      = $('.img_id');

    var frame;

    addImg.click(function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if ( frame ) {
            frame.open();
            return;
        }

        // Else create a new one.
        var options = {
            title: 'Select or Upload Your Image',
            multiple: false,
            button: {
                text: 'Add image',
                },
            // Mime type.
            library: {
                type: 'image',
            },
        };

        // Create a new media frame.
        frame = wp.media(options);

        /** Custom parameters.
         *
         * Uses the _wpPluploadSettings object that the wp_plupload_default_settings() function
         * creates in a footer script. That function is called by wp_enqueue_media().
         *
         * POST parameters are stored in the _wpPluploadSettings.defaults.multipart_params object.
         * Files filters are stored in the _wpPluploadSettings.defaults.filters object.
         */


        // Add a custom POST parameter so we can apply specific files restrictions with wp_handle_upload_prefilter.
        // Our POST key is "_my_frontend_page_1", with a value of 1.
        _wpPluploadSettings.defaults.multipart_params._my_frontend_page_1 = 1;

        // Apply file restrictions on the browser side too to avoid unnecessary ajax calls.
        _wpPluploadSettings.defaults.filters.max_file_size = myUploads.maxFileSize;
        _wpPluploadSettings.defaults.filters.mime_types = [{extensions: myUploads.mimeTypes}];


        // When an image is selected in the media frame...
        frame.on( 'select', function() {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            img.attr('src', attachment.url);

            // Send the attachment id to our hidden input.
            hidden.val( attachment.id );

            // Hide the add image link.
            addImg.hide();

            // Show the remove image link.
            delImg.show();
        });

        // Finally, open the modal on click.
        frame.open();
    });


    // DELETE IMAGE ACTION.
    delImg.click(function(e) {

        e.preventDefault();

        // Clear out the preview image.
        img.attr('src', '');

        // Delete the attachment id in our hidden input.
        hidden.val('');

        // Show the add image link.
        addImg.show();

        // Hide the remove image link
        delImg.hide();
    });

})(jQuery);