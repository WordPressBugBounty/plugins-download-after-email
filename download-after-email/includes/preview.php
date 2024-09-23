<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'dae_content_preview' );
function dae_content_preview() {
	
	if ( ! empty( $_GET['dae_preview'] ) && 'true' == $_GET['dae_preview'] ) {
		
		session_start();
		
		if (
			! is_user_logged_in()
			|| ! current_user_can( 'edit_posts' )
			|| empty( $_SESSION['nonce'] )
			|| empty( $_SESSION['action'] )
			|| ! wp_verify_nonce( $_SESSION['nonce'], $_SESSION['action'] )
			|| empty( $_SESSION['dae_settings'] )
			|| empty( $_SESSION['preview_css'] )
		) {
			session_unset();
			session_destroy();
			die( esc_html__( 'Click the preview button to see the latest changes...', 'download-after-email' ) );
		}
		
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<?php wp_head(); ?>
			</head>
			<body class="dae-preview-body">
				<div id="dae-preview-info" class="dashicons-before dashicons-info"><span><?php esc_html_e( 'Change the size of the window to see how it looks in smaller areas. The preview is displayed while front-end css has been loaded, such as the stylesheet of your current theme. This may affect the layout. Make sure you have selected a download file before testing the form.', 'download-after-email' ); ?></span></div>
				<?php
				if ( 'default' == $_SESSION['preview_css'] ) {
					echo dae_content_shortcode_css_return( '', $_SESSION['dae_settings'] );
				}
				echo dae_content_shortcode_return( '', $_SESSION['download_title'], $_SESSION['download_text'], $_SESSION['dae_settings'] );
				wp_footer();
				?>
			</body>
		</html>
		<?php
		
		session_unset();
		session_destroy();
		die;
		
	}
	
}