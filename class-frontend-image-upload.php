<?php
/**
 * Manages front-end image uploads.
 *
 * @package Frontend_Image_Upload
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Frontend_Image_Upload' ) ) {

	/**
	 * Frontend_Image_Upload Class.
	 */
	class Frontend_Image_Upload {

		/**
		 * Constructor.
		 */
		public function __construct() {

			// Loads wp-media & our own script.
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			// Filters the attachments ajax query that fetches the files shown in the modal library tab.
			add_filter( 'ajax_query_attachments_args', array( $this, 'media_ajax_query' ) );

			// Hook into Media Upload to apply our filters.
			add_filter( 'wp_handle_upload_prefilter', array( $this, 'handle_upload_prefilter' ) );
		}


		/**
		 * Scripts loading.
		 */
		public function load_scripts() {
			wp_enqueue_media();
			wp_enqueue_script( 'my-upload-script', plugin_dir_url( __FILE__ ) . 'my-upload-script.js', array( 'jquery' ), '1.0', true );
			// Let's pass our filter values to our script.
			wp_add_inline_script(
				'my-upload-script',
				'const myUploads = ' . json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
					array(
						'maxFileSize' => 1048576,
						'mimeTypes'   => 'png,jpeg,jpg',
					)
				),
				'before'
			);
		}


		/**
		 * Filters the arguments passed to WP_Query during an Ajax call for querying attachments.
		 *
		 * @param array $query Array of query variables.
		 * @return array
		 */
		public function media_ajax_query( $query ) {

			// Non admins will only see their own images in the modal.
			if ( ! current_user_can( 'update_core' ) ) {
				$query['author'] = get_current_user_id();
			}

			return $query;
		}



		/**
		 * Filters data for the current file being uploaded.
		 *
		 * @param array $file An array of data for a single file.
		 * @return array
		 */
		public function handle_upload_prefilter( $file ) {
			$user = wp_get_current_user();

			// Admins see all.
			if ( $user->has_cap( 'update_core' ) ) {
				return $file;
			}

			// Image file filtering for our use case.
			if ( isset( $_POST['_my_frontend_page_1'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$post_value = intval( wp_unslash( $_POST['_my_frontend_page_1'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

				// Check our custom $_POST value and the user: only logged-in users with the proper role shall upload.
				if ( 1 !== $post_value || ! in_array( 'specific_role', $user->roles, true ) ) {
					return array();
				}

				// Validate the file.
				$errors = $this->validate_img( $file );

				// If our validation function returns any error, we add them to the file data.
				if ( ! empty( $errors ) ) {
					$file['error'] = implode( "\n", $errors );
				}
			}

			return $file;
		}



		/**
		 * Validates the image uploaded by the user.
		 *
		 * @param array $file Array of uploaded file data.
		 *
		 * @return array
		 */
		private function validate_img( $file ) {
			$errors = array();

			// Max size in bytes, 1 MB.
			$max_size = 1048576;
			// Authorized file extensions.
			$exts = array( 'png', 'jpg', 'jpeg' );
			// Get the uploaded file extension.
			$type = pathinfo( $file['name'], PATHINFO_EXTENSION );

			// File extension check.
			if ( ! in_array( $type, $exts, true ) ) {
				$errors['mime_types'] = sprintf(
					esc_html__( 'File type must be %s', 'my-text-domain' ),
					implode( ', ', $exts )
				);
			}

			// File max size check.
			if ( $file['size'] > $max_size ) {
				$errors['max_size'] = sprintf(
					esc_html__( 'File size must be less than or equal to %s MB', 'my-text-domain' ),
					$max_size
				);
			}

			return $errors;
		}

	}

	return new Frontend_Image_Upload();
}
