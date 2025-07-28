<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mckp_create_nonce( $file, $email, $link_id ) {

	$link_id = (int) $link_id;
	$db_version = get_option( 'dae_db_version' );
	
	$data = $file . '|' . time() . '|' . wp_get_session_token();
	$nonce = wp_hash( $data, 'nonce' );
	
	if ( version_compare( $db_version, '1.1', '<' ) ) {

		$option_name = 'mckp_download_nonce-' . substr( wp_hash( $file . '-' . $email, 'nonce' ), -12, 10 );
		update_option( $option_name, $nonce, false );
	
	} else {

		global $wpdb;
		$table_linkmeta = $wpdb->prefix . 'dae_linkmeta';

		$option_name = 'nonce-' . substr( wp_hash( $file . '-' . $email, 'nonce' ), -12, 10 );
		$wpdb->insert(
			$table_linkmeta,
			array(
				'link_id' 		=> $link_id,
				'meta_key'		=> $option_name,
				'meta_value'	=> $nonce
			),
			array( '%d', '%s', '%s' )
		);

	}
	
	return $nonce;
	
}

function mckp_verify_nonce( $file, $email ) {

	$db_version = get_option( 'dae_db_version' );
	
	if ( version_compare( $db_version, '1.1', '<' ) ) {

		$option_name = 'mckp_download_nonce-' . substr( wp_hash( $file . '-' . $email, 'nonce' ), -12, 10 );
		$option_value = get_option( $option_name );

		$option_name_old = 'mckp_download_nonce-' . $file . '-' . $email;
		$option_value_old = get_option( $option_name_old );
		
		if ( ! empty( $option_value ) || ! empty( $option_value_old ) ) {
			
			if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
				$nonce = sanitize_text_field( $_POST['nonce'] );
			} elseif ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
				$nonce = sanitize_text_field( $_GET['nonce'] );
			}

			if ( ! empty( $option_value ) && hash_equals( $option_value, $nonce ) ) {
				return true;
			}

			if ( ! empty( $option_value_old ) && hash_equals( $option_value_old, $nonce ) ) {
				return true;
			}

			return false;
			
		} else {
			
			return false;
			
		}

	} else {

		global $wpdb;
		$table_links = $wpdb->prefix . 'dae_links';
		$table_linkmeta = $wpdb->prefix . 'dae_linkmeta';

		$meta_key = 'nonce-' . substr( wp_hash( $file . '-' . $email, 'nonce' ), -12, 10 );
		$linkmeta_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_linkmeta WHERE meta_key = %s ORDER BY id DESC LIMIT 1", $meta_key ) );

		if ( empty( $linkmeta_row ) ) {
			return false;
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$nonce = sanitize_text_field( $_POST['nonce'] );
		} elseif ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			$nonce = sanitize_text_field( $_GET['nonce'] );
		}

		if ( ! empty( $linkmeta_row->meta_value ) && ! empty( $nonce ) && hash_equals( $linkmeta_row->meta_value, $nonce ) ) {

			$options = get_option( 'dae_options' );
			if ( empty( $options['limit_links'] ) ) {
				$options['limit_links'] = 1;
			}

			if ( 'unlimited' == $options['limit_links'] ) {

				return true;

			} elseif ( 'once' == $options['limit_links'] ) {

				if ( 0 == $linkmeta_row->link_id ) {

					$wpdb->delete(
						$table_linkmeta,
						array( 'meta_key' => $linkmeta_row->meta_key ),
						array( '%s' )
					);

					return true;

				} else {

					$link_used = $wpdb->get_var( $wpdb->prepare( "SELECT link_used FROM $table_links WHERE id = %d LIMIT 1", $linkmeta_row->link_id ) );

					if ( 'used' == $link_used ) {
						return false;
					} else {
						return true;
					}

				}

			} else {

				if ( 0 == $linkmeta_row->link_id ) {

					$wpdb->delete(
						$table_linkmeta,
						array( 'meta_key' => $linkmeta_row->meta_key ),
						array( '%s' )
					);

					return true;

				}

				$options['limit_links'] = (int) $options['limit_links'];

				$current_time = current_time( 'timestamp' );
				$link_date = $wpdb->get_var( $wpdb->prepare( "SELECT time FROM $table_links WHERE id = %d LIMIT 1", $linkmeta_row->link_id ) );
				$link_date = strtotime( $link_date );
				$expiration_time = $link_date + $options['limit_links'] * 3600;

				if ( $current_time > $expiration_time ) {
					return false;
				} else {
					return true;
				}

			}

		} else {

			return false;
			
		}

	}
	
}

