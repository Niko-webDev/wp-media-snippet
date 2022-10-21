<?php
/**
 * WP uploader mark-up.
 *
 *  @package Frontend_Image_Upload
 */

defined( 'ABSPATH' ) || exit;

?>
<h3><?php esc_html_e( 'Front-end wp uploader', 'my-text-domain' ); ?></h3>

<div>
	<p class="mock_label"><?php esc_html_e( 'Upload Your Image', 'my-text-domain' ); ?></p>

	<a class="img_upload_action img_add" href="#"><?php esc_html__e( 'Add image', 'my-text-domain' ); ?></a>
	<div class="img_preview">
		<input type="hidden" class="img_id">
		<img src="" style="max-height: 150px;">
	</div>
	<a class="img_upload_action img_remove" href="#" style="display:none;"><?php esc_html__e( 'Remove image', 'my-text-domain' ); ?></a>
</div>
