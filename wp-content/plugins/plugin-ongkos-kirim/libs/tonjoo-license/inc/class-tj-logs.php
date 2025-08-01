<?php

if ( ! class_exists( 'TJ_Logs' ) ) {

	/**
	 * Class TJ_Logs
	 */
	class TJ_Logs {

		/**
		 * Dir path
		 *
		 * @var string
		 */
		public $dir;

		/**
		 * Full file path
		 *
		 * @var string
		 */
		public $file;

		/**
		 * Max line
		 * 
		 * @var integer
		 */
		public $max_line;

		/**
		 * Constructor
		 *
		 * @param string  $filename File name (without extension).
		 * @param integer $max_line Logs max line.
		 */
		public function __construct( $filename = 'log', $max_line = 300 ) {

			$this->dir = WP_CONTENT_DIR . '/uploads/tj-logs/';

			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}
			if ( ! $wp_filesystem->exists( $this->dir ) ) {
				$wp_filesystem->mkdir( $this->dir );
			}

			$this->file = $this->dir . $filename . '.txt';
			$this->max_line = $max_line;

		}

		/**
		 * Check if path is wrtable
		 *
		 * @param  string $path Location path.
		 * @return boolean       Is writable.
		 */
		public function is_writable( $path = '' ) {
			if ( empty( $path ) ) {
				$path = $this->dir;
			}
			global $wp_filesystem;
			include_once ABSPATH . 'wp-admin/includes/file.php';
			// If for some reason the include doesn't work as expected just return false.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				return false;
			}
			$writable = WP_Filesystem( false, $path );
			// We consider the directory as writable if it uses the direct transport, otherwise credentials would be needed.
			return $writable && 'direct' === $wp_filesystem->method;
		}

		/**
		 * Write log
		 *
		 * @param  string $message Message content.
		 */
		public function write( $message = '' ) {
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}

			// read file as array.
			$content = $wp_filesystem->get_contents_array( $this->file );
			// if file is empty, create it.
			if ( ! is_array( $content ) ) {
				$content = array();
			}
			// insert new line here.
			$content[] = '[' . $this->get_local_time() . '] ' . $message;

			// delete old logs.
			if ( $this->max_line < count( $content ) ) {
				array_shift( $content );
			}

			// compile logs as single string.
			$logs = '';
			foreach ( $content as $k => $line ) {
				if ( '' !== $line ) {
					$logs .= trim( $line ) . PHP_EOL;
				}
			}

			do_action( 'tj_log_write', $this );

			return $wp_filesystem->put_contents( $this->file, $logs );
		}

		/**
		 * Read all logs
		 *
		 * @param  boolean $to_array Set true to read file as array.
		 * @return array|string           Logs contents, return array if $to_array set true.
		 */
		public function read( $to_array = false ) {
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}

			if ( true === $to_array ) {
				return $wp_filesystem->get_contents_array( $this->file );
			}
			return $wp_filesystem->get_contents( $this->file );
		}

		/**
		 * Clear logs
		 */
		public function clear() {
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}
			return $wp_filesystem->put_contents( $this->file, '' );
		}

		/**
		 * Get local time from timestamps
		 *
		 * @param  integer $time Timestamps.
		 * @return string        Current time in local format.
		 */
		private function get_local_time( $time = 0 ) {
			if ( 0 === $time ) {
				$time = time();
			}
			$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			return date( 'Y-m-d H:i:s', $time + $offset );
		}

		/**
		 * Get file url
		 *
		 * @return string File url.
		 */
		public function get_file_url() {
			return apply_filters( 'tj_log_file_url', str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $this->file ) );
		}

	}

}
