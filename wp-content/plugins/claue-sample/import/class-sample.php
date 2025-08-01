<?php
class JAS_Demo_Sample {
	private $filename;
	public $tables = array();

	/**
	 * Construct function.
	 *
	 * @return  void
	 */
	public function __construct() {
		$this->filename = 'sample.json';
		$this->tables = array( 'options','postmeta','posts','terms','term_relationships','term_taxonomy','woocommerce_attribute_taxonomies','wc_product_meta_lookup', 'nextend2_image_storage', 'nextend2_section_storage', 'nextend2_smartslider3_generators', 'nextend2_smartslider3_sliders', 'nextend2_smartslider3_sliders_xref', 'nextend2_smartslider3_slides', 'sbi_instagram_feeds_posts', 'sbi_instagram_posts' );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 100 );
		add_action( 'wp_ajax_look_import_demo', array( $this,'import' ) );
		add_action( 'wp_ajax_nopriv_look_import_demo', array( $this,'import' ) );
	}

	/**
	 * Get demo file.
	 *
	 * @return  void
	 */
	public function getDemoFilePath() {
		return JAS_SAMPLE_PATH . '/demo-files/' . $this->filename;
	}

	/**
	 * Add sub-menu to JAS menu.
	 *
	 * @return  void
	 */
	public function admin_menu() {
		add_submenu_page(
			'jas',
			__( 'Import Sample Data', 'jas-sample' ),
			__( 'Install Sample Data', 'jas-sample' ),
			'manage_options',
			'jas-sample-data',
			array( $this, 'render_html' )
		);
	}

	/**
	 * Render admin html.
	 *
	 * @return  void
	 */
	public function render_html() {
		if ( isset($_REQUEST['export'] ) && $_REQUEST['export'] == 1 )  {
			$this->export();
			echo "<div style='padding: 5px;'>Your export file url: <a href='" . JAS_SAMPLE_URI . '/cache/' . $this->filename . "'>" . JAS_SAMPLE_URI . '/cache/' . $this->filename . '</a></div>';
		} else {
			$action = isset($_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			ob_start();
			$file =  JAS_SAMPLE_PATH . '/import/view/import.php';
			require( $file );
			echo ob_get_clean();

			if( 'demo-data' == $action && check_admin_referer( 'jas-demo-code' , 'demononce' ) ) {
				if ( $_POST && isset( $_POST['layout'] ) ) {
					echo '<p style="color:green">Start import! Please wait...</p>';
						$this->import( esc_attr( $_POST['layout'] ) );
					echo '<p style="color:green">Done!</p>';
				}
			}
		}
	}

	/**
	 * Import database.
	 *
	 * @return  void
	 */
	public function import() {
		global $wpdb, $table_prefix;
		$view = $_REQUEST['layout'];
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) define( 'WP_LOAD_IMPORTERS', true );

		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

			if ( file_exists( $class_wp_importer ) ) {
				require_once( $class_wp_importer );
			}
		}

		if ( ! class_exists( 'WP_Import' ) ) {
			$class_wp_import = JAS_SAMPLE_PATH . '/import/wordpress-importer.php';

			if ( file_exists( $class_wp_import ) )
				require_once($class_wp_import);
			else
				$importer_error = true;
		}

		$content = file_get_contents( JAS_SAMPLE_PATH . '/demo-files/' . $view . '/' . $this->filename );
		$data = ( array )json_decode( $content );

		foreach( $data as $key => $value ) {
			$table = $table_prefix.$key;
			$rows = $value;
			if ( $key == 'options' ) {
				$wpdb->get_results( 'DELETE FROM ' . $table . ' WHERE option_name NOT IN ("siteurl","fileupload_url","home","' . $table_prefix . 'user_roles")' );
			} else {
				$wpdb->get_results( 'TRUNCATE TABLE ' . $table );
			}
			foreach( $rows as $row ) {
				$row = ( array )$row;
				if ( $key == 'options' ) {
					if ( $row['option_name'] != 'wp_user_roles' && $row['option_name'] != 'siteurl' && $row['option_name'] != 'fileupload_url' && $row['option_name'] != 'home' ) {
						$wpdb->insert($table,$row);
					}
				} else {
					$wpdb->insert( $table, $row );
				}
			}
		}

		$args = array(
			'post_type'      => 'attachment',
			'order'          => 'ASC',
			'posts_per_page' => 1000
		);

		$to_url = get_site_url();
		$urls   = array( 'http://janstudio.net/claue' );
		foreach( $urls as $from_url ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='_menu_item_url'", $from_url, $to_url ) );
		}

		$posts = get_posts( $args );
		$wp_import = new WP_Import();
		foreach ( $posts as $post ) {
			$post = ( array )$post;
			$post['upload_date'] = date( 'Y-m-d' );
			$url = JAS_SAMPLE_URI . '/demo-files/img.png';
			$upload = $wp_import->fetch_remote_file( $url, ( array )$post );
			if ( is_array( $upload ) ) {
				update_attached_file( $post['ID'], $upload['file'] );
				wp_update_attachment_metadata( $post['ID'], wp_generate_attachment_metadata( $post['ID'], $upload['file'] ) );
			}
		}
		wp_reset_postdata();
	}

	/**
	 * Export database.
	 *
	 * @return  void
	 */
	public function export() {
		global $wpdb, $table_prefix;

		$result = array();
		if ( is_writable( JAS_SAMPLE_PATH . '/cache' ) ) {
			foreach( $this->tables  as $table ) {
				if ( $table == 'postmeta' ) {
					$sql = "SELECT postmeta.* FROM " . $table_prefix . $table . " postmeta LEFT JOIN " . $table_prefix . "posts post ON post.ID = postmeta.post_id WHERE post.post_status = 'publish' OR post.post_status = 'inherit' ";
					$result[$table] = $wpdb->get_results( $sql );

				} elseif ( $table == 'posts' ) {
					$result[$table] = $wpdb->get_results( "SELECT * FROM " . $table_prefix . $table . " post  WHERE post.post_status = 'publish' OR post.post_status = 'inherit'" );

				} elseif ( $table == 'options' ) {
					$sql = "SELECT * FROM " . $table_prefix . $table;
					$sql .= " WHERE " . $table_prefix . $table . " . option_id NOT IN (SELECT option_id FROM " . $table_prefix . $table . " WHERE option_name LIKE '%_transient_%' )";
					$result[$table] = $wpdb->get_results( $sql );

				} else {
					$result[$table] = $wpdb->get_results( "SELECT * FROM " . $table_prefix . $table );
				}
			}

			file_put_contents(
				JAS_SAMPLE_PATH . '/cache/' . $this->filename,
				json_encode( $result )
			);
			return true;
		}
		return false;
	}
}

