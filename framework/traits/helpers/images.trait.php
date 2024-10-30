<?php

/**
 * Trait cjwpbldr_helpers_images
 */
trait cjwpbldr_helpers_images{
	/**
	 * @param $image
	 *
	 * @return mixed
	 */
	public function imageSrc( $image ) {
		if( gettype( $image ) == 'string' && strpos( $image, 'img' ) > 0 ) {
			$xpath = new DOMXPath( @DOMDocument::loadHTML( $image ) );
			$src = $xpath->evaluate( "string(//img/@src)" );

			return $src;
		} else {
			return $image;
		}
	}

	/**
	 * @param        $image
	 * @param array  $size
	 * @param bool   $crop
	 * @param int    $quality
	 * @param string $destination_file_path
	 *
	 * @return mixed|null
	 */
	public function imageResize( $image, $size = [], $crop = false, $quality = 100, $destination_file_path = '' ) {
		$img = wp_get_image_editor( $image );
		if( ! is_wp_error( $img ) ) {
			$path = dirname( str_replace( $this->root_dir, $this->root_url, $image ) );
			$img->resize( $size[0], $size[1], $crop );
			$img->set_quality( $quality );
			$suffix = $img->get_suffix();
			if( $destination_file_path == '' ) {
				$filename = $img->generate_filename( $suffix, $path, 'png' );
			} else {
				$filename = $destination_file_path;
			}
			$saved = $img->save( $filename );

			if( ! is_wp_error( $saved ) ) {
				return str_replace( $this->root_dir, $this->root_url, $saved['path'] );
			}
		} else {
			return null;
		}
	}

	/**
	 * @param $path
	 *
	 * @return false|string
	 */
	public function getSvgCode( $path ) {
		return file_get_contents( $path );
	}

	/**
	 * @param $img_path
	 *
	 * @return string
	 */
	public function imgToBase64( $img_path ) {
		$arrContextOptions = [
			"ssl" => [
				"verify_peer" => false,
				"verify_peer_name" => false,
			],
		];

		$img_path = str_replace( $this->site_url, ABSPATH, $img_path );
		$type = pathinfo( $img_path, PATHINFO_EXTENSION );
		$data = file_get_contents( $img_path, false, stream_context_create( $arrContextOptions ) );
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );

		return $base64;
	}

	/**
	 * @param     $base64_string
	 * @param     $destination_file_path
	 * @param int $quality
	 *
	 * @return mixed|null
	 */
	public function base64toJpeg( $base64_string, $destination_file_path, $quality = 0 ) {
		$string = $base64_string;
		if( $this->containString( 'base64,', $base64_string ) ) {
			$data = explode( 'base64,', $base64_string );
			if( isset( $data[1] ) ) {
				$string = $data[1];
			}
		}
		$this->putFileContents( $destination_file_path, base64_decode( $string ) );
		$url = str_replace( ABSPATH, site_url( '/' ), $destination_file_path );

		if( $quality > 0 ) {
			$url = $this->imageResize( $url, [1024, 1024], false, 80, $destination_file_path );
			$url = str_replace( ABSPATH, site_url( '/' ), $url );
		}

		return $url;
	}

	/**
	 * @param string $image_url
	 * @param string $image_name_with_extension
	 * @param string $return
	 *
	 * @return array|bool|false|int|string|WP_Error
	 */
	public function saveImageFromUrl( $image_url = '', $image_name_with_extension = 'random-image.png', $return = 'url' ) {
		//If image URL is empty, bail out!
		if( '' == $image_url ) {
			return false;
		}
		//Check if an image is a valid image URL
		$url = str_replace( "https://", "http://", $image_url );
		// Add Featured Image to Post
		$upload_dir = wp_upload_dir(); // Set upload folder
		$image_data = file_get_contents( $url ); // Get image data
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name_with_extension ); // Generate unique name
		$filename = basename( $unique_file_name ); // Create image file name
		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		// Create the image file on the server
		$this->putFileContents( $file, $image_data );

		// Check image file type
		$wp_file_type = wp_check_filetype( $filename, null );
		// Set attachment data
		$attachment = [
			'post_mime_type' => $wp_file_type['type'],
			'post_title' => sanitize_file_name( $filename ),
			'post_content' => '',
			'post_status' => 'inherit'
		];
		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file );
		//If image is saved as an attachment successfully
		if( $attach_id ) {
			// Include image.php
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id, $attach_data );
			$result = ($return == 'url') ? wp_get_attachment_url( $attach_id ) : $attach_id;
		} else {
			//return an error if the image is saved not saved attached in the first place
			$result = [
				'error' => __(
					'Image could not be saved as an attachment. Please try again later.',
					'cjwpbldr'
				)
			];
		}

		return $result;
	}

	/**
	 * Check if a given type is an image. This would only check if the file
	 * extension is given in a valid format.
	 *
	 * Valid Format: jpg, jpeg, png, ....
	 * InValid Format: image/jpg, image/jpeg, image/png ...
	 *
	 * @param string $image_type | File extension.
	 *
	 * @return bool
	 * @since   1.0.0
	 * @package WP Builder
	 */
	public function isImage( $image_type = '' ) {

		if( '' == $image_type ) {
			return false;
		}

		$image_types = [
			'jpg',
			'jpeg',
			'png',
			'gif',
		];

		if( in_array( $image_type, $image_types ) ) {
			return true;
		}

		return false;
	}
}