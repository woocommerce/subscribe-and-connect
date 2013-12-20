jQuery( document ).ready( function ( e ) {
	jQuery( '.woothemes-sc-network-fields tbody' ).sortable();
	jQuery( '.woothemes-sc-network-fields tbody' ).disableSelection();

	jQuery( '.woothemes-sc-network-fields tbody' ).bind( 'sortstop', function ( e, ui ) {
		var orderString = '';

		jQuery( e.target ).find( 'tr' ).each( function ( i, e ) {
			if ( i > 0 ) { orderString += ','; }
			orderString += jQuery( this ).attr( 'id' );
		});

		jQuery( 'input[id="networks_order"]' ).attr( 'value', orderString );
	});
});