function mckp_delete_nonce( $file, $email ) {
	
	$option_name_old = 'mckp_download_nonce-' . $file . '-' . $email;
	delete_option( $option_name_old );

	$option_name = 'mckp_download_nonce-' . substr( wp_hash( $file . '-' . $email, 'nonce' ), -12, 10 );
	delete_option( $option_name );
	
}

function mckp_get_client_ip() {

	$ipaddress = '';

	// Check for X-Forwarded-For header (may contain multiple IPs)
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
		foreach ( $ips as $ip ) {
			$ip = trim( $ip );
			if (
				filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE )
			) {
				$ipaddress = $ip;
				break;
			}
		}
	}

	// Fallback to HTTP_CLIENT_IP
	if ( empty( $ipaddress ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		if (
			filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE )
		) {
			$ipaddress = $ip;
		}
	}

	// Fallback to REMOTE_ADDR
	if ( empty( $ipaddress ) && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (
			filter_var( $ip, FILTER_VALIDATE_IP )
		) {
			$ipaddress = $ip;
		}
	}

	if ( empty( $ipaddress ) ) {
		$ipaddress = 'Unknown';
	}

	$ipaddress = apply_filters( 'dae_ip_address', $ipaddress );

	return $ipaddress;

}

function mckp_content_media( $media_id, $media_input_name, $image ) {
	
	if ( $image ) {
		
		if ( ! empty( $media_id ) ) {
			
			$media_url = wp_get_attachment_thumb_url( $media_id );
			$url_parts = explode( '/', $media_url );
			$media_name = end( $url_parts );
			$media_class = 'mk-media';
			$media_class_remove = 'mk-media-remove dashicons dashicons-no';
			
		} else {
			
			$media_url = '';
			$media_name = __( 'No image selected', 'download-after-email' );
			$media_class = 'mk-media dashicons dashicons-format-image';
			$media_class_remove = 'mk-media-remove';
			
		}
		
		?>
		<input type="hidden" name="<?php echo esc_attr( $media_input_name ); ?>" value="<?php echo esc_attr( $media_id ); ?>" />
		<a class="<?php echo esc_attr( $media_class ); ?>" title="<?php echo esc_attr( $media_name ); ?>"><img src="<?php echo esc_url( $media_url ); ?>" /></a>
		<span class="<?php echo esc_attr( $media_class_remove ); ?>"></span>
		<?php
		
	} else {
		
		if ( ! empty( $media_id ) ) {
			
			$file_name = basename( get_attached_file( $media_id, true ) );
			$media_class_remove = 'mk-media-remove dashicons dashicons-no';
			
		} else {
			
			$file_name = __( 'No file selected', 'download-after-email' );
			$media_class_remove = 'mk-media-remove';
			
		}
		
		?>
		<input type="hidden" name="<?php echo esc_attr( $media_input_name ); ?>" value="<?php echo esc_attr( $media_id ); ?>" />
		<span class="<?php echo esc_attr( $media_class_remove ); ?>"></span>
		<span class="mk-media-filename"><?php echo esc_html( $file_name ); ?></span>
		<button class="mk-media button" type="button"><?php esc_html_e( 'Select File', 'download-after-email' ); ?></button>
		<?php
		
	}
	
}

function mckp_sanitize_form_content( $form_content ) {

	$allowed_tags = wp_kses_allowed_html( 'post' );

	$allowed_tags['form'] = array(
		'class'			=> true,
		'id'			=> true,
		'method'		=> true,
		'action'		=> true,
		'novalidate'	=> true,
		'autocomplete'	=> true
	);
	$allowed_tags['input'] = array(
		'type'			=> true,
		'class'			=> true,
		'id'			=> true,
		'name'			=> true,
		'value'			=> true,
		'placeholder'	=> true
	);
	$allowed_tags['select'] = array(
		'class'			=> true,
		'id'			=> true,
		'name'			=> true
	);
	$allowed_tags['option'] = array(
		'class'			=> true,
		'id'			=> true,
		'value'			=> true
	);
	
	return wp_kses( $form_content, $allowed_tags );

}

function mckp_get_links_count( $file_name ) {

	global $wpdb;
	$table_links = $wpdb->prefix . 'dae_links';

	$used_links = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $table_links WHERE file = %s AND link_used = %s", array( $file_name, 'used' ) ) );
	$unused_links = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $table_links WHERE file = %s AND link_used = %s", array( $file_name, 'not used' ) ) );

	return array(
		'used'		=> count( $used_links ),
		'unused'	=> count( $unused_links ),
		'total'		=> count( $used_links ) + count( $unused_links )
	);

}

function dae_get_download_file_name( $file_id ) {

	$file_path = get_attached_file( $file_id, true );

	if ( empty( $file_path ) ) {
		$file_url = wp_get_attachment_url( $file_id );
	} else {
		$file_name = basename( $file_path );
	}

	if ( empty( $file_name ) && ! empty( $file_url ) ) {
		$file_name = strtok( basename( $file_url ), '?' );
	} elseif ( empty( $file_name ) ) {
		$file_name = '';
	}
	
	return $file_name;

}

function dae_set_db_version() {

    global $wpdb;
    $table_options = $wpdb->prefix . 'options';

    $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(option_id) FROM $table_options WHERE option_name LIKE %s", 'mckp_download_nonce%' ) );

    if ( empty( $count ) ) {
        update_option( 'dae_db_version', '1.1', false );
    } else {
        update_option( 'dae_db_version', '1.0', false );
    }

}

function dae_setup_uploads_folder() {

	$upload_dir = wp_upload_dir();

	if ( empty( $upload_dir['basedir'] ) ) {
		return;
	}

	$dirname = $upload_dir['basedir'] . '/dae-uploads';

	if ( ! file_exists( $dirname ) ) {
		wp_mkdir_p( $dirname );
	}

	if ( ! file_exists( $dirname ) ) {
		return;
	}

	$file_path = $dirname . '/.htaccess';

	$marker = 'DAE deny access download files';

	$insertion = '
	<IfModule !authz_core_module>
		Order Deny,Allow
		Deny from all
		<FilesMatch "\.(jpg|jpeg)$">
			Allow from all
		</FilesMatch>
	</IfModule>
	<IfModule authz_core_module>
		Require all denied
		<FilesMatch "\.(jpg|jpeg)$">
			<RequireAll>
				Require all granted
			</RequireAll>
		</FilesMatch>
	</IfModule>
	';

	insert_with_markers( $file_path, $marker, $insertion );

}

function dae_check_ajax_nonce() {

	if ( empty( $_POST['file'] ) || empty( $_POST['dae_nonce'] ) ) {
		return false;
	}

	$file = basename( sanitize_text_field( $_POST['file'] ) );
	$action = 'dae_download_' . $file;

	return check_ajax_referer( $action, 'dae_nonce', false ) !== false;

}

function dae_rate_limit_check( $action, $max_requests = 5, $window_seconds = 60 ) {

	$ip = mckp_get_client_ip();
	$key = 'dae_rate_limit_' . $action . '_' . md5( $ip );
	$data = get_transient( $key );
	$now = time();

	if ( ! $data || ! is_array( $data ) || $now > $data['window_start'] + $window_seconds ) {
		$data = array( 'count' => 1, 'window_start' => $now );
		set_transient( $key, $data, $window_seconds );
		return true;
	} elseif ( $data['count'] < $max_requests ) {
		$data['count']++;
		set_transient( $key, $data, $window_seconds - ( $now - $data['window_start'] ) );
		return true;
	} else {
		return false;
	}

}

function dae_get_download_filepath( $file ) {

	// Exclude dotfiles (files starting with a dot)
    if ( strpos( $file, '.' ) === 0 ) {
        return false;
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
        return false;
	} else {
		return $filepath;
	}

}

?>