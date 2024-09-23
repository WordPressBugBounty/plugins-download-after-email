jQuery( document ).ready( function( $ ) {
	
	function mkAjax( action, form, objData, callback ) {
		
		var formdata = new FormData( form[0] ),
			array = [],
			arrayLength,
			i;
		
		formdata.append( '_ajax_nonce', objDaeUpdate.nonce );
		formdata.append( 'action', action );
		
		for ( x in objData ) {
			
			if ( $.isArray( objData[ x ] ) ) {
				
				array = objData[ x ];
				arrayLength = array.length;
				
				for ( i = 0; i < arrayLength; i++ ) {
					formdata.append( x + '[]', array[ i ] );
				}
				
			} else {
				
				formdata.append( x, objData[ x ] );
				
			}
			
		}
		
		$.ajax( {
			url: objDaeUpdate.ajaxUrl,
			type: 'POST',
			data: formdata,
			contentType: false,
			cache: false,
			processData: false,
			success: callback
		} );
		
	}
	
	$( 'body' ).on( 'submit', '#dae-update-form', function( e ) {

		var form = $( this );

		e.preventDefault();
		
		$( '#dae-update-form-steps' ).hide().after( '<div class="dae-loader"></div>' );
		form.find( 'input[type=submit]' ).attr( 'disabled', 'disabled' );

		mkAjax( 'dae_update_database', form, {}, function( data ) {

			$( '#dae-update-admin-notice' ).html( data );

			if ( data.search( 'dae-update-form' ) === -1 && data.search( 'dae-update-admin-notice-error' ) === -1 ) {
				$( '#dae-update-admin-notice' ).attr( 'class', 'notice notice-success' );
			}

			$( '#dae-update-form-steps' ).show();
			$( '.dae-loader' ).remove();
			form.find( 'input[type=submit]' ).removeAttr( 'disabled' );

		} );

	} );

} );