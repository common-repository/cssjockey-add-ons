<?php

trait cjwpbldr_helpers_filesystem {
	public function createDirectory( $path ) {
		global $wp_filesystem;
		// filesystem vars
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		$method = null;
		$context = '';
		$fields = ['file-data'];
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		if( is_dir( $path ) ) {
			return $path;
		}

		if( ! is_dir( $path ) ) {
			$wp_filesystem->mkdir( $path );

			return $path;
		}

		return false;
	}

	public function getFileContents( $source_file ) {
		global $wp_filesystem;
		// filesystem vars
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		$method = null;
		$context = '';
		$fields = ['file-data'];
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		return $wp_filesystem->get_contents( $source_file );
	}

	public function putFileContents( $file_path, $file_content ) {
		global $wp_filesystem;
		// filesystem vars
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		$method = null;
		$context = '';
		$fields = ['file-data'];
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		return $wp_filesystem->put_contents( $file_path, $file_content, FS_CHMOD_FILE );
	}

	public function copyFile( $file_source, $file_destination ) {
		global $wp_filesystem;
		WP_Filesystem();
		// filesystem vars
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		$method = null;
		$context = '';
		$fields = ['file-data'];
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		$check = $wp_filesystem->copy( $file_source, $file_destination );
		if( is_wp_error( $check ) ) {
			return $check;
		} else {
			return $check;
		}

		return false;
	}

	public function copyDirectory( $src, $dst ) {
		$dir = opendir( $src );
		@mkdir( $dst );
		while( false !== ($file = readdir( $dir )) ) {
			if( ($file != '.') && ($file != '..') ) {
				if( is_dir( $src . '/' . $file ) ) {
					$this->copyDirectory( $src . '/' . $file, $dst . '/' . $file );
				} else {
					copy( $src . '/' . $file, $dst . '/' . $file );
				}
			}
		}
		closedir( $dir );
	}

