<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'dae_post_types_init' );
function dae_post_types_init() {
	
	$labels = array(
		'name'					=> _x( 'Downloads', 'post type general name', 'download-after-email' ),
		'singular_name'			=> _x( 'Download', 'post type singular name', 'download-after-email' ),
		'add_new'				=> _x( 'Add New', 'download', 'download-after-email' ),
		'add_new_item'			=> __( 'Add New Download', 'download-after-email' ),
		'edit_item'				=> __( 'Edit Download', 'download-after-email' ),
		'new_item'				=> __( 'New Download', 'download-after-email' ),
		'view_item'				=> __( 'View Download', 'download-after-email' ),
		'view_items'			=> __( 'View Downloads', 'download-after-email' ),
		'search_items'			=> __( 'Search Downloads', 'download-after-email' ),
		'not_found'				=> __( 'No downloads found.', 'download-after-email' ),
		'not_found_in_trash'	=> __( 'No downloads found in Trash.', 'download-after-email' ),
		'parent_item_colon'		=> null,
		'all_items'				=> __( 'All Downloads', 'download-after-email' ),
		'archives'				=> __( 'Download Archives', 'download-after-email' ),
		'attributes'			=> __( 'Download Attributes', 'download-after-email' ),
		'insert_into_item'		=> __( 'Insert into download', 'download-after-email' ),
		'uploaded_to_this_item'	=> __( 'Uploaded to this download', 'download-after-email' ),
		'featured_image'		=> _x( 'Featured Image', 'download', 'download-after-email' ),
		'set_featured_image'	=> _x( 'Set featured image', 'download', 'download-after-email' ),
		'remove_featured_image'	=> _x( 'Remove featured image', 'download', 'download-after-email' ),
		'use_featured_image'	=> _x( 'Use as featured image', 'download', 'download-after-email' ),
		'filter_items_list'		=> __( 'Filter downloads list', 'download-after-email' ),
		'items_list_navigation'	=> __( 'Downloads list navigation', 'download-after-email' ),
		'items_list'			=> __( 'Downloads list', 'download-after-email' )
	);
	
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'exclude_from_search'	=> true,
		'publicly_queryable'	=> false,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus'		=> false,
		'show_in_admin_bar'		=> false,
		'show_in_rest'			=> false,
		'menu_icon'				=> 'dashicons-download',
		'supports'				=> array( 'title', 'editor' ),
		'register_meta_box_cb'	=> 'dae_add_meta_boxes_download'
	);
	
	register_post_type( 'dae_download', $args );
	
}

