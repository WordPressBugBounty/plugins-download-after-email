<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dae_content_shortcode_css_return( $download_id, $dae_settings ) {

	if ( ! isset( $dae_settings['checkbox_link_color'] ) ) {
		$dae_settings['checkbox_link_color'] = 'unset';
		$dae_settings['checkbox_link_color_hover'] = 'unset';
	}

	if ( 'image' == $dae_settings['background_type'] ) {
		$background = 'url(' . esc_url( wp_get_attachment_url( $dae_settings['background_id'] ) ) . ')';
	} else {
		$background = esc_html( $dae_settings['background_color'] );
	}

	preg_match( '~\d+[.]*\d*([a-z]+|[%])~', $dae_settings['input_font_size'], $match_unit );
	$input_size_nr = str_replace( $match_unit[1], '', $dae_settings['input_font_size'] );
	$field_height = $input_size_nr * 3 . $match_unit[1];
	$field_height = 'calc(' . $field_height . ' + 4px)';
	$select_icon_top = 'calc(50% - ' . $input_size_nr / 2 . $match_unit[1] . ')';

	$justify_content['left'] = 'flex-start';
	$justify_content['right'] = 'flex-end';
	$justify_content['center'] = 'center';

	$margin_message['left'] = '20px auto 20px 20px';
	$margin_message['right'] = '20px 20px 20px auto';
	$margin_message['center'] = '20px auto';

	$content = '
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper {
			background: ' . $background . ' !important;
			background-attachment: ' . esc_html( $dae_settings['background_attachment'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-file-image {
			width: ' . esc_html( $dae_settings['file_image_width_small'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-download-file-image {
			width: ' . esc_html( $dae_settings['file_image_width_wide'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-title {
			font-size: ' . esc_html( $dae_settings['title_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			color: ' . esc_html( $dae_settings['title_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text {
			font-size: ' . esc_html( $dae_settings['text_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			color: ' . esc_html( $dae_settings['text_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text h1,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text h2,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text h3,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text h4,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text h5 {
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-button {
			color: ' . esc_html( $dae_settings['button_color'] ) . ' !important;
			background: ' . esc_html( $dae_settings['button_background'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['button_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			width: ' . esc_html( $dae_settings['button_width'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['button_padding'] ) . ' !important;
			border-color: ' . esc_html( $dae_settings['button_border_color'] ) . ' !important;
			border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-moz-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-webkit-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-button:hover {
			color: ' . esc_html( $dae_settings['button_color_hover'] ) . ' !important;
			background: ' . esc_html( $dae_settings['button_background_hover'] ) . ' !important;
			border-color: ' . esc_html( $dae_settings['button_border_color_hover'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['button_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			width: ' . esc_html( $dae_settings['button_width'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['button_padding'] ) . ' !important;
			border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-moz-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-webkit-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-label {
			font-size: ' . esc_html( $dae_settings['label_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			color: ' . esc_html( $dae_settings['label_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-icon {
			height: ' . esc_html( $field_height ) . ' !important;
			font-size: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			color: ' . esc_html( $dae_settings['input_icon_color'] ) . ' !important;
			background: ' . esc_html( $dae_settings['input_icon_background'] ) . ' !important;
			border-radius: ' . esc_html( $dae_settings['border_radius'] . ' 0 0 ' . $dae_settings['border_radius'] ) . ' !important;
			-moz-border-radius: ' . esc_html( $dae_settings['border_radius'] . ' 0 0 ' . $dae_settings['border_radius'] ) . ' !important;
			-webkit-border-radius: ' . esc_html( $dae_settings['border_radius'] . ' 0 0 ' . $dae_settings['border_radius'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-field {
			height: ' . esc_html( $field_height ) . ' !important;
			font-size: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			color: ' . esc_html( $dae_settings['input_color'] ) . ' !important;
			background: ' . esc_html( $dae_settings['input_background'] ) . ' !important;
			border-radius: ' . esc_html( '0 ' . $dae_settings['border_radius'] . ' ' . $dae_settings['border_radius'] . ' 0' ) . ' !important;
			-moz-border-radius: ' . esc_html( '0 ' . $dae_settings['border_radius'] . ' ' . $dae_settings['border_radius'] . ' 0' ) . ' !important;
			-webkit-border-radius: ' . esc_html( '0 ' . $dae_settings['border_radius'] . ' ' . $dae_settings['border_radius'] . ' 0' ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-select-icon {
			top: ' . esc_html( $select_icon_top ) . ' !important;
			right: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['input_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			color: ' . esc_html( $dae_settings['input_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-field::-webkit-input-placeholder,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-field::placeholder {
			color: ' . esc_html( $dae_settings['placeholder_color'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-field::-ms-input-placeholder {
			color: ' . esc_html( $dae_settings['placeholder_color'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-checkbox-text {
			color: ' . esc_html( $dae_settings['checkbox_color'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['checkbox_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-checkbox-text a {
			color: ' . esc_html( $dae_settings['checkbox_link_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-checkbox-text a:hover {
			color: ' . esc_html( $dae_settings['checkbox_link_color_hover'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-submit {
			color: ' . esc_html( $dae_settings['submit_color'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['submit_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['submit_font_size'] ) . ' !important;
			background: ' . esc_html( $dae_settings['submit_background'] ) . ' !important;
			border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-moz-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-webkit-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-submit:hover {
			color: ' . esc_html( $dae_settings['submit_color_hover'] ) . ' !important;
			background: ' . esc_html( $dae_settings['submit_background_hover'] ) . ' !important;
			font-size: ' . esc_html( $dae_settings['submit_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
			padding: ' . esc_html( $dae_settings['submit_font_size'] ) . ' !important;
			border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-moz-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
			-webkit-border-radius: ' . esc_html( $dae_settings['border_radius'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-loading {
			color: ' . esc_html( $dae_settings['submit_background'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-message {
			font-size: ' . esc_html( $dae_settings['submit_message_font_size'] ) . ' !important;
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-error {
			color: ' . esc_html( $dae_settings['submit_error_message_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-success {
			color: ' . esc_html( $dae_settings['submit_success_message_color'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-category-interests h4,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-input-wrap-interest label {
			font-family: ' . esc_html( $dae_settings['font_family'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper {
			align-items: ' . esc_html( $justify_content[ $dae_settings['alignment_small'] ] ) . ' !important;
			-webkit-align-items: ' . esc_html( $justify_content[ $dae_settings['alignment_small'] ] ) . ' !important;
			justify-content: flex-start !important;
			-webkit-justify-content: flex-start !important;
			-moz-justify-content: fle-start !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-content-wrapper,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-title,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-download-text,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-wrapper p,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-category-interests-wrap {
			text-align: ' . esc_html( $dae_settings['alignment_small'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-field-wrap {
			justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_small'] ] ) . ' !important;
			-webkit-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_small'] ] ) . ' !important;
			-moz-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_small'] ] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-label,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-message,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper .dae-shortcode-register-category-interests-wrap {
			margin: ' . esc_html( $margin_message[ $dae_settings['alignment_small'] ] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide {
			align-items: center !important;
			-webkit-align-items: center !important;
			justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
			-webkit-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
			-moz-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-download-content-wrapper,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-download-title,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-download-text,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-wrapper p,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-category-interests-wrap {
			text-align: ' . esc_html( $dae_settings['alignment_wide'] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-field-wrap {
			justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
			-webkit-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
			-moz-justify-content: ' . esc_html( $justify_content[ $dae_settings['alignment_wide'] ] ) . ' !important;
		}
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-label,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-message,
		#dae-shortcode' . esc_html( $download_id ) . '-download-wrapper.dae-shortcode-download-wrapper-wide .dae-shortcode-register-category-interests-wrap {
			margin: ' . esc_html( $margin_message[ $dae_settings['alignment_wide'] ] ) . ' !important;
		}
	';

	$content = '<style type="text/css">' . $content . '</style>';

	return $content;

}

function dae_content_shortcode_return( $download_id, $download_title, $download_text, $dae_settings, $atts = [] ) {

	$dae_messages = get_option( 'dae_messages' );
	
	$file_image_src = wp_get_attachment_image_src( $dae_settings['file_image_id'], $dae_settings['file_image_size'] );
	
	if ( $file_image_src ) {

		$alt_text = get_post_meta( $dae_settings['file_image_id'], '_wp_attachment_image_alt', true );

		if ( ! empty( $alt_text ) ) {
			$alt_attr = ' alt="' . esc_attr( $alt_text ) . '"';
		} else {
			$alt_attr = '';
		}
		
		$file_image_html = '<img class="dae-shortcode-download-file-image" src="' . esc_url( $file_image_src[0] ) . '" width="' . esc_attr( $file_image_src[1] ) . '" height="' . esc_attr( $file_image_src[2] ) . '"' . $alt_attr . ' />';

	} else {

		$file_image_html = '';
		
	}
	
	if ( empty( $download_title ) ) {
		$download_title_html = '';
	} else {
		$download_title_html = '<h2 class="dae-shortcode-download-title">' . esc_html( $download_title ) . '</h2>';
	}
	
	if ( empty( $download_text ) ) {
		$download_text_html = '';
	} else {
		$download_text_html = '<div class="dae-shortcode-download-text">' . wp_kses_post( nl2br( $download_text ) ) . '</div>';
	}

	if ( ! empty( $dae_messages['required_checkbox'] ) ) {

		if ( empty( $dae_messages['required_checkbox_text'] ) ) {
			$required_checkbox_text = __( 'I confirm that I have read and agree to the <a href="#" target="_blank">Privacy Policy</a>.', 'download-after-email' );
		} else {
			$required_checkbox_text = $dae_messages['required_checkbox_text'];
		}

		$required_checkbox_html = '
			<p>
				<input class="dae-shortcode-register-checkbox" type="checkbox" name="required_checkbox" value="' . esc_attr( $required_checkbox_text ) . '" />
				<span class="dae-shortcode-register-checkbox-text">' . wp_kses_post( $required_checkbox_text ) . '</span>
			</p>
		';

	} else {

		$required_checkbox_html = '';

	}

	if ( ! empty( $dae_messages['optional_checkbox'] ) ) {

		if ( empty( $dae_messages['optional_checkbox_text'] ) ) {
			$optional_checkbox_text = __( 'Subscribe to get exclusive content and recommendations every month. You can unsubscribe anytime.', 'download-after-email' );
		} else {
			$optional_checkbox_text = $dae_messages['optional_checkbox_text'];
		}

		$optional_checkbox_html = '
			<p>
				<input class="dae-shortcode-register-checkbox" type="checkbox" name="optional_checkbox" value="' . esc_attr( $optional_checkbox_text ) . '" />
				<span class="dae-shortcode-register-checkbox-text">' . wp_kses_post( $optional_checkbox_text ) . '</span>
			</p>
		';

	} else {

		$optional_checkbox_html = '';

	}

	$file_name = dae_get_download_file_name( $dae_settings['file_id'] );
	
	$html_icon = '<div class="dae-shortcode-register-icon"><i class="fas fa-envelope"></i></div>';
	$html_field = '<div class="dae-shortcode-register-input-wrap"><input class="dae-shortcode-register-field" type="email" name="email" placeholder="' . esc_attr( $dae_settings['placeholder_text'] ) . '" autocomplete="off" /></div>';
	$html_fields = apply_filters( 'dae_shortcode_html_fields', '<div class="dae-shortcode-register-field-wrap">' . $html_icon . $html_field . '</div>', $file_name, $atts );
	
	$content = '
		<div id="dae-shortcode' . esc_attr( $download_id ) . '-download-wrapper" class="dae-shortcode-download-wrapper">
			' . $file_image_html . '
			<div class="dae-shortcode-download-content-wrapper">
				' . $download_title_html . '
				' . $download_text_html . '
				<div class="dae-shortcode-download-button">
					<span class="dae-shortcode-download-button-icon"><i class="fas fa-download"></i></span>
					<span class="dae-shortcode-download-button-text">' . esc_html( $dae_settings['button_text'] ) . '</span>
				</div>
				<div class="dae-shortcode-register-wrapper">
					<p class="dae-shortcode-register-label">' . esc_html( $dae_settings['label'] ) . '</p>
					<form class="dae-shortcode-register-form" method="post" novalidate="novalidate">
						<input type="hidden" name="file" value="' . esc_attr( $file_name ) . '" />
						' . $html_fields . '
						' . $required_checkbox_html . '
						' . $optional_checkbox_html . '
						<p>
							<input class="dae-shortcode-register-submit" type="submit" value="' . esc_attr( $dae_settings['submit_text'] ) . '" />
						</p>
						<p class="dae-shortcode-register-loading">
							<i class="fas fa-spinner fa-spin"></i>
						</p>
					</form>
					<p class="dae-shortcode-register-message"></p>
				</div>
			</div>
		</div>
	';
	
	return $content;
	
}

add_action( 'init', 'dae_shortcodes_init' );
function dae_shortcodes_init() {
	
	add_shortcode( 'download_after_email', 'dae_content_shortcode' );
	function dae_content_shortcode( $atts = [], $content = null, $tag = '' ) {
		
		if ( ! is_admin() && ! empty( $atts['id'] ) ) {
			
			$download_id = (int) $atts['id'];
			$css = empty( $atts['css'] ) ? 'on' : sanitize_text_field( $atts['css'] );

			if ( empty( $download_id ) ) {
				return '';
			}
			
			$download = get_posts( array( 'p' => $download_id, 'post_type' => 'dae_download' ) );
			
			if ( ! empty( $download ) ) {
				
				$download_title = $download[0]->post_title;
				$download_text = $download[0]->post_content;
				$dae_settings = get_post_meta( $download_id, 'dae_settings', true );

				if ( 'off' != $css ) {
					$content = dae_content_shortcode_css_return( $download_id, $dae_settings );
				} else {
					$content = '';
				}
				
				$content .= dae_content_shortcode_return( $download_id, $download_title, $download_text, $dae_settings, $atts );
				
			} else {
				
				$content = '';
				
			}
			
			return $content;
			
		}
		
	}
	
}

add_action( 'wp_ajax_dae_send_downloadlink', 'dae_send_downloadlink' );
add_action( 'wp_ajax_nopriv_dae_send_downloadlink', 'dae_send_downloadlink' );

function dae_send_downloadlink() {
	
	$_POST = stripslashes_deep( $_POST );
	
	$messages = get_option( 'dae_messages' );
	$field_labels = get_option( 'dae_field_labels' );
	$fields = get_option( 'dae_fields' );
	$options = get_option( 'dae_options' );

	$file = basename( sanitize_text_field( $_POST['file'] ) );
	$form_content = mckp_sanitize_form_content( $_POST['form_content'] );

	if ( empty( $messages['required_checkbox'] ) ) {
		$required_checkbox = 'Checkbox disabled.';
	} else {
		$required_checkbox = isset( $_POST['required_checkbox'] ) ? sanitize_text_field( $_POST['required_checkbox'] ) : '';
	}

	if ( empty( $messages['optional_checkbox'] ) ) {
		$optional_checkbox = false;
	} else {
		$optional_checkbox = isset( $_POST['optional_checkbox'] ) ? sanitize_text_field( $_POST['optional_checkbox'] ) : '';
	}
	
	$empty_values = false;
	
	foreach ( $field_labels as $field_label ) {
		
		$field_label_name = str_replace( ' ', '_', strtolower( $field_label ) );
		
		if ( ! empty( $fields[ $field_label_name . '_visible' ] ) ) {
			
			if ( 'email' == $fields[ $field_label_name . '_type' ] ) {
				$values[ $field_label_name ] = sanitize_email( $_POST[ $field_label_name ] );
			} elseif ( 'url' == $fields[ $field_label_name . '_type' ] ) {
				$values[ $field_label_name ] = esc_url_raw( $_POST[ $field_label_name ] );
			} else {
				$values[ $field_label_name ] = sanitize_text_field( $_POST[ $field_label_name ] );
			}
			
			if ( empty( $values[ $field_label_name ] ) && ! in_array( $field_label_name, apply_filters( 'dae_optional_fields', array() ) ) ) {
				$empty_values = true;
			} elseif ( 'date' == $fields[ $field_label_name . '_type' ] ) {
				$values[ $field_label_name ] = ! empty( $values[ $field_label_name ] ) ? date( $fields[ $field_label_name . '_date_format' ], strtotime( $values[ $field_label_name ] ) ) : '';
			}
			
		}
		
	}

	if ( empty( $file ) ) {

		$form_message = apply_filters( 'dae_form_missing_file_message', __( 'There is currently no download file available.', 'download-after-email' ) );

		echo json_encode( array(
			'type'		=> 'missing',
			'message'	=> '<span class="dae-shortcode-register-error">' . $form_message . '</span>'
		) );

	} elseif ( empty( $required_checkbox ) || $empty_values || ! dae_check_ecnon() ) {

		$form_message = ! empty( $messages['unvalid_input'] ) ? $messages['unvalid_input'] : __( 'Please make sure all fields are filled in correctly.', 'download-after-email' );

		echo json_encode( array(
			'type'		=> 'empty',
			'message'	=> '<span class="dae-shortcode-register-error">' . $form_message . '</span>'
		) );

	} elseif ( apply_filters( 'dae_form_validation', false, $values ) ) {
		
		$form_message = apply_filters( 'dae_form_validation_message', __( 'Please make sure all fields are filled in correctly.', 'download-after-email' ) );

		echo json_encode( array(
			'type'		=> 'validation',
			'message'	=> '<span class="dae-shortcode-register-error">' . $form_message . '</span>'
		) );

	} else {
		
		if ( $subscriber = DAE_Subscriber::get_instance( $values['email'] ) ) {

			$subscriber_id = $subscriber->id;
			
			if ( false !== $optional_checkbox ) {

				$meta_values['optional_checkbox'] = $optional_checkbox;
				$values['optional_checkbox'] = $optional_checkbox;

				if ( ! isset( $subscriber->meta['optin_time'] ) && ! empty( $optional_checkbox ) ) {
					$meta_values['optin_time'] = current_time( 'Y-m-d H:i:s' );
					$values['optin_time'] = $meta_values['optin_time'];
				} elseif ( isset( $subscriber->meta['optin_time'] ) ) {
					$values['optin_time'] = $subscriber->meta['optin_time'];
				}

				DAE_Subscriber::update_subscriber_meta( $subscriber_id, $meta_values );

			}
			
		} else {
			
			if ( false !== $optional_checkbox ) {

				$values['optional_checkbox'] = $optional_checkbox;

				if ( ! empty( $optional_checkbox ) ) {
					$values['optin_time'] = current_time( 'Y-m-d H:i:s' );
				}

			}

			$subscriber_id = DAE_Subscriber::insert( $values );

		}

		if ( ! empty( $subscriber->links[ $file ] ) && ! empty( $options['unlimited_emails'] ) && strtotime( current_time( 'Y-m-d H:i:s' ) ) > ( strtotime( $subscriber->links[ $file ]['time'] ) + 60 ) ) {

			DAE_Subscriber::delete_link( $subscriber->links[ $file ]['id'] );
			unset( $subscriber->links[ $file ] );

		}

		if ( ! empty( $subscriber->links[ $file ] ) ) {

			$form_message = ! empty( $messages['email_exists'] ) ? $messages['email_exists'] : __( 'An email with the download link has already been sent to this email address.', 'download-after-email' );
			
			echo json_encode( array(
				'type'		=> 'exists',
				'message'	=> '<span class="dae-shortcode-register-error">' . $form_message . '</span>'
			) );
			
		} elseif ( $subscriber_id ) {
			
			session_start();
			
			$link_id = DAE_Subscriber::insert_link( $subscriber_id, $form_content, $file );
			
			$nonce = mckp_create_nonce( $file, $values['email'], $link_id );

			$download_url = esc_url( add_query_arg(
				array(
					'file'	=> rawurlencode( $file ),
					'nonce'	=> rawurlencode( $nonce ),
					'email'	=> rawurlencode( $values['email'] )
				),
				home_url( '/' )
			) );
			
			$download_link = '<a href="' . $download_url . '">' . $file . '</a>';

			$messages['email_content_type'] = empty( $messages['email_content_type'] ) ? 'html' : $messages['email_content_type'];

			if ( 'multipart' == $messages['email_content_type'] ) {

				add_action( 'phpmailer_init', 'dae_mail_alt_body' );
				function dae_mail_alt_body( $phpmailer ) {
					$phpmailer->AltBody = strip_tags( $phpmailer->Body );
				}

			} else {

				$_SESSION['email_content_type'] = $messages['email_content_type'];

				add_filter( 'wp_mail_content_type', 'dae_filter_email_content_type' );
				function dae_filter_email_content_type() {
					return 'text/' . $_SESSION['email_content_type'];
				}

			}
			
			if ( ! empty( $messages['email_from_email'] ) ) {
				
				$_SESSION['email_from_email'] = $messages['email_from_email'];
				
				add_filter( 'wp_mail_from', 'dae_filter_from_email', 999 );
				function dae_filter_from_email( $from_email ) {
					return $_SESSION['email_from_email'];
				}
				
			}
			
			if ( ! empty( $messages['email_from_name'] ) ) {
				
				$_SESSION['email_from_name'] = $messages['email_from_name'];
				
				add_filter( 'wp_mail_from_name', 'dae_filter_from_name', 999 );
				function dae_filter_from_name( $from_name ) {
					return $_SESSION['email_from_name'];
				}
				
			}
			
			$to = $values['email'];
			$subject = ! empty( $messages['email_subject'] ) ? $messages['email_subject'] : __( 'Your Free Download', 'download-after-email' );
			
			if ( empty( $messages['email_content'] ) ) {
				$message = __( '<p>Hi,</p><p>Thank you for subscribing.</p><p>You can find your Free Download here: {download_link}</p>', 'download-after-email' );
				$messages['email_content'] = '';
			} else {
				$message = wp_kses_post( wpautop( $messages['email_content'] ) );
			}

			if ( 'multipart' == $messages['email_content_type'] || 'html' == $messages['email_content_type'] ) {
				$message = '<html>
					<head>
						<meta http-equiv="content-type" content="text/html; charset=utf-8" />
					</head>
					<body>' . $message . '</body>
				</html>';
			}

			$subject = apply_filters( 'dae_email_subject', $subject, $values, $file );
			$message = apply_filters( 'dae_email_message', $message, $values, $messages['email_content'], $file, $download_url );
			
			$headers = apply_filters( 'dae_email_headers', '' );
			$attachments = apply_filters( 'dae_email_attachments', array() );
			
			$message = str_replace( '{download_link}', $download_link, $message );
			$message = str_replace( '{download_url}', $download_url, $message );
			$message = str_replace( '{download_file}', $file, $message );
			$subject = str_replace( '{download_file}', $file, $subject );
			
			foreach ( $values as $label => $value ) {
				$message = str_replace( '{' . $label . '}', $value, $message );
				$subject = str_replace( '{' . $label . '}', $value, $subject );
			}
			
			if ( preg_match_all( '~src="([^"]+)~', $message, $src_matches ) ) {
				
				$upload_dir = wp_upload_dir();
				
				foreach ( $src_matches[1] as $src_match ) {
					
					$filename = basename( $src_match );
					$filepath = strchr( $upload_dir['basedir'], 'wp-content', true ) . strchr( $src_match, 'wp-content', false );

					if ( file_exists( $filepath ) ) {
						$filetype = strtolower( pathinfo( $filepath, PATHINFO_EXTENSION ) );
						$filename_without_ext = strtolower( basename( $filename, '.' . $filetype ) );
						$message = str_replace( $src_match, 'cid:' . $filename_without_ext, $message );
						$_SESSION['src_matches'][] = array( $src_match, $filepath, $filename_without_ext );
					}

				}
				
				if ( ! empty( $_SESSION['src_matches'] ) ) {

					add_action( 'phpmailer_init', 'dae_add_embedded_images' );
					function dae_add_embedded_images( $phpmailer ) {
						
						foreach ( $_SESSION['src_matches'] as $src_match_arr ) {
							$phpmailer->AddEmbeddedImage( $src_match_arr[1], $src_match_arr[2] );
						}
						
					}

				}

			}
			
			wp_mail( $to, $subject, $message, $headers, $attachments );

			if ( 'plain' == $messages['email_content_type'] ) {
				$_SESSION['email_content_type'] = 'html';
			}
			
			if ( ! empty( $messages['email_notification'] ) ) {
				
				$notification_table_rows = '
					<tr>
						<th style="text-align: left; padding: 0.5em 1em; color: #0073aa;">' . esc_html__( 'Download File', 'download-after-email' ) . '</th>
						<td>' . esc_html( $file ) . '</td>
					</tr>
				';
				
				foreach( $values as $label => $value ) {
					$notification_table_rows .= '
						<tr>
							<th style="text-align: left; padding: 0.5em 1em; color: #0073aa;">' . esc_html( ucwords( str_replace( '_', ' ', $label ) ) ) . '</th>
							<td>' . esc_html( $value ) . '</td>
						</tr>
					';
				}
				
				$notification_message = '
					<p>' . esc_html__( 'A download form has been submitted successfully with the following values:', 'download-after-email' ) . '</p>
					<table style="margin: 1em; font-size: 0.9em;">
						<tbody>
							' . $notification_table_rows . '
						</tbody>
					</table>
				';

				$notification_message = '<html>
					<head>
						<meta http-equiv="content-type" content="text/html; charset=utf-8" />
					</head>
					<body>' . $notification_message . '</body>
				</html>';
				
				$notification_message = apply_filters( 'dae_email_notification', $notification_message, $values, $file );
				
				$arr_email_notification = explode( ';', $messages['email_notification'] );

				foreach ( $arr_email_notification as $email_notification ) {
					wp_mail( $email_notification, __( 'New Download Form Submission', 'download-after-email' ), $notification_message );
				}
				
			}

			if ( 'multipart' == $messages['email_content_type'] ) {
				remove_action( 'phpmailer_init', 'dae_mail_alt_body' );
			} else {
				remove_filter( 'wp_mail_content_type', 'dae_filter_email_content_type' );
			}
			
			remove_filter( 'wp_mail_from', 'dae_filter_from_email' );
			remove_filter( 'wp_mail_from_name', 'dae_filter_from_name' );
			
			session_unset();
			session_destroy();

			$form_message = ! empty( $messages['email_success'] ) ? $messages['email_success'] : __( 'An email has been sent with the download link.', 'download-after-email' );
			
			echo json_encode( array(
				'type'		=> 'success',
				'message'	=> '<span class="dae-shortcode-register-success">' . $form_message . '</span>'
			) );
			
			do_action( 'dae_after_send_downloadlink', $subscriber_id );
			
		}
		
	}
	
	wp_die();
	
}

?>