	public function replaceFileContents( $source_file, $destination_file, $replace, $with ) {
		global $wp_filesystem;
		// filesystem vars
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		$method = null;
		$context = '';
		$fields = ['file-data'];
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}
		if( file_exists( $source_file ) ) {
			$css = $wp_filesystem->get_contents( $source_file );
			$css = str_replace( $replace, $with, $css );
			$wp_filesystem->put_contents( $destination_file, $css, FS_CHMOD_FILE );

			return 'done';
		} else {
			return 'file not found';
		}
	}

	public function connectFileSystem( $url, $method, $context, $fields = null ) {
		if( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
			return false;
		}
		if( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		return true;
	}

	public function unzipFile( $source_zip_file = null, $destination_path = null ) {
		$errors = null;
		if( is_null( $source_zip_file ) ) {
			$errors[] = __( 'You must specify source zip file.', 'wp-builder-locale' );

			return $errors;
		}
		if( is_null( $destination_path ) ) {
			$errors[] = __( 'You must specify a destination path.', 'wp-builder-locale' );

			return $errors;
		}

		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		if( $this->connectFileSystem( $url, '', '', 'module-file' ) ) {
			$unzip_file = unzip_file( $source_zip_file, $destination_path );
			if( $unzip_file ) {
				return true;
			} else {
				$errors[] = __( 'There was an error unzipping the file.', 'wp-builder-locale' );

				return $errors;
			}
		}
	}

	public function deleteFile( $file_path = null ) {
		$errors = null;
		if( is_null( $file_path ) ) {
			$errors[] = __( 'You must specify a file path.', 'wp-builder-locale' );

			return $errors;
		}
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		if( $this->connectFileSystem( $url, '', '', 'module-file' ) ) {
			$file_path = str_replace( $this->site_url, ABSPATH, $file_path );
			if( file_exists( $file_path ) ) {
				unlink( $file_path );
			}

			return true;
		}
	}

	public function deleteDirectory( $dir_path = null ) {
		$errors = null;
		if( is_null( $dir_path ) ) {
			$errors[] = __( 'You must specify a file path.', 'wp-builder-locale' );

			return $errors;
		}
		$url = wp_nonce_url( $this->currentUrl(), 'wp-builder-locale' );
		if( $this->connectFileSystem( $url, '', '', 'module-file' ) ) {
			$files = array_diff( scandir( $dir_path ), ['.', '..'] );
			foreach( $files as $file ) {
				(is_dir( "$dir_path/$file" )) ? $this->deleteDirectory( "$dir_path/$file" ) : unlink( "$dir_path/$file" );
			}
			rmdir( $dir_path );

			return true;
		}
	}

	public function zipFolder( $source, $destination ) {
		if( extension_loaded( 'zip' ) ) {
			if( file_exists( $source ) ) {
				$zip = new ZipArchive();
				if( $zip->open( $destination, ZIPARCHIVE::CREATE ) ) {
					$source = realpath( $source );
					if( is_dir( $source ) ) {
						$iterator = new RecursiveDirectoryIterator( $source );
						// skip dot files while iterating
						$iterator->setFlags( RecursiveDirectoryIterator::SKIP_DOTS );
						$files = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
						foreach( $files as $file ) {
							$file = realpath( $file );
							if( is_dir( $file ) ) {
								$zip->addEmptyDir( str_replace( $source . '/', '', $file . '/' ) );
							} elseif( is_file( $file ) ) {
								$zip->addFromString( str_replace( $source . '/', '', $file ), file_get_contents( $file ) );
							}
						}
					} elseif( is_file( $source ) ) {
						$zip->addFromString( basename( $source ), file_get_contents( $source ) );
					}
				}
				$zip->close();

				return $destination;
			}
		}

		return false;
	}

	public function uploadFile( $file, $allowed_width = null, $allowed_height = null, $allowed_file_types = null, $allowed_file_size = null, $output = 'guid', $upload_dir = null ) {
		global $wpdb;
		if( ! function_exists( 'wp_handle_upload' ) ) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$wp_upload_dir = wp_upload_dir();
		$KB = '1024';
		$MB = $KB * 1024;
		$GB = $MB * 1024;
		$TB = $GB * 1024;
		$errors = null;
		if( is_null( $upload_dir ) ) {
			$targetPath = trailingslashit( $wp_upload_dir['path'] );
		} else {
			$this->createDirectory( $upload_dir );
			$targetPath = trailingslashit( $upload_dir );
		}

		$tempFile = @$file['tmp_name'];
		$targetFile = @$file['name'];
		$fileParts = @pathinfo( $file['name'] );
		$ext = '.' . @$fileParts['extension'];
		$allowed_ext = @$fileParts['extension'];
		$file_size = @$file['size'];
		if( is_array( $file_size ) ) {
			$file_size = $file_size[0];
		}
		if( is_array( $file['type'] ) ) {
			$file['type'] = $file['type'][0];
		}

		if( ! is_null( $allowed_file_size ) && $file_size > $allowed_file_size ) {
			$errors[] = sprintf( esc_attr__( 'File size must be below %s MB.', 'wp-builder-locale' ), number_format( $allowed_file_size / (1024 * 1024), 1 ) );
		}
		list( $img_width, $img_height ) = @getimagesize( $tempFile );
		if( ! is_null( $allowed_width ) && $img_width > $allowed_width ) {
			$errors[] = sprintf( esc_attr__( 'Image width must be %s pixels.', 'wp-builder-locale' ), $allowed_width );
		}
		if( ! is_null( $allowed_height ) && $img_height > $allowed_height ) {
			$errors[] = sprintf( esc_attr__( 'Image height must be %s pixels.', 'wp-builder-locale' ), $allowed_height );
		}

		if( ! is_null( $allowed_file_types ) ) {
			if( ! $this->isFileMimeTypeAllowed( $file['type'] ) ) {
				$errors['file_type'] = sprintf( esc_attr__( 'You must upload a valid file. <b>%s</b> is not allowed.', 'wp-builder-locale' ), $file['type'] );
			} else {
				if( isset( $fileParts['extension'] ) ) {
					if( gettype( $allowed_file_types ) == 'string' && ! $this->containString( $fileParts['extension'], $allowed_file_types ) ) {
						$errors['file_type'] = sprintf( esc_attr__( 'You must upload a valid file. <b>%s</b> is not allowed.', 'wp-builder-locale' ), $file['type'] );
					}
					if( gettype( $allowed_file_types ) == 'array' && ! in_array( $fileParts['extension'], $allowed_file_types ) ) {
						$errors['file_type'] = sprintf( esc_attr__( 'You must upload a valid file. <b>%s</b> is not allowed.', 'wp-builder-locale' ), $file['type'] );
					}
				}
			}
		}

		if( is_array( $errors ) ) {
			return ['errors' => array_values( $errors )];
		} else {
			if( ! is_null( $targetPath ) ) {
				if( is_array( $targetFile ) ) {
					$targetFile = $targetFile[0];
				}
				$newFileName = wp_unique_filename( $targetPath, $targetFile );
				$targetFile = str_replace( '//', '/', $targetPath ) . $newFileName;
				$filename = $targetFile;
				$wp_file_type = wp_check_filetype( basename( $filename ), null );
				$upload_overrides = ['test_form' => false];
				$file_handler = wp_handle_upload( $file, $upload_overrides );

				if( $file_handler && ! isset( $file_handler['error'] ) ) {
					$attachment = [
						'post_mime_type' => $wp_file_type['type'],
						'post_title' => sanitize_file_name( $newFileName ),
						'post_content' => '',
						'post_status' => 'inherit',
					];
					$attach_id = wp_insert_attachment( $attachment, $filename );
					if( $attach_id ) {
						require_once(wp_normalize_path( ABSPATH . 'wp-admin/includes/image.php' ));
						require_once(wp_normalize_path( ABSPATH . 'wp-admin/includes/media.php' ));
						$attach_data = wp_generate_attachment_metadata( $attach_id, $file_handler['url'] );
						wp_update_attachment_metadata( $attach_id, $attach_data );
					}
					if( $output == 'guid' ) {
						return $file_handler['url'];
					} else {
						return [
							'path' => $file_handler['file'],
							'guid' => $file_handler['url'],
						];
					}
				} else {
					return ['errors' => $file_handler['error']];
				}
			}
		}
	}

}