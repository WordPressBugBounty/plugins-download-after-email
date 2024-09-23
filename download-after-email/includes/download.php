<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'dae_download_file' );
function dae_download_file() {

    if ( empty( $_GET['file'] ) || empty( $_GET['email'] ) || empty( $_GET['nonce'] ) ) {
        return;
    }

    $_GET = stripslashes_deep( $_GET );

    $file = sanitize_text_field( $_GET['file'] );
    $email = sanitize_email( $_GET['email'] );

    $messages = get_option( 'dae_messages' );

    if ( ! mckp_verify_nonce( $file, $email ) ) {
        die( ! empty( $messages['unvalid_link'] ) ? esc_html( $messages['unvalid_link'] ) : esc_html__( 'This link has already been used and is now unavailable.', 'download-after-email' ) );
    }

    if ( $subscriber = DAE_Subscriber::get_instance( $email ) ) {

        if ( empty( $messages['optional_checkbox'] ) && apply_filters( 'dae_run_integrations', true, $subscriber, $file ) ) {

            if ( class_exists( 'DAE_Integrations' ) ) {
                DAE_Integrations::run( $subscriber );
            }

            do_action( 'dae_download_integrations', $subscriber );

        } else {

            if (
                ( ! empty( $subscriber->meta['optional_checkbox'] )
                || ( ! empty( $subscriber->meta['optin_time'] ) && ! $subscriber->has_used_links ) )
                && apply_filters( 'dae_run_integrations_optional', true, $subscriber, $file )
            ) {

                if ( class_exists( 'DAE_Integrations' ) ) {
                    DAE_Integrations::run( $subscriber );
                }

                do_action( 'dae_download_integrations_optional', $subscriber );

            }

        }

        DAE_Subscriber::update_link( $subscriber->id, $file );

    }

    $upload_dir = wp_upload_dir();
    $filepath = $upload_dir['basedir'] . '/dae-uploads/' . $file;

    if ( ! file_exists( $filepath ) ) {
        $filepath = $upload_dir['basedir'] . '/' . $file;
    }

    if ( ! file_exists( $filepath ) ) {
        $filepath = $upload_dir['path'] . '/' . $file;
    }

    if ( ! file_exists( $filepath ) ) {
        die( ! empty( $messages['download_failed'] ) ? esc_html( $messages['download_failed'] ) : esc_html__( 'This download file could not be found. Please try again or feel free to contact us.', 'download-after-email' ) );
	}

	@ini_set( 'zlib.output_compression', 'Off' );

    $file_pointer = @fopen( $filepath, 'rb' );

    if ( ! $file_pointer ) {
        die( 'File could not be opened.' );
    }

    $mime_content_type = mime_content_type( $filepath );
    $file_size  = filesize( $filepath );

    header( "Content-Disposition: attachment; filename=\"$file\"" );
    header( "Pragma: public" );
    header( "Expires: -1" );
    header( "Cache-Control: public, must-revalidate, post-check=0, pre-check=0" );
    header( "Content-Type: $mime_content_type" );
    header( "Content-Length: $file_size" );

    @set_time_limit( 0 );

    while( ! feof( $file_pointer ) ) {

        print( @fread( $file_pointer, 1024 * 8 ) );
        if ( ob_get_level() ) {
            ob_flush();
        }
        flush();

        if ( connection_status() != 0 ) {
            @fclose( $file_pointer );
            die( 'Connection aborted or timed out.' );
        }

    }

    @fclose( $file_pointer );

    $options = get_option( 'dae_options' );
    $db_version = get_option( 'dae_db_version' );

    if ( version_compare( $db_version, '1.1', '<' ) && empty( $options['unlimited_links'] ) ) {
        mckp_delete_nonce( $file, $email );
    }

    exit;

}

?>