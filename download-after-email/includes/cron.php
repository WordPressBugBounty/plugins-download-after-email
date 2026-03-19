<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {

	if ( ! wp_next_scheduled( 'dae_cleanup_expired_transients' ) ) {
		wp_schedule_event( time(), 'hourly', 'dae_cleanup_expired_transients' );
	}

} );

add_action( 'dae_cleanup_expired_transients', 'dae_cleanup_expired_transients_callback' );
function dae_cleanup_expired_transients_callback() {

	global $wpdb;

	$now = time();

	// Clean up expired rate limit transients
	$timeout_options = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options}
			 WHERE option_name LIKE %s
			 AND option_value < %d",
			'_transient_timeout_dae_rate_limit_%',
			$now
		)
	);

	foreach ( $timeout_options as $row ) {
		$timeout_option = $row->option_name;
		$transient_option = str_replace( '_timeout_', '_', $timeout_option );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name = %s OR option_name = %s",
				$timeout_option,
				$transient_option
			)
		);
	}
}
