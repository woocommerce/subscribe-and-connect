(function($) {
	$(document).ready(function() {
		var frame;
		$( 'a.upload-button' ).on( 'click', function( e ) {
			var $el = $( this );

			event.preventDefault();

			file_path_field = $el.parent().find( '.upload-url' );

			// If the media frame already exists, reopen it.
			if ( frame ) {
			  frame.open();
			  return;
			}

			frame = wp.media({
				title: $el.data( 'uploader-title' ),
			    button: {
			      text: $el.data( 'uploader-button-text' ),
			    },
			    multiple: false,  // Set to true to allow multiple files to be selected
				library:   {
					type: 'image'
				}
			});

			// When an image is selected, run a callback.
			frame.on( 'select', function() {
			  // We set multiple to false so only get one image from the uploader
			  attachment = frame.state().get('selection').first().toJSON();

			  // Do something with attachment.id and/or attachment.url here
			  $( file_path_field ).val( attachment.url );

			  // Small preview of the image
			  $( file_path_field ).parents( '.woothemes-sc-network-item' ).find( '.image-preview img' ).attr( 'src', attachment.url );
			});

			// Finally, open the modal
			frame.open();
		});
	});
})(jQuery);