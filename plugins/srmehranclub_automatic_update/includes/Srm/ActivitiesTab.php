<?php
 if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Activities controller
 */
class ActivitiesTab extends WP_List_Table
{	
	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Activity', 'sr' ), //singular name of the listed records
			'plural' => __( 'Activities', 'sr' ), //plural name of the listed records
			'ajax' => false //should this table support ajax?
			] );
	
	}

	/**
	* Retrieve customer’s data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_customers( $per_page = 5, $page_number = 1 ) {

		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->prefix}srclubplugins_history ORDER BY date DESC";
		
		// if ( ! empty( $_REQUEST['orderby'] ) ) {
		// $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		// $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		// }
		
		$sql .= " LIMIT $per_page";
		
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		
		return $result;
		}
	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {
		global $wpdb;
		
		$sql = "SELECT COUNT(id) FROM {$wpdb->prefix}srclubplugins_history";
		
		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No Activities avaliable.', 'sr' );
	}

	/**
	* Render a column when no column specific method exists.
	*
	* @param array $item
	* @param string $column_name
	*
	* @return mixed
	*/
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'product_name':
                return $item[ $column_name ];
            case 'type':
                return $item[ $column_name ];
            case 'version':
                return $item[ $column_name ];
            case 'action':
                return ucFirst($item[ $column_name ]);
            case 'path':
                return site_url();
			case 'date':
				return date('d-m-Y h:i a',strtotime($item[ $column_name ]));
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	* Associative array of columns
	*
	* @return array
	*/
	function get_columns() {
		$columns = [
		'product_name' => __( 'Product Name', 'sp' ),
		'type' => __( 'Type', 'sp' ),
		'version' => __( 'Version', 'sp' ),
        'action'=>'Activities',
        'path'=>'Site Url',
		'date'=>'Date'
		];

	return $columns;
	}

	/**
	* Handles data query and filter, sorting, and pagination.
	*/
	public function prepare_items() {

        $this->_column_headers = [
            $this->get_columns(),
            [], // hidden columns
            $this->get_sortable_columns(),
            $this->get_primary_column_name(),
        ];
		
		$per_page =12;
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();
		
		$this->set_pagination_args( [
		'total_items' => $total_items, //WE have to calculate the total number of items
		'per_page' => $per_page //WE have to determine how many items to show on a page
		] );
		
		$this->items = self::get_customers( $per_page, $current_page );
	}
	 
	 
}