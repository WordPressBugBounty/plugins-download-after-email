<?php
/*
Plugin Name: Download After Email
Plugin URI: https://www.download-after-email.com/
Description: Subscribe & Download plugin for gaining subscribers by offering free downloads.
Version: 2.1.6
Author: MK-Scripts
Text Domain: download-after-email
Domain Path: /languages
*/

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DAE_VERSION', '2.1.6' );

if( ! function_exists( 'mckp_function_exists' ) ) {
	
	function mckp_function_exists( $functions ) {
		
		$match = false;
		
		if( ! is_array( $functions ) ) {
			$functions = array( $functions );
		}
		
		foreach( $functions as $function ) {
			
			if(
				function_exists( $function )
				|| has_action( 'wp_ajax_' . $function )
				|| has_action( 'wp_ajax_nopriv_' . $function )
			) {
				$match = true;
			}
			
		}
		
		if( true == $match ) {
			
			if( ! has_action( 'admin_notices', 'mckp_content_admin_notice' ) ) {
				
				add_action( 'admin_notices', 'mckp_content_admin_notice' );
				function mckp_content_admin_notice() {
					
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php esc_html_e( '"Download after Email" is using the same name for a variable, function or a class as another plugin and may not function as expected. ', 'download-after-email' ); ?></p>
					</div>
					<?php
					
				}
				
			}
			
		}
		
		return $match;
		
	}
	
}

if( ! mckp_function_exists( 'dae_load_plugin_textdomain' ) ) {
	
	add_action( 'init', 'dae_load_plugin_textdomain' );
	function dae_load_plugin_textdomain() {
		load_plugin_textdomain( 'download-after-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
}

if( ! mckp_function_exists( 'dae_activation' ) && is_admin() ) {

	register_activation_hook( __FILE__, 'dae_activation' );
	function dae_activation( $network_wide ) {

		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			$sites = get_sites( array( 'fields' => 'ids' ) );
		} else {
			$sites = array( 1 );
		}

		foreach ( $sites as $site ) {

			if ( is_multisite() && $network_wide ) {
				switch_to_blog( $site );
			}

			$table_subscribers = $wpdb->prefix . 'dae_subscribers';
			$table_subscribermeta = $wpdb->prefix . 'dae_subscribermeta';
			$table_links = $wpdb->prefix . 'dae_links';
			$table_linkmeta = $wpdb->prefix . 'dae_linkmeta';

			$charset_collate = $wpdb->get_charset_collate();

			$sql[] = "CREATE TABLE $table_subscribers (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				KEY time (time),
				PRIMARY KEY  (id)
			) $charset_collate;";

			$sql[] = "CREATE TABLE $table_subscribermeta (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				subscriber_id bigint(20) UNSIGNED NOT NULL,
				meta_key varchar(190) NOT NULL,
				meta_value varchar(190) NOT NULL,
				KEY subscriber_id (subscriber_id),
				KEY meta_key (meta_key),
				KEY meta_value (meta_value),
				PRIMARY KEY  (id)
			) $charset_collate;";

			$sql[] = "CREATE TABLE $table_links (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				subscriber_id bigint(20) UNSIGNED NOT NULL,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				time_used datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				ip varchar(100) NOT NULL,
				ip_used varchar(100) NOT NULL,
				form_content text NOT NULL,
				file varchar(190) NOT NULL,
				link_used varchar(20) NOT NULL,
				KEY subscriber_id (subscriber_id),
				KEY time (time),
				KEY time_used (time_used),
				KEY file (file),
				KEY link_used (link_used),
				PRIMARY KEY  (id)
			) $charset_collate;";

			$sql[] = "CREATE TABLE $table_linkmeta (
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
			dbDelta( $sql );

			add_option( 'dae_field_labels', array( 'Email' ), '', false );
			add_option( 'dae_fields', array(
				'email_visible'	=> 'visible',
				'email_type'	=> 'email'
			), '', false );
			add_option( 'dae_messages', array(), '', false );
			add_option( 'dae_subscribers_per_page', 25, '', false );
			add_option( 'dae_options', array(), '', false );

			dae_set_db_version();

			dae_setup_uploads_folder();

			if ( is_multisite() && $network_wide ) {
				restore_current_blog();
			}

		}

	}

}

if( ! mckp_function_exists( 'dae_deactivation' ) && is_admin() ) {

	register_deactivation_hook( __FILE__, 'dae_deactivation' );
	function dae_deactivation( $network_wide ) {

		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			$sites = get_sites( array( 'fields' => 'ids' ) );
		} else {
			$sites = array( 1 );
		}

		foreach ( $sites as $site ) {

			if ( is_multisite() && $network_wide ) {
				switch_to_blog( $site );
			}

			$dae_options = get_option( 'dae_options' );

			if( ! empty( $dae_options['delete_messages'] ) ) {
				delete_option( 'dae_messages' );
			}

			if( ! empty( $dae_options['delete_subscribers'] ) ) {

				$table_names = array(
					$wpdb->prefix . 'dae_subscribers',
					$wpdb->prefix . 'dae_subscribermeta',
					$wpdb->prefix . 'dae_links',
					$wpdb->prefix . 'dae_linkmeta'
				);

				foreach( $table_names as $table_name ) {
					$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
				}

			}

			if( ! file_exists( plugin_dir_path( __DIR__ ) . 'dae-plus/dae-plus.php' ) ) {

				delete_option( 'dae_field_labels' );
				delete_option( 'dae_fields' );

			}

			do_action( 'dae_deactivation' );

			if ( is_multisite() && $network_wide ) {
				restore_current_blog();
			}

		}

	}

}

if( ! mckp_function_exists( 'dae_uninstall' ) && is_admin() ) {
	
	register_uninstall_hook( __FILE__, 'dae_uninstall' );
	function dae_uninstall() {

		if ( is_multisite() ) {
			$sites = get_sites( array( 'fields' => 'ids' ) );
		} else {
			$sites = array( 1 );
		}

		foreach ( $sites as $site ) {

			if ( is_multisite() ) {
				switch_to_blog( $site );
			}

			delete_option( 'dae_subscribers_per_page' );
			delete_option( 'dae_options' );
			delete_option( 'dae_db_version' );

			if ( is_multisite() ) {
				restore_current_blog();
			}

		}

	}

}

if( ! mckp_function_exists( 'dae_wp_enqueue_scripts' ) && ! is_admin() ) {
	
	add_action( 'wp_enqueue_scripts', 'dae_wp_enqueue_scripts' );
	function dae_wp_enqueue_scripts() {
		
		wp_enqueue_style( 'dae-download', plugins_url( '/css/download.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/download.css' ), 'all' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'dae-fa', plugins_url( '/css/all.css', __FILE__ ) );
		
		wp_enqueue_script( 'dae-media-query', plugins_url( '/js/media-query.js', __FILE__ ), array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/media-query.js' ), true );
		
		wp_enqueue_script( 'dae-download', plugins_url( '/js/download.js', __FILE__ ), array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/download.js' ), true );
		$download_nonce = wp_create_nonce( 'dae_download' );
		wp_localize_script( 'dae-download', 'objDaeDownload', array(
			'ajaxUrl'	=> admin_url( 'admin-ajax.php' ),
			'nonce'		=> $download_nonce,
		));
		
	}
	
}

if( ! mckp_function_exists( 'dae_admin_enqueue_scripts' ) && is_admin() ) {
	
	add_action( 'admin_enqueue_scripts', 'dae_admin_enqueue_scripts' );
	function dae_admin_enqueue_scripts( $hook ) {

		dae_enqueue_update();
		
		if( ( $hook == 'post.php' || $hook == 'post-new.php' ) && get_post_type() == 'dae_download' ) {
			wp_enqueue_media();
		}
		
		if(
			( ( 'post.php' == $hook || 'post-new.php' == $hook ) && 'dae_download' == get_post_type() )
			|| 'dae_download_page_dae-messages' == $hook
			|| 'dae_download_page_dae-subscribers' == $hook
			|| 'dae_download_page_dae-options' == $hook
		) {
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'dae-fa', plugins_url( '/css/all.css', __FILE__ ) );
			wp_enqueue_style( 'dae-admin', plugins_url( '/css/dae-admin.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/dae-admin.css' ), 'all' );
			
			wp_enqueue_script( 'dae-admin', plugins_url( '/js/dae-admin.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/dae-admin.js' ), true );
			$dae_admin_nonce = wp_create_nonce( 'dae_admin' );
			wp_localize_script( 'dae-admin', 'objDaeAdmin', array(
				'ajaxUrl'			=> admin_url( 'admin-ajax.php' ),
				'previewUrl'		=> home_url() . '/?dae_preview=true',
				'nonce'				=> $dae_admin_nonce,
				'selectFile'		=> __( 'Select File', 'download-after-email' ),
				'select'			=> __( 'Select', 'download-after-email' ),
				'noImage'			=> __( 'No image selected', 'download-after-email' ),
				'noFile'			=> __( 'No file selected', 'download-after-email' ),
				'removeSubscriber'	=> __( 'Are you sure you want to remove this subscriber, including the attached data of the downloadlinks?', 'download-after-email' ),
			));
			
		}

		if( ( $hook == 'post.php' || $hook == 'post-new.php' ) && get_post_type() == 'dae_download' ) {

			$upload_dir = wp_upload_dir();
			$dirname = '';
			
			if ( ! empty( $upload_dir['basedir'] ) ) {
				$dirname = $upload_dir['basedir'] . '/dae-uploads';
			}
	
			if ( ! file_exists( $dirname ) ) {
	
				add_action( 'admin_notices', function() {
	
					?>
					<div class="notice notice-error is-dismissible">
						<p>
							<span><?php esc_html_e( 'The dae-uploads folder could not be created within your Wordpress uploads folder during the activation of Download After Email. Try to adjust the permissions of your uploads folder and re-activate the plugin.', 'download-after-email' ); ?></span>
							<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank"><?php esc_html_e( 'More information about changing file permissions for Wordpress.', 'download-after-email' ); ?></a>
						</p>
					</div>
					<?php
	
				} );
	
			}
	
			if ( ! function_exists( 'mime_content_type' ) ) {
	
				add_action( 'admin_notices', function() {
	
					?>
					<div class="notice notice-error is-dismissible">
						<p>
							<span><?php esc_html_e( 'The php_fileinfo extension is currently disabled in your PHP configuration settings. Download After Email needs this setting to be enabled for certain functionalities.', 'download-after-email' ); ?></span>
						</p>
					</div>
					<?php
	
				} );
	
			}
	
		}
		
	}
	
}

if( ! mckp_function_exists( array(
	'mckp_create_nonce',
	'mckp_verify_nonce',
	'mckp_delete_nonce',
	'mckp_get_client_ip',
	'mckp_content_media',
	'mckp_sanitize_form_content',
	'mckp_get_links_count',
	'dae_get_download_file_name',
	'dae_set_db_version'
) ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php' );
}

if ( ! class_exists( 'DAE_Subscriber' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-dae-subscriber.php' );
}

if( ! mckp_function_exists( array(
	'dae_content_shortcode_css_return',
	'dae_content_shortcode_return',
	'dae_shortcodes_init',
	'dae_content_shortcode',
	'dae_send_downloadlink',
	'dae_mail_alt_body',
	'dae_filter_email_content_type',
	'dae_filter_from_email',
	'dae_filter_from_name',
	'dae_add_embedded_images'
) ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php' );
}

if( ! mckp_function_exists( 'dae_download_file' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/download.php' );
}

if( ! mckp_function_exists( 'dae_content_preview' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/preview.php' );
}

if( is_admin() ) {
	
	if( ! mckp_function_exists( array(
		'dae_post_types_init',
		'dae_download_updated_messages',
		'dae_add_meta_boxes_download',
		'dae_content_meta_box_settings_background',
		'dae_content_meta_box_settings',
		'dae_change_background_type',
		'dae_content_meta_box_shortcode',
		'dae_content_meta_box_preview',
		'dae_content_meta_box_duplicate',
		'dae_save_meta_boxes_download',
		'dae_open_preview',
		'dae_set_custom_edit_download_columns',
		'dae_custom_download_column'
	) ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/post-types.php' );
	}
	
	if( ! mckp_function_exists( array(
		'dae_sanitize_cb_html',
		'dae_sanitize_cb_text',
		'dae_settings_init',
		'dae_add_menu_pages',
		'dae_content_messages',
		'dae_content_subscribers_table',
		'dae_content_subscribers',
		'dae_search_subscribers',
		'dae_change_page_subscribers',
		'dae_remove_subscriber',
		'dae_content_subscribers_premium',
		'dae_content_options',
		'dae_admin_footer_text'
	) ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/admin-menu.php' );
	}

	if( ! mckp_function_exists( array(
		'dae_content_update_admin_notice',
		'dae_add_update_admin_notice',
		'dae_enqueue_update',
		'dae_update_database'
	) ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/update.php' );
	}
	
}

?>