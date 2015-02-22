// Uploading files
var file_frame;

jQuery('div[data-select-image]').live('click', function( event ){
	image = jQuery( this ).data( 'select-image' );
	console.log();

	event.preventDefault();
	if(event.target.id == image + '-add'){
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Choose ' + image,
			button: {
				text: jQuery( this ).data( 'button-text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
			jQuery( '#' + image + '-img' )
				.show()
				.attr( 'src', attachment.url )
				.attr( 'alt', attachment.caption );

			jQuery('#' + image + '-add').hide();
			jQuery('#' + image + '-remove').show();
			jQuery('.' + image + '-value').val(attachment.id)
		});

		// Finally, open the modal
		file_frame.open();
	}
	if(event.target.id == image + '-remove'){
		jQuery( '#'+event.target.id )
			.hide();
		jQuery( this ).find('img')
			.hide()
			.addClass( 'hidden' );
		jQuery( '#logo-add' )
			.show()
			.removeClass( 'hidden' );
		jQuery( this ).find('input')
			.val( '' );
	}
});



