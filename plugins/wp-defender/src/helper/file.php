<?php

namespace WP_Defender\Helper;

/**
 * Class consists file related helper utilities.
 */
class File {

	/**
	 * Check is two files identical.
	 *
	 * @param string $local_file File path of the local file for content comparison.
	 * @param string $remote_file Url of the remote file for content comparison.
	 *
	 * @return WP_Error|string If remote fetch fails return WP_Error object or
	 * true for identical file content or false for non identical file content.
	 */
	public function is_identical_content( string $local_file, string $remote_file ) {
		wp_raise_memory_limit();

		$local_file_content = file( $local_file, FILE_IGNORE_NEW_LINES );

		$tmp = download_url( $remote_file );

		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}

		$remote_file_content = file( $tmp, FILE_IGNORE_NEW_LINES );

		@unlink( $tmp );

		return $local_file_content === $remote_file_content;
	}
}