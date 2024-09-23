jQuery( document ).ready( function( $ ) {
	
	function mkAjax( action, form, objData, callback ) {
		
		var formdata = new FormData( form[0] ),
			array = [],
			arrayLength,
			i;
		
		formdata.append( '_ajax_nonce', objDaeAdmin.nonce );
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
			url: objDaeAdmin.ajaxUrl,
			type: 'POST',
			data: formdata,
			contentType: false,
			cache: false,
			processData: false,
			success: callback
		} );
		
	}
	
	$( '.dae-colorpicker' ).wpColorPicker();
	
	$( 'body' ).on( 'click', '.mk-media', function( e ) {
		
		e.preventDefault();
		
		var mkMedia = $( this );
		
		frame = wp.media( {
		  title: objDaeAdmin.selectFile,
		  button: {
			text: objDaeAdmin.select
		  },
		  multiple: false
		} );
		
		frame.on( 'select', function() {
			
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			
			if ( 'image' === attachment.type && 'image/vnd.dwg' !== attachment.mime ) {
				
				mkMedia.prev().val( attachment.id );
				
				if ( attachment.sizes.thumbnail ) {
					mkMedia.find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
				} else {
					mkMedia.find( 'img' ).attr( 'src', attachment.sizes.full.url );
				}
				
				mkMedia.attr( 'title', attachment.filename ).removeClass( 'dashicons dashicons-format-image' );
				mkMedia.next().addClass( 'dashicons dashicons-no' );
				
			} else {
				
				mkMedia.prev().prev().prev().val( attachment.id );
				mkMedia.prev().html( attachment.filename );
				mkMedia.prev().prev().addClass( 'dashicons dashicons-no' );
				
			}
			
		} );
		
		frame.open();
		
	} );
	
	$( 'body' ).on( 'click', '.mk-media-remove', function() {
		
		if ( $( this ).prev().is( 'a' ) ) {
			
			$( this ).prev().prev().val( '' );
			$( this ).prev().find( 'img' ).attr( 'src', '' );
			$( this ).prev().attr( 'title', objDaeAdmin.noImage ).addClass( 'dashicons dashicons-format-image' );
			$( this ).removeClass( 'dashicons dashicons-no' );
			
		} else {
			
			$( this ).prev().val( '' );
			$( this ).next().html( objDaeAdmin.noFile );
			$( this ).removeClass( 'dashicons dashicons-no' );
			
		}
		
	} );
	
	$( '#dae-download-background-type' ).change( function() {
		
		var backgroundType = $( this ).val();
		
		mkAjax( 'dae_change_background_type', '', {
			background_type: backgroundType
		},function( data ) {
			$( '#dae-download-background-type' ).parent().next().replaceWith( data );
			if ( 'color' === backgroundType ) {
				$( '#dae-download-background-type' ).parent().next().find( 'input' ).wpColorPicker();
			}
		} );
		
	} );
	
	$( '#dae-download-preview-button' ).click( function() {
		
		var downloadTitle = $( '#titlewrap input' ).val(),
			downloadText = $( '.wp-editor-area' ).val(),
			daeSettings = {},
			previewCSS = $( '#dae-download-preview-css' ).val(),
			newWindow = window.open();
			
		$( '#dae-download-tables tr' ).each( function() {
			
			if ( $( this ).find( 'input[type="text"]' ).attr( 'name' ) ) {
				daeSettings[ $( this ).find( 'input[type="text"]' ).attr( 'name' ) ] = $( this ).find( 'input[type="text"]' ).val();
			}
			if ( $( this ).find( 'input[type="hidden"]' ).attr( 'name' ) ) {
				daeSettings[ $( this ).find( 'input[type="hidden"]' ).attr( 'name' ) ] = $( this ).find( 'input[type="hidden"]' ).val();
			}
			if ( $( this ).find( 'select' ).attr( 'name' ) ) {
				daeSettings[ $( this ).find( 'select' ).attr( 'name' ) ] = $( this ).find( 'select' ).val();
			}
			if ( $( this ).find( 'textarea' ).attr( 'name' ) ) {
				daeSettings[ $( this ).find( 'textarea' ).attr( 'name' ) ] = $( this ).find( 'textarea' ).val();
			}
			
		} );
		
		$.post( objDaeAdmin.ajaxUrl, {
		   _ajax_nonce: objDaeAdmin.nonce,
			action: 'dae_open_preview',
			download_title: downloadTitle,
			download_text: downloadText,
			dae_settings: daeSettings,
			preview_css: previewCSS
		}, function( data ) {
			newWindow.location.href = objDaeAdmin.previewUrl;
		} );
		
	} );
	
	$( '#dae-subscribers-search-form' ).submit( function( e ) {
		e.preventDefault();
		mkAjax( 'dae_search_subscribers', $( this ), {}, function( data ) {
			$( '#dae-subscribers-table-wrap' ).html( data );
		} );
	} );
	
	$( 'body' ).on( 'click', '.dae-subscribers-links-icon', function() {
		$( this ).next().toggleClass( 'dae-subscribers-links-open' );
	} );
	
	$( 'body' ).on( 'click', '.dae-subscribers-page-nav', function() {
		
		var searchValue = '',
			currentPage = Number( $( '#dae-subscribers-page' ).text() ),
			countPages = Number( $( '#dae-subscribers-count-pages' ).text() ),
			i = $( this ).find( 'i' ),
			page = 1;
			
		if ( $( '#dae-subscribers-search-value' ) ) {
			searchValue = $( '#dae-subscribers-search-value' ).text();
		}
		
		if ( i.hasClass( 'fa-angle-left' ) ) {
			if ( 1 < currentPage ) {
				page = currentPage - 1;
			}
		}
		
		if ( i.hasClass( 'fa-angle-right' ) ) {
			if ( currentPage < countPages ) {
				page = currentPage + 1;
			} else {
				page = countPages;
			}
		}
		
		if ( i.hasClass( 'fa-angle-double-right' ) ) {
			page = countPages;
		}
		
		mkAjax( 'dae_change_page_subscribers', '', {
			page: page,
			search_value: searchValue
		}, function( data ) {
			$( '#dae-subscribers-table-wrap' ).html( data );
		} );
		
	} );
	
	$( 'body' ).on( 'click', '.dae-subscribers-remove', function() {
		
		var id = $( this ).parent().parent().children().first().text(),
			ok = confirm( objDaeAdmin.removeSubscriber );
		
		if ( ok ) {
			$( this ).parent().parent().remove();
			mkAjax( 'dae_remove_subscriber', '', {
				id: id
			}, function( data ) {} );
		}
		
	} );
	
} );