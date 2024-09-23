<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dae_content_update_admin_notice( $version, $steps_to_go, $total_steps ) {

	if ( $steps_to_go > 0 ) {
		$message = __( 'Download After Email has been updated successfully! To complete the update process, the database must be updated. It is advisable to make a backup beforehand. Please do not refresh the page during the update process.', 'download-after-email' );
	} else {
		$message = __( 'The database update for Download After Email has been completed!', 'download-after-email' );
	}

	?>
	<p><?php echo esc_html( $message ); ?></p>
	<p><?php echo esc_html( __( 'Database update version', 'download-after-email' ) . ': ' . $version ); ?></p>
	<?php if ( $steps_to_go > 0 ) : ?>
		<form id="dae-update-form" method="post">
			<input type="hidden" name="version" value="<?php echo esc_attr( $version ); ?>" />
			<input type="hidden" name="steps_to_go" value="<?php echo esc_attr( $steps_to_go ); ?>" />
			<input type="hidden" name="total_steps" value="<?php echo esc_attr( $total_steps ); ?>" />
			<p>
				<input class="button" type="submit" value="<?php esc_attr_e( 'Start/Proceed Update', 'download-after-email' ); ?>" />
				<span id="dae-update-form-steps"><i><?php echo esc_html( $steps_to_go . ' ' . __( 'step(s) to go', 'download-after-email' ) ); ?></i></span>
			</p>
		</form>
	<?php endif; ?>
	<?php

}

function dae_add_update_admin_notice( $version, $steps_to_go, $total_steps ) {

	add_action( 'admin_notices', function() use ( $version, $steps_to_go, $total_steps ) {

		?><div id="dae-update-admin-notice" class="notice notice-warning"><?php
			dae_content_update_admin_notice( $version, $steps_to_go, $total_steps );
		?></div><?php

	} );

}

function dae_enqueue_update() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$htaccess_updated = get_option( 'dae_htaccess_updated' );

	if ( empty( $htaccess_updated ) ) {
		dae_setup_uploads_folder();
		update_option( 'dae_htaccess_updated', 1, false );
	}
	
	$db_version = get_option( 'dae_db_version' );
	$update_needed = true;
	global $wpdb;
    $table_options = $wpdb->prefix . 'options';

	if ( version_compare( $db_version, '1.1', '<' ) ) {

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(option_id) FROM $table_options WHERE option_name LIKE %s", 'mckp_download_nonce%' ) );
		$count = empty( $count ) ? (int) 1 : (int) $count;
		$steps = ceil( $count / 2500 );

		dae_add_update_admin_notice( '1.1', $steps, $steps );

	} else {

		$update_needed = false;

	}

	if ( $update_needed ) {

		?><style>
			.dae-loader {
				display: inline-block;
				vertical-align: middle;
				border: 4px solid #f1f1f1;
				border-radius: 50%;
				border-top: 4px solid #0075aa;
				width: 14px;
				height: 14px;
				margin: 0 4px;
				-webkit-animation: daespin 2s linear infinite;
				animation: daespin 2s linear infinite;
			}
			@-webkit-keyframes daespin {
				0% { -webkit-transform: rotate(0deg); }
				100% { -webkit-transform: rotate(360deg); }
			}
			@keyframes daespin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style><?php

		wp_enqueue_script( 'dae-update', plugins_url( '/js/update.js', __DIR__ ), array( 'jquery' ), filemtime( plugin_dir_path( __DIR__ ) . 'js/update.js' ), true );
		wp_localize_script( 'dae-update', 'objDaeUpdate', array(
			'ajaxUrl'	=> admin_url( 'admin-ajax.php' ),
			'nonce'		=> wp_create_nonce( 'dae_update' )
		) );

	}

}

add_action( 'wp_ajax_dae_update_database', 'dae_update_database' );
function dae_update_database() {

	check_ajax_referer( 'dae_update' );

	$version = sanitize_text_field( $_POST['version'] );
	$steps_to_go = (int) $_POST['steps_to_go'];
	$total_steps = (int) $_POST['total_steps'];

	if ( empty( $version ) || empty( $steps_to_go ) || empty( $total_steps ) || ! current_user_can( 'manage_options' ) ) {
		wp_die( '<p id="dae-update-admin-notice-error">' . esc_html__( 'The database update for Download After Email could not be performed because you do not have the correct user permissions.', 'download-after-email' ) . '</p>' );
	}

	@set_time_limit( 0 );
	global $wpdb;

	if ( '1.1' === $version ) {

		$table_options = $wpdb->prefix . 'options';
		$table_linkmeta = $wpdb->prefix . 'dae_linkmeta';

		if ( $steps_to_go === $total_steps ) {

			$options = get_option( 'dae_options' );

			if ( empty( $options['unlimited_links'] ) ) {
				$options['limit_links'] = 'once';
			} else {
				$options['limit_links'] = 'unlimited';
			}

			update_option( 'dae_options', $options, false );

			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_linkmeta (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				link_id bigint(20) UNSIGNED NOT NULL,
				meta_key varchar(190) NOT NULL,
				meta_value varchar(190) NOT NULL,
				KEY link_id (link_id),
				KEY meta_key (meta_key),
				KEY meta_value (meta_value),
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			if ( ! maybe_create_table( $table_linkmeta, $sql ) ) {
				wp_die( '<p id="dae-update-admin-notice-error">' . esc_html__( 'The database update for Download After Email could not be performed because a new database table could not be created.', 'download-after-email' ) . '</p>' );
			}

		}

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM $table_options WHERE option_name LIKE %s ORDER BY option_id ASC LIMIT 2500", 'mckp_download_nonce%' ) );

		foreach ( $results as $result ) {

			if ( strpos( $result->option_name, '@', 1 ) === false ) {
				$meta_key = str_replace( 'mckp_download_', '', $result->option_name );
			} else {
				preg_match( '~mckp_download_nonce-(.+[.][a-z]{3,4})-([^@]+@.+)~i', $result->option_name, $arr_meta_key );
				$meta_key = 'nonce-' . substr( wp_hash( $arr_meta_key[1] . '-' . $arr_meta_key[2], 'nonce' ), -12, 10 );
			}

			$number_rows = $wpdb->insert(
				$table_linkmeta,
				array(
					'link_id' 		=> 0,
					'meta_key'		=> $meta_key,
					'meta_value'	=> $result->option_value
				),
				array( '%d', '%s', '%s' )
			);

			if ( false !== $number_rows ) {
				delete_option( $result->option_name );
			}

		}

	}

	$steps_to_go--;

	dae_content_update_admin_notice( $version, $steps_to_go, $total_steps );

	if ( $steps_to_go === 0 ) {
		update_option( 'dae_db_version', $version, false );
	}

	wp_die();

}

?>