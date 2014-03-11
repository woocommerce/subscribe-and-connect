(function($) {
	$(document).ready(function() {
		var frame;

		$( 'a.preview-link' ).on( 'click', function( e ) {
			if ( $( this ).hasClass( 'remove' ) ) {
				var placeholder_image = $( this ).parents( '.form-table' ).parents( '.form-table' ).find( '.subscribe-and-connect-placeholder-image' ).attr( 'src' );
				$( this ).find( 'img' ).attr( 'src', placeholder_image );
				$( this ).addClass( 'add' ).removeClass( 'remove' );
				return false;
			} else {
				var $el = $( this );

				event.preventDefault();

				file_path_field = $el.parents( '.subscribe-and-connect-network-item' ).find( '.upload-id' );

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
				  var attachment = frame.state().get('selection').first().toJSON();

				  // Do something with attachment.id and/or attachment.url here
				  $( file_path_field ).val( attachment.id );

				  // Small preview of the image
				  $( file_path_field ).parents( '.subscribe-and-connect-network-item' ).find( '.image-preview img' ).attr( 'src', attachment.url );

				  // Swap out the CSS classes
				  $( this ).addClass( 'remove' ).removeClass( 'add' );
				});

				// Finally, open the modal
				frame.open();
			}
		});
	});
})(jQuery);