add_filter( 'post_updated_messages', 'dae_download_updated_messages' );
function dae_download_updated_messages( $messages ) {
	
	$post				= get_post();
	$post_type			= get_post_type( $post );
	$post_type_object	= get_post_type_object( $post_type );
	
	$scheduled_date = date_i18n( __( 'M j, Y @ H:i', 'download-after-email' ), strtotime( $post->post_date ) );
 
	$messages['dae_download'] = array(
		0	=> '', // Unused. Messages start at index 1.
		1	=> __( 'Download updated.', 'download-after-email' ),
		2	=> __( 'Custom field updated.', 'download-after-email' ),
		3	=> __( 'Custom field deleted.', 'download-after-email' ),
		4	=> __( 'Download updated.', 'download-after-email' ),
		5	=> isset( $_GET['revision'] ) ? sprintf( __( 'Download restored to revision from %s', 'download-after-email' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6	=> __( 'Download published.', 'download-after-email' ),
		7	=> __( 'Download saved.', 'download-after-email' ),
		8	=> __( 'Download submitted.', 'download-after-email' ),
		9	=> sprintf( __( 'Page scheduled for: %s.', 'download-after-email' ), '<strong>' . $scheduled_date . '</strong>' ),
		10	=> __( 'Download draft updated.', 'download-after-email' ),
	);
	
	return $messages;
	
}

function dae_add_meta_boxes_download() {
	
	add_meta_box( 'dae_meta_box_settings', __( 'Settings', 'download-after-email' ), 'dae_content_meta_box_settings', 'dae_download', 'normal' );
	add_meta_box( 'dae_meta_box_shortcode', __( 'Shortcode', 'download-after-email' ), 'dae_content_meta_box_shortcode', 'dae_download', 'side' );
	add_meta_box( 'dae_meta_box_preview', __( 'Preview', 'download-after-email' ), 'dae_content_meta_box_preview', 'dae_download', 'side' );
	add_meta_box( 'dae_meta_box_duplicate', __( 'Duplicate', 'download-after-email' ), 'dae_content_meta_box_duplicate', 'dae_download', 'side' );
	
}

function dae_content_meta_box_settings_background( $dae_settings ) {
	
	if ( empty( $dae_settings['background_type'] ) ) {
		$dae_settings['background_type'] = 'image';
	}
	
	?>
	<?php if ( 'image' == $dae_settings['background_type'] ) : ?>
		<td>
			<?php mckp_content_media( ! empty( $dae_settings['background_id'] ) ? $dae_settings['background_id'] : '', 'background_id', true ); ?>
		</td>
	<?php else : ?>
		<td>
			<input class="dae-colorpicker" type="text" name="background_color" data-default-color="#f1f1f1" value="<?php echo ! empty( $dae_settings['background_color'] ) ? esc_attr( $dae_settings['background_color'] ) : esc_attr( '#f1f1f1' ); ?>" />
		</td>
	<?php endif; ?>
	<?php
	
}

function dae_content_meta_box_settings( $post ) {
	
	$dae_settings = get_post_meta( $post->ID, 'dae_settings', true );
	
	if ( empty( $dae_settings['file_image_size'] ) ) {
		unset( $dae_settings );
		$dae_settings['file_image_size'] = '';
		$dae_settings['background_type'] = '';
		$dae_settings['background_attachment'] = '';
		$dae_settings['alignment_wide'] = '';
		$dae_settings['alignment_small'] = '';
	}
	
	?>
	<div id="dae-download-tables">
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Download file', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Download file *', 'download-after-email' ); ?></th>
					<td>
						<?php mckp_content_media( ! empty( $dae_settings['file_id'] ) ? $dae_settings['file_id'] : '', 'file_id', false ); ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'File image', 'download-after-email' ); ?></th>
					<td>
						<?php mckp_content_media( ! empty( $dae_settings['file_image_id'] ) ? $dae_settings['file_image_id'] : '', 'file_image_id', true ); ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'File image size', 'download-after-email' ); ?></th>
					<td>
						<select name="file_image_size">
							<option value="full" <?php selected( 'full', $dae_settings['file_image_size'] ); ?>><?php esc_html_e( 'Full', 'download-after-email' ); ?></option>
							<option value="large" <?php selected( 'large', $dae_settings['file_image_size'] ); ?>><?php esc_html_e( 'Large', 'download-after-email' ); ?></option>
							<option value="medium_large" <?php selected( 'medium_large', $dae_settings['file_image_size'] ); ?>><?php esc_html_e( 'Medium large', 'download-after-email' ); ?></option>
							<option value="medium" <?php selected( 'medium', $dae_settings['file_image_size'] ); ?>><?php esc_html_e( 'Medium', 'download-after-email' ); ?></option>
							<option value="thumbnail" <?php selected( 'thumbnail', $dae_settings['file_image_size'] ); ?>><?php esc_html_e( 'Thumbnail', 'download-after-email' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'File image width (wide area)', 'download-after-email' ); ?></th>
					<td><input type="text" name="file_image_width_wide" value="<?php echo ! empty( $dae_settings['file_image_width_wide'] ) ? esc_attr( $dae_settings['file_image_width_wide'] ) : esc_attr( '40%' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'File image width (small area)', 'download-after-email' ); ?></th>
					<td><input type="text" name="file_image_width_small" value="<?php echo ! empty( $dae_settings['file_image_width_small'] ) ? esc_attr( $dae_settings['file_image_width_small'] ) : esc_attr( '80%' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Background', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Background', 'download-after-email' ); ?></th>
					<td>
						<select id="dae-download-background-type" name="background_type">
							<option value="image" <?php selected( 'image', $dae_settings['background_type'] ); ?>><?php esc_html_e( 'Image', 'download-after-email' ); ?></option>
							<option value="color" <?php selected( 'color', $dae_settings['background_type'] ); ?>><?php esc_html_e( 'Color', 'download-after-email' ); ?></option>
						</select>
					</td>
					<?php dae_content_meta_box_settings_background( $dae_settings ); ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Background-attachment', 'download-after-email' ); ?></th>
					<td>
					<select name="background_attachment">
						<option value="scroll" <?php selected( 'scroll', $dae_settings['background_attachment'] ); ?>><?php esc_html_e( 'Scroll', 'download-after-email' ); ?></option>
						<option value="fixed" <?php selected( 'fixed', $dae_settings['background_attachment'] ); ?>><?php esc_html_e( 'Fixed', 'download-after-email' ); ?></option>
					</select>
					</td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Title', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Title font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="title_font_size" value="<?php echo ! empty( $dae_settings['title_font_size'] ) ? esc_attr( $dae_settings['title_font_size'] ) : esc_attr( '40px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Title color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="title_color" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['title_color'] ) ? esc_attr( $dae_settings['title_color'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Content', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Text font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="text_font_size" value="<?php echo ! empty( $dae_settings['text_font_size'] ) ? esc_attr( $dae_settings['text_font_size'] ) : esc_attr( '16px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Text color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="text_color" data-default-color="#444444" value="<?php echo ! empty( $dae_settings['text_color'] ) ? esc_attr( $dae_settings['text_color'] ) : esc_attr( '#444444' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Button', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Button text', 'download-after-email' ); ?></th>
					<td><input type="text" name="button_text" value="<?php echo ! empty( $dae_settings['button_text'] ) ? esc_attr( $dae_settings['button_text'] ) : esc_attr( 'FREE DOWNLOAD' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_color" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['button_color'] ) ? esc_attr( $dae_settings['button_color'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button color hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_color_hover" data-default-color="#ffffff" value="<?php echo ! empty( $dae_settings['button_color_hover'] ) ? esc_attr( $dae_settings['button_color_hover'] ) : esc_attr( '#ffffff' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button background', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_background" data-default-color="none" value="<?php echo ! empty( $dae_settings['button_background'] ) ? esc_attr( $dae_settings['button_background'] ) : esc_attr( 'none' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button background hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_background_hover" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['button_background_hover'] ) ? esc_attr( $dae_settings['button_background_hover'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button border-color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_border_color" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['button_border_color'] ) ? esc_attr( $dae_settings['button_border_color'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button border-color hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="button_border_color_hover" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['button_border_color_hover'] ) ? esc_attr( $dae_settings['button_border_color_hover'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="button_font_size" value="<?php echo ! empty( $dae_settings['button_font_size'] ) ? esc_attr( $dae_settings['button_font_size'] ) : esc_attr( '25px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button width', 'download-after-email' ); ?></th>
					<td><input type="text" name="button_width" value="<?php echo ! empty( $dae_settings['button_width'] ) ? esc_attr( $dae_settings['button_width'] ) : esc_attr( 'auto' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button padding', 'download-after-email' ); ?></th>
					<td><input type="text" name="button_padding" value="<?php echo ! empty( $dae_settings['button_padding'] ) ? esc_attr( $dae_settings['button_padding'] ) : esc_attr( '20px 8px' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Label', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Label', 'download-after-email' ); ?></th>
					<td><input type="text" name="label" value="<?php echo ! empty( $dae_settings['label'] ) ? esc_attr( $dae_settings['label'] ) : esc_attr( 'Send download link to:' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Label font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="label_font_size" value="<?php echo ! empty( $dae_settings['label_font_size'] ) ? esc_attr( $dae_settings['label_font_size'] ) : esc_attr( '18px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Label color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="label_color" data-default-color="#444444" value="<?php echo ! empty( $dae_settings['label_color'] ) ? esc_attr( $dae_settings['label_color'] ) : esc_attr( '#444444' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Input fields', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Input font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="input_font_size" value="<?php echo ! empty( $dae_settings['input_font_size'] ) ? esc_attr( $dae_settings['input_font_size'] ) : esc_attr( '15px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Input color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="input_color" data-default-color="#444444" value="<?php echo ! empty( $dae_settings['input_color'] ) ? esc_attr( $dae_settings['input_color'] ) : esc_attr( '#444444' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Input background', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="input_background" data-default-color="#f9f9f9" value="<?php echo ! empty( $dae_settings['input_background'] ) ? esc_attr( $dae_settings['input_background'] ) : esc_attr( '#f9f9f9' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Input icon color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="input_icon_color" data-default-color="#ffffff" value="<?php echo ! empty( $dae_settings['input_icon_color'] ) ? esc_attr( $dae_settings['input_icon_color'] ) : esc_attr( '#ffffff' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Input icon background', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="input_icon_background" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['input_icon_background'] ) ? esc_attr( $dae_settings['input_icon_background'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Placeholders', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<?php if ( ! is_plugin_active( 'dae-plus/dae-plus.php' ) ) : ?>
					<tr>
						<th><?php esc_html_e( 'Placeholder text', 'download-after-email' ); ?></th>
						<td><input type="text" name="placeholder_text" value="<?php echo ! empty( $dae_settings['placeholder_text'] ) ? esc_attr( $dae_settings['placeholder_text'] ) : esc_attr( 'Email' ); ?>" /></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><?php esc_html_e( 'Placeholder color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="placeholder_color" data-default-color="#888888" value="<?php echo ! empty( $dae_settings['placeholder_color'] ) ? esc_attr( $dae_settings['placeholder_color'] ) : esc_attr( '#888888' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Checkboxes', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Checkbox settings', 'download-after-email' ); ?></th>
					<td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=dae_download&page=dae-messages' ) ); ?>" target="_blank"><?php esc_html_e( 'Click here to configure your checkboxes.', 'download-after-email' ); ?></a></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Checkbox text font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="checkbox_font_size" value="<?php echo ! empty( $dae_settings['checkbox_font_size'] ) ? esc_attr( $dae_settings['checkbox_font_size'] ) : esc_attr( '12px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Checkbox text color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="checkbox_color" data-default-color="#444444" value="<?php echo ! empty( $dae_settings['checkbox_color'] ) ? esc_attr( $dae_settings['checkbox_color'] ) : esc_attr( '#444444' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Checkbox link color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="checkbox_link_color" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['checkbox_link_color'] ) ? esc_attr( $dae_settings['checkbox_link_color'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Checkbox link color hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="checkbox_link_color_hover" data-default-color="#0081c1" value="<?php echo ! empty( $dae_settings['checkbox_link_color_hover'] ) ? esc_attr( $dae_settings['checkbox_link_color_hover'] ) : esc_attr( '#0081c1' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Submit button', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Submit button text', 'download-after-email' ); ?></th>
					<td><input type="text" name="submit_text" value="<?php echo ! empty( $dae_settings['submit_text'] ) ? esc_attr( $dae_settings['submit_text'] ) : esc_attr( 'Send link' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit button color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_color" data-default-color="#ffffff" value="<?php echo ! empty( $dae_settings['submit_color'] ) ? esc_attr( $dae_settings['submit_color'] ) : esc_attr( '#ffffff' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit button color hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_color_hover" data-default-color="#ffffff" value="<?php echo ! empty( $dae_settings['submit_color_hover'] ) ? esc_attr( $dae_settings['submit_color_hover'] ) : esc_attr( '#ffffff' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit button background', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_background" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['submit_background'] ) ? esc_attr( $dae_settings['submit_background'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit button background hover', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_background_hover" data-default-color="#0081c1" value="<?php echo ! empty( $dae_settings['submit_background_hover'] ) ? esc_attr( $dae_settings['submit_background_hover'] ) : esc_attr( '#0081c1' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit button font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="submit_font_size" value="<?php echo ! empty( $dae_settings['submit_font_size'] ) ? esc_attr( $dae_settings['submit_font_size'] ) : esc_attr( '18px' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Submit message', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Submit message font-size', 'download-after-email' ); ?></th>
					<td><input type="text" name="submit_message_font_size" value="<?php echo ! empty( $dae_settings['submit_message_font_size'] ) ? esc_attr( $dae_settings['submit_message_font_size'] ) : esc_attr( '16px' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit success message color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_success_message_color" data-default-color="#0073aa" value="<?php echo ! empty( $dae_settings['submit_success_message_color'] ) ? esc_attr( $dae_settings['submit_success_message_color'] ) : esc_attr( '#0073aa' ); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Submit error message color', 'download-after-email' ); ?></th>
					<td><input class="dae-colorpicker" type="text" name="submit_error_message_color" data-default-color="#dd1111" value="<?php echo ! empty( $dae_settings['submit_error_message_color'] ) ? esc_attr( $dae_settings['submit_error_message_color'] ) : esc_attr( '#dd1111' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Borders', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Border-radius', 'download-after-email' ); ?></th>
					<td><input type="text" name="border_radius" value="<?php echo ! empty( $dae_settings['border_radius'] ) ? esc_attr( $dae_settings['border_radius'] ) : esc_attr( '10px' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Font', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Font-family', 'download-after-email' ); ?></th>
					<td><input type="text" name="font_family" value="<?php echo ! empty( $dae_settings['font_family'] ) ? esc_attr( $dae_settings['font_family'] ) : esc_attr( 'Arial, Helvetica, sans-serif' ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<h3 class="dae-download-table-title"><?php esc_html_e( 'Alignment', 'download-after-email' ); ?></h3>
		<table class="oc-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Alignment wide area', 'download-after-email' ); ?></th>
					<td>
						<select name="alignment_wide">
							<option value="center" <?php selected( 'center', $dae_settings['alignment_wide'] ); ?>><?php esc_html_e( 'Align center', 'download-after-email' ); ?></option>
							<option value="left" <?php selected( 'left', $dae_settings['alignment_wide'] ); ?>><?php esc_html_e( 'Align left', 'download-after-email' ); ?></option>
							<option value="right" <?php selected( 'right', $dae_settings['alignment_wide'] ); ?>><?php esc_html_e( 'Align right', 'download-after-email' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Alignment small area', 'download-after-email' ); ?></th>
					<td>
						<select name="alignment_small">
							<option value="center" <?php selected( 'center', $dae_settings['alignment_small'] ); ?>><?php esc_html_e( 'Align center', 'download-after-email' ); ?></option>
							<option value="left" <?php selected( 'left', $dae_settings['alignment_small'] ); ?>><?php esc_html_e( 'Align left', 'download-after-email' ); ?></option>
							<option value="right" <?php selected( 'right', $dae_settings['alignment_small'] ); ?>><?php esc_html_e( 'Align right', 'download-after-email' ); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	
}

add_action( 'wp_ajax_dae_change_background_type', 'dae_change_background_type' );
function dae_change_background_type() {
	
	check_ajax_referer( 'dae_admin' );
	
	$dae_settings['background_type'] = sanitize_text_field( $_POST['background_type'] );
	
	if ( current_user_can( 'edit_posts' ) && ! empty( $dae_settings['background_type'] ) ) {
		
		dae_content_meta_box_settings_background( $dae_settings );
		
	}
	
	wp_die();
	
}

function dae_content_meta_box_shortcode( $post ) {
	
	$shortcode = get_post_meta( $post->ID, 'dae_shortcode', true );
	
	?>
	<h4><?php esc_html_e( 'Copy and paste this shortcode into your post or page', 'download-after-email' ); ?></h4>
	<p id="dae-download-shortcode">
		<?php if ( ! empty( $shortcode ) ) : ?>
			<span id="dae-download-shortcode-copy"><?php echo esc_html( $shortcode ); ?></span>
		<?php else : ?>
			<span class="dashicons dashicons-flag"></span><span><?php esc_html_e( 'Shortcode is available after saving.', 'download-after-email' ); ?></span>
		<?php endif; ?>
	</p>
	<p class="dae-message-info"><?php esc_html_e( 'Set "css" attribute to "off" to display without CSS styling options. For example: [download_after_email id="123" css="off"]', 'download-after-email' ); ?></p>
	<?php
	
}

function dae_content_meta_box_preview() {
	
	?>
	<p>
		<select id="dae-download-preview-css">
			<option value="default"><?php esc_html_e( 'Default', 'download-after-email' ); ?></option>
			<option value="no-css"><?php esc_html_e( 'No CSS styling options', 'download-after-email' ); ?></option>
		</select>
		<button id="dae-download-preview-button" class="button" type="button"><?php esc_html_e( 'Preview', 'download-after-email' ); ?></button>
	</p>
	<?php
	
}

function dae_content_meta_box_duplicate( $post ) {

	$duplicate_id = get_post_meta( $post->ID, 'dae_duplicate_id', true );
	
	?>
	<h4><?php esc_html_e( 'Use settings of another download', 'download-after-email' ); ?></h4>
	<p><input type="text" name="duplicate_id" value="<?php if ( ! empty( $duplicate_id ) ) { echo esc_attr( $duplicate_id ); } ?>" placeholder="<?php esc_attr_e( 'Download ID (e.g. 215)', 'download-after-email' ); ?>" /></p>
	<p class="dae-message-info"><?php esc_html_e( 'Duplicate the settings of another download during saving, excluding the section Download file.', 'download-after-email' ); ?></p>
	<?php

	do_action( 'dae_meta_box_duplicate', $post );

}

add_action( 'save_post', 'dae_save_meta_boxes_download' );
function dae_save_meta_boxes_download( $post_id ) {
	
	if ( isset( $_POST['post_type'] ) && 'dae_download' == $_POST['post_type'] ) {
		
		$nonce_action = 'update-post_' . $post_id;
		
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], $nonce_action ) ) {
			return;
		}
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return;
		}
		
		if ( is_multisite() && ms_is_switched() ) {
			return $post_id;
		}
		
		$shortcode = '[download_after_email id="' . $post_id . '"]';
		
		$dae_settings['file_id'] = (int) $_POST['file_id'];
		$dae_settings['file_image_id'] = (int) $_POST['file_image_id'];
		$dae_settings['file_image_size'] = sanitize_text_field( $_POST['file_image_size'] );
		$dae_settings['file_image_width_wide'] = sanitize_text_field( $_POST['file_image_width_wide'] );
		$dae_settings['file_image_width_small'] = sanitize_text_field( $_POST['file_image_width_small'] );

		$duplicate_id = (int) $_POST['duplicate_id'];

		if ( ! empty( $duplicate_id ) && 'dae_download' == get_post_type( $duplicate_id ) ) {
			$duplicate_settings = get_post_meta( $duplicate_id, 'dae_settings', true );
		} else {
			$duplicate_id = '';
		}

		if ( ! empty( $duplicate_settings ) && is_array( $duplicate_settings ) ) {

			$dae_settings = array_merge( $duplicate_settings, $dae_settings );

		} else {

			$dae_settings['background_type'] = sanitize_text_field( $_POST['background_type'] );
			if ( 'image' == $dae_settings['background_type'] ) {
				$dae_settings['background_id'] = sanitize_text_field( $_POST['background_id'] );
			} else {
				$dae_settings['background_color'] = sanitize_text_field( $_POST['background_color'] );
			}
			$dae_settings['background_attachment'] = sanitize_text_field( $_POST['background_attachment'] );
			$dae_settings['title_font_size'] = sanitize_text_field( $_POST['title_font_size'] );
			$dae_settings['title_color'] = sanitize_text_field( $_POST['title_color'] );
			$dae_settings['text_font_size'] = sanitize_text_field( $_POST['text_font_size'] );
			$dae_settings['text_color'] = sanitize_text_field( $_POST['text_color'] );
			$dae_settings['button_text'] = sanitize_text_field( $_POST['button_text'] );
			$dae_settings['button_color'] = sanitize_text_field( $_POST['button_color'] );
			$dae_settings['button_color_hover'] = sanitize_text_field( $_POST['button_color_hover'] );
			$dae_settings['button_background'] = sanitize_text_field( $_POST['button_background'] );
			$dae_settings['button_background_hover'] = sanitize_text_field( $_POST['button_background_hover'] );
			$dae_settings['button_border_color'] = sanitize_text_field( $_POST['button_border_color'] );
			$dae_settings['button_border_color_hover'] = sanitize_text_field( $_POST['button_border_color_hover'] );
			$dae_settings['button_font_size'] = sanitize_text_field( $_POST['button_font_size'] );
			$dae_settings['button_width'] = sanitize_text_field( $_POST['button_width'] );
			$dae_settings['button_padding'] = sanitize_text_field( $_POST['button_padding'] );
			$dae_settings['label'] = sanitize_text_field( $_POST['label'] );
			$dae_settings['label_font_size'] = sanitize_text_field( $_POST['label_font_size'] );
			$dae_settings['label_color'] = sanitize_text_field( $_POST['label_color'] );
			$dae_settings['input_font_size'] = sanitize_text_field( $_POST['input_font_size'] );
			$dae_settings['input_color'] = sanitize_text_field( $_POST['input_color'] );
			$dae_settings['input_background'] = sanitize_text_field( $_POST['input_background'] );
			$dae_settings['input_icon_color'] = sanitize_text_field( $_POST['input_icon_color'] );
			$dae_settings['input_icon_background'] = sanitize_text_field( $_POST['input_icon_background'] );
			$dae_settings['placeholder_text'] = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( $_POST['placeholder_text'] ) : 'Email';
			$dae_settings['placeholder_color'] = sanitize_text_field( $_POST['placeholder_color'] );
			$dae_settings['checkbox_font_size'] = sanitize_text_field( $_POST['checkbox_font_size'] );
			$dae_settings['checkbox_color'] = sanitize_text_field( $_POST['checkbox_color'] );
			$dae_settings['checkbox_link_color'] = sanitize_text_field( $_POST['checkbox_link_color'] );
			$dae_settings['checkbox_link_color_hover'] = sanitize_text_field( $_POST['checkbox_link_color_hover'] );
			$dae_settings['submit_text'] = sanitize_text_field( $_POST['submit_text'] );
			$dae_settings['submit_color'] = sanitize_text_field( $_POST['submit_color'] );
			$dae_settings['submit_color_hover'] = sanitize_text_field( $_POST['submit_color_hover'] );
			$dae_settings['submit_background'] = sanitize_text_field( $_POST['submit_background'] );
			$dae_settings['submit_background_hover'] = sanitize_text_field( $_POST['submit_background_hover'] );
			$dae_settings['submit_font_size'] = sanitize_text_field( $_POST['submit_font_size'] );
			$dae_settings['submit_message_font_size'] = sanitize_text_field( $_POST['submit_message_font_size'] );
			$dae_settings['submit_success_message_color'] = sanitize_text_field( $_POST['submit_success_message_color'] );
			$dae_settings['submit_error_message_color'] = sanitize_text_field( $_POST['submit_error_message_color'] );
			$dae_settings['border_radius'] = sanitize_text_field( $_POST['border_radius'] );
			$dae_settings['font_family'] = sanitize_text_field( str_replace( '"', '', $_POST['font_family'] ) );
			$dae_settings['alignment_wide'] = sanitize_text_field( $_POST['alignment_wide'] );
			$dae_settings['alignment_small'] = sanitize_text_field( $_POST['alignment_small'] );

		}
		
		update_post_meta( $post_id, 'dae_shortcode', $shortcode );
		update_post_meta( $post_id, 'dae_duplicate_id', $duplicate_id );
		update_post_meta( $post_id, 'dae_settings', $dae_settings );
		
		$filepath = get_attached_file( $dae_settings['file_id'], true );
		
		if ( ! empty( $filepath ) && ! strchr( $filepath, 'dae-uploads' ) ) {
			
			$upload_dir = wp_upload_dir();
			
			if ( ! empty( $upload_dir['basedir'] ) ) {
				
				$new_filepath = $upload_dir['basedir'] . '/dae-uploads/' . basename( $filepath );
				
				if ( rename( $filepath, $new_filepath ) ) {
					
					update_attached_file( $dae_settings['file_id'], $new_filepath );

					$attachment_metadata = wp_get_attachment_metadata( $dae_settings['file_id'] );

					if ( ! empty( $attachment_metadata['sizes'] ) ) {

						foreach ( $attachment_metadata['sizes'] as $image_data ) {

							$attachment_filepath = $upload_dir['basedir'] . '/' . $image_data['file'];
							
							if ( file_exists( $attachment_filepath ) ) {
								$new_attachment_filepath = $upload_dir['basedir'] . '/dae-uploads/' . $image_data['file'];
								rename( $attachment_filepath, $new_attachment_filepath );
							}

						}

					}
					
				}
				
			}
			
		}

		do_action( 'dae_save_meta_boxes_download', $post_id, $dae_settings );

	}

}

add_action( 'wp_ajax_dae_open_preview', 'dae_open_preview' );
function dae_open_preview() {
	
	check_ajax_referer( 'dae_admin' );
	
	$_POST = stripslashes_deep( $_POST );
	
	$download_title = sanitize_text_field( $_POST['download_title'] );
	$download_text = wp_kses( $_POST['download_text'], 'post' );
	$dae_settings_raw = $_POST['dae_settings'];
	$preview_css = sanitize_text_field( $_POST['preview_css'] );
	
	foreach ( $dae_settings_raw as $name => $value ) {
		
		if ( 'font_family' == $name ) {
			$value = str_replace( '"', '', $value );
		}
		
		$dae_settings[ $name ] = sanitize_text_field( $value );
		
	}
	
	if ( ! isset( $dae_settings['placeholder_text'] ) ) {
		$dae_settings['placeholder_text'] = '';
	}
	
	if ( current_user_can( 'edit_posts' ) && ! empty( $dae_settings ) ) {
		
		session_start();
		
		$_SESSION['nonce'] = $_POST['_ajax_nonce'];
		$_SESSION['action'] = 'dae_admin';
		$_SESSION['download_title'] = $download_title;
		$_SESSION['download_text'] = $download_text;
		$_SESSION['dae_settings'] = $dae_settings;
		$_SESSION['preview_css'] = $preview_css;
		
	}
	
	wp_die();
	
}

add_filter( 'manage_dae_download_posts_columns', 'dae_set_custom_edit_download_columns' );
function dae_set_custom_edit_download_columns( $columns ) {
	
	unset( $columns['date'] );
	$columns['file'] = __( 'File', 'download-after-email' );
	$columns['links'] = __( 'Links', 'download-after-email' );
	$columns['shortcode'] = __( 'Shortcode', 'download-after-email' );
	$columns['date'] = __( 'Date', 'download-after-email' );
	
	return $columns;
	
}

add_action( 'manage_dae_download_posts_custom_column' , 'dae_custom_download_column', 10, 2 );
function dae_custom_download_column( $column, $post_id ) {

	$settings = get_post_meta( $post_id, 'dae_settings', true );
	if ( ! empty( $settings['file_id'] ) ) {
		$file_name = dae_get_download_file_name( $settings['file_id'] );
	}

	switch ( $column ) {

		case 'file' :

			if ( ! empty( $file_name ) ) {
				echo esc_html( $file_name );
			}
			break;

		case 'links' :

			if ( ! empty( $file_name ) ) {
				$links_count = mckp_get_links_count( $file_name );
				echo esc_html( $links_count['used'] . ' used of ' . $links_count['total'] );
			}
			break;

		case 'shortcode' :

			$shortcode = get_post_meta( $post_id, 'dae_shortcode', true );
	
			if ( ! empty( $shortcode ) ) {
				echo esc_html( $shortcode );
			}
			break;

	}

}

?>