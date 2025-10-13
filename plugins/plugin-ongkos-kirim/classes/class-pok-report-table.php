<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Shipping report table
 */
class POK_Report_Table extends WP_List_Table {

	/**
	 * Start date
	 *
	 * @var string
	 */
	protected $start_date;

	/**
	 * End date
	 *
	 * @var string
	 */
	protected $end_date;

	/**
	 * Bank name
	 *
	 * @var string
	 */
	protected $courier;

	/**
	 * POK Core
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * POK Helper
	 *
	 * @var object
	 */
	protected $helper;

	/**
	 * Max items.
	 *
	 * @var int
	 */
	protected $max_items;

	/**
	 * Total cost
	 *
	 * @var float
	 */
	protected $total_cost;

	/**
	 * Total weight
	 *
	 * @var float
	 */
	protected $total_weight;

	/**
	 * Constructor
	 *
	 * @param string $courier Courier name.
	 */
	public function __construct( $courier ) {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		$this->courier  = $courier;
		parent::__construct(
			array(
				'singular' => 'Shipping Courier',
				'plural'   => 'Shipping Courier',
				'ajax'     => false,
			)
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No order found', 'pok' );
	}

	/**
	 * Set table classes
	 *
	 * @return array Classes.
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$ranges = apply_filters(
			'pok_report_ranges', array(
				'year'       => __( 'Year', 'pok' ),
				'last_month' => __( 'Last month', 'pok' ),
				'month'      => __( 'This month', 'pok' ),
				'7day'       => __( 'Last 7 days', 'pok' ),
			)
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day'; // WPCS: Input var okay, CSRF okay.

		if ( ! in_array( $current_range, array_merge( array( 'custom' ), array_keys( $ranges ) ), true ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );
		$this->prepare_items();

		?>
		<div id="poststuff" class="pok-report woocommerce-reports-wide">
			<div class="postbox">
				<div class="stats_range">
					<ul>
						<?php
						foreach ( $ranges as $range => $name ) {
							echo '<li class="' . ( $current_range === $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . esc_html( $name ) . '</a></li>';
						}
						?>
						<li class="custom <?php echo ( 'custom' === $current_range ) ? 'active' : ''; ?>">
							<?php esc_html_e( 'Custom:', 'pok' ); ?>
							<form method="GET">
								<div>
									<?php
									// Maintain query string.
									foreach ( $_GET as $key => $value ) { // WPCS: Input var okay, CSRF okay.
										if ( is_array( $value ) ) {
											foreach ( $value as $v ) {
												echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
											}
										} else {
											echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
										}
									}
									?>
									<input type="hidden" name="range" value="custom" />
									<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( wp_unslash( $_GET['start_date'] ) ) : ''; ?>" name="start_date" class="range_datepicker from" /><?php //@codingStandardsIgnoreLine ?>
									<span>&ndash;</span>
									<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( wp_unslash( $_GET['end_date'] ) ) : ''; ?>" name="end_date" class="range_datepicker to" /><?php //@codingStandardsIgnoreLine ?>
									<button type="submit" class="button" value="<?php esc_attr_e( 'Go', 'pok' ); ?>"><?php esc_html_e( 'Go', 'pok' ); ?></button>
									<?php wp_nonce_field( 'custom_range', 'wc_reports_nonce', false ); ?>
								</div>
							</form>
						</li>
					</ul>
				</div>
				<div id="pok-report-summary">
					<table>
						<?php do_action( 'pok_report_summary_before', $this ); ?>
						<tr>
							<td class="index"><?php esc_html_e( 'Courier', 'pok' ); ?></td>
							<td>:</td>
							<td class="value"><?php echo 'all' === $this->courier ? esc_html__( 'All Courier', 'pok' ) : esc_html( $this->helper->get_courier_name( $this->courier ) ); ?></td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Period', 'pok' ); ?></td>
							<td>:</td>
							<td class="value"><?php echo esc_html( date_i18n( get_option( 'date_format' ), $this->start_date ) . ' - ' . date_i18n( get_option( 'date_format' ), $this->end_date ) ); ?></td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Order Count', 'pok' ); ?></td>
							<td>:</td>
							<td class="value"><?php echo esc_html( $this->max_items ); ?></td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Total Cost', 'pok' ); ?></td>
							<td>:</td>
							<td class="value"><?php echo wp_kses_post( wc_price( $this->total_cost ) ); ?></td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Total Shipping Weight', 'pok' ); ?></td>
							<td>:</td>
							<td class="value"><?php echo esc_html( number_format( floatval( $this->total_weight ), 2, get_option( 'woocommerce_price_decimal_sep' ), get_option( 'woocommerce_price_thousand_sep' ) ) ); ?> kg</td>
						</tr>
						<?php do_action( 'pok_report_summary_after', $this ); ?>
					</table>
				</div>
			</div>
			<?php $this->display(); ?>
		</div>
		<?php
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'id'            => __( 'Order', 'pok' ),
			'order_date'    => __( 'Order Date', 'pok' ),
			'weight'        => __( 'Weight', 'pok' ),
			'courier'       => __( 'Courier', 'pok' ),
			'service'       => __( 'Service', 'pok' ),
			'insurance'     => __( 'Insurance Fee', 'pok' ),
			'timber_packing' => __( 'Timber Packing Fee', 'pok' ),
			'cost'          => __( 'Total Cost', 'pok' ),
			'status'        => __( 'Status', 'pok' ),
		);

		return apply_filters( 'pok_report_columns', $columns );
	}

	/**
	 * Set sortable columns
	 *
	 * @return array Columns.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'id'            => array( 'order_id', true ),
			'order_date'    => array( 'order_date', true ),
			'courier'       => array( 'courier', true ),
			'service'       => array( 'service', true ),
			'cost'          => array( 'cost', true ),
			'insurance'     => array( 'insurance', true ),
			'timber_packing' => array( 'timber_packing', true ),
			'weight'        => array( 'weight', true ),
			'status'        => array( 'status', true ),
		);
		return apply_filters( 'pok_report_sortable_columns', $sortable_columns );
	}

	/**
	 * Get column value.
	 *
	 * @param mixed  $item          Item.
	 * @param string $column_name   Column name.
	 */
	public function column_default( $item, $column_name ) {
		$output = '';
		switch ( $column_name ) {
			case 'id':
				$output = '<a href="' . esc_url( admin_url( 'post.php?action=edit&post=' . $item->order_id ) ) . '"><strong>#' . esc_html( $item->order_id ) . '</strong></a>';
				break;
			case 'order_date':
				$output  = esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item->order_date ) ) );
				$output .= '<br>';
				$output .= esc_html( date_i18n( get_option( 'time_format' ), strtotime( $item->order_date ) ) );
				break;
			case 'courier':
				$output = esc_html( $this->helper->get_courier_name( $item->courier ) );
				break;
			case 'service':
				$output = esc_html( $item->service );
				break;
			case 'insurance':
				$output = ( ! empty( $item->insurance ) ? wp_kses_post( wc_price( $item->insurance ) ) : '—' );
				break;
			case 'timber_packing':
				$output = ( ! empty( $item->timber_packing ) ? wp_kses_post( wc_price( $item->timber_packing ) ) : '—' );
				break;
			case 'cost':
				$output = wp_kses_post( wc_price( $item->cost ) );
				break;
			case 'weight':
				$output = esc_html( floatval( $item->weight ) ) . ' kg';
				break;
			case 'status':
				$output = esc_html( wc_get_order_status_name( $item->order_status ) );
				break;
		}
		echo apply_filters( 'pok_report_column', $output, $column_name, $item, $this ); // WPCS: XSS ok.
	}

	/**
	 * Show additional filter
	 *
	 * @param  string $which Tablenav location.
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			?>
			<form action=""> 
				<div class="alignleft actions">

					<select name="status">
						<option value=""><?php esc_html_e( 'All Order Status', 'pok' ); ?></option>
						<?php foreach ( wc_get_order_statuses() as $key => $status ) : ?>
							<option <?php echo isset( $_GET['status'] ) && sanitize_text_field( wp_unslash( $_GET['status'] ) ) === $key ? 'selected' : ''; // WPCS: Input var okay, CSRF okay. ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $status ); ?></option>
						<?php endforeach; ?>
					</select>

					<?php if ( 'nusantara' === $this->setting->get( 'base_api' ) ) : ?>
						<select name="service">
							<option value=""><?php esc_html_e( 'All Service', 'pok' ); ?></option>
							<?php
							$services = $this->core->get_courier_service();
							if ( 'all' === $this->courier ) {
								foreach ( $services as $courier => $courier_services ) {
									foreach ( $courier_services as $key => $label ) {
										?>
										<option <?php echo isset( $_GET['service'] ) && sanitize_text_field( wp_unslash( $_GET['service'] ) ) === ( $courier . '_' . $key ) ? 'selected' : ''; // WPCS: Input var okay, CSRF okay. ?> value="<?php echo esc_attr( $courier . '_' . $key ); ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ) . ' - ' . esc_html( $label['name'] ); ?></option>
										<?php
									}
								}
							} else {
								foreach ( $services[ $this->courier ] as $key => $label ) {
									?>
									<option <?php echo isset( $_GET['service'] ) && sanitize_text_field( wp_unslash( $_GET['service'] ) ) === $key ? 'selected' : ''; // WPCS: Input var okay, CSRF okay. ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label['name'] ); ?></option>
									<?php
								}
							}
							?>
						</select>
					<?php endif; ?>

					<input type="hidden" name="page" value="wc-reports">
					<input type="hidden" name="tab" value="pok_shipping">
					<input type="hidden" name="report" value="<?php echo isset( $_GET['report'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['report'] ) ) ) : 'all'; // WPCS: Input var okay, CSRF okay. ?>">
					<?php if ( isset( $_GET['range'] ) ) : // WPCS: Input var okay, CSRF okay. ?>
						<input type="hidden" name="range" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['range'] ) ) ); // WPCS: Input var okay, CSRF okay. ?>">
					<?php endif; ?>
					<?php if ( isset( $_GET['start_date'] ) ) : // WPCS: Input var okay, CSRF okay. ?>
						<input type="hidden" name="start_date" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) ); // WPCS: Input var okay, CSRF okay. ?>">
					<?php endif; ?>
					<?php if ( isset( $_GET['end_date'] ) ) : // WPCS: Input var okay, CSRF okay. ?>
						<input type="hidden" name="end_date" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) ); // WPCS: Input var okay, CSRF okay. ?>">
					<?php endif; ?>
					<input class="button" type="submit" value="<?php esc_html_e( 'Get Report', 'pok' ); ?>">
				</div>
			</form>
			<?php
		}
	}

	/**
	 * Prepare list items.
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$current_page          = absint( $this->get_pagenum() );
		$per_page              = apply_filters( 'pok_report_per_page', 20 );

		$this->get_items( $current_page, $per_page );

		/**
		 * Pagination.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $this->max_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $this->max_items / $per_page ),
			)
		);
	}

	/**
	 * Get Products matching stock criteria.
	 *
	 * @param int $current_page Current page.
	 * @param int $per_page     Per page.
	 */
	public function get_items( $current_page, $per_page ) {
		global $wpdb;

		$this->max_items = 0;
		$this->items     = array();

		$query_select = apply_filters(
			'pok_report_query_select', 'SELECT orders.ID AS order_id, 
			orders.post_status AS order_status, 
			orders.post_date AS order_date, 
			courier.meta_value AS courier, 
			service.meta_value AS service, 
			weight.meta_value AS weight, 
			cost.meta_value AS cost, 
			insurance.meta_value AS insurance, 
			timber_packing.meta_value AS timber_packing 
		'
		);
		$query_from = apply_filters(
			'pok_report_query_from', "FROM {$wpdb->posts} as orders
		"
		);
		$query_join = apply_filters(
			'pok_report_query_join', "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS shipping ON ( orders.ID = shipping.order_id AND shipping.order_item_type = 'shipping' ) 
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_by_pok ON ( is_by_pok.order_item_id = shipping.order_item_id AND is_by_pok.meta_key = 'created_by_pok' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS cost ON ( cost.order_item_id = shipping.order_item_id AND cost.meta_key = 'cost' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS courier ON ( courier.order_item_id = shipping.order_item_id AND courier.meta_key = 'courier' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS service ON ( service.order_item_id = shipping.order_item_id AND service.meta_key = 'service' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS weight ON ( weight.order_item_id = shipping.order_item_id AND weight.meta_key = 'weight' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS timber_packing ON ( timber_packing.order_item_id = shipping.order_item_id AND timber_packing.meta_key = 'timber_packing' ) 
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS insurance ON ( insurance.order_item_id = shipping.order_item_id AND insurance.meta_key = 'insurance' ) 
		"
		);
		$query_where  = 'WHERE 1=1 ';
		$query_where .= "AND orders.post_type IN ( 'shop_order' ) ";
		$query_where .= 'AND courier.meta_value LIKE %s ';
		$query_where .= 'AND orders.post_date >= %s ';
		$query_where .= 'AND orders.post_date <= %s ';

		// filter by status.
		if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) { // WPCS: Input var okay, CSRF okay.
			$query_where .= "AND orders.post_status = '" . esc_sql( sanitize_text_field( wp_unslash( $_GET['status'] ) ) ) . "' "; // WPCS: Input var okay, CSRF okay.
		}

		// filter by service.
		if ( isset( $_GET['service'] ) && ! empty( $_GET['service'] ) ) { // WPCS: Input var okay, CSRF okay.
			$services = $this->core->get_courier_service();
			if ( 'all' === $this->courier ) {
				$explode = explode( '_', sanitize_text_field( wp_unslash( $_GET['service'] ) ) ); // WPCS: Input var okay, CSRF okay.
				$courier = $explode[0];
				if ( in_array( $courier, $this->core->get_courier( $this->setting->get('base_api'), $this->helper->get_license_type() ), true ) ) {
					$query_where .= "AND courier.meta_value LIKE '%" . esc_sql( $courier ) . "%' ";
					if ( isset( $explode[1] ) && in_array( $explode[1], array_keys( $services[ $courier ] ), true ) ) {
						$query_where .= "AND service.meta_value LIKE '%" . esc_sql( $explode[1] ) . "%' ";
					}
				}
			} else {
				$service = sanitize_text_field( wp_unslash( $_GET['service'] ) ); // WPCS: Input var okay, CSRF okay.
				if ( in_array( $service, array_keys( $services[ $this->courier ] ), true ) ) {
					$query_where .= "AND service.meta_value LIKE '%" . esc_sql( $service ) . "%' ";
				}
			}
		}
		$query_where = apply_filters( 'pok_report_query_where', $query_where );

		$orderby    = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'order_id'; // WPCS: Input var okay, CSRF okay.
		$order      = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc'; // WPCS: Input var okay, CSRF okay.

		$this->items        = $wpdb->get_results( $wpdb->prepare( "{$query_select}{$query_from}{$query_join}{$query_where} ORDER BY {$orderby} {$order} LIMIT %d, %d;", ( 'all' === $this->courier ? '%' : $this->courier ), date( 'Y-m-d', $this->start_date ) . ' 00:00:00', date( 'Y-m-d', $this->end_date ) . ' 23:59:59', ( $current_page - 1 ) * $per_page, $per_page ) );
		$this->max_items    = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT orders.ID ) {$query_from}{$query_join}{$query_where};", ( 'all' === $this->courier ? '%' : $this->courier ), date( 'Y-m-d', $this->start_date ) . ' 00:00:00', date( 'Y-m-d', $this->end_date ) . ' 23:59:59' ) );
		$this->total_cost  = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( cost.meta_value ) {$query_from}{$query_join}{$query_where};", ( 'all' === $this->courier ? '%' : $this->courier ), date( 'Y-m-d', $this->start_date ) . ' 00:00:00', date( 'Y-m-d', $this->end_date ) . ' 23:59:59' ) );
		$this->total_weight = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( weight.meta_value ) {$query_from}{$query_join}{$query_where};", ( 'all' === $this->courier ? '%' : $this->courier ), date( 'Y-m-d', $this->start_date ) . ' 00:00:00', date( 'Y-m-d', $this->end_date ) . ' 23:59:59' ) );
	}


	/**
	 * Get the current range and calculate the start and end dates.
	 *
	 * @param  string $current_range Current range.
	 */
	public function calculate_current_range( $current_range ) {

		switch ( $current_range ) {

			case 'custom':
				$this->start_date = isset( $_GET['start_date'] ) ? max( strtotime( '-20 years' ), strtotime( sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) ) ) : strtotime( '-1 year' ); // WPCS: Input var okay, CSRF okay.

				if ( empty( $_GET['end_date'] ) ) { // WPCS: Input var okay, CSRF okay.
					$this->end_date = strtotime( 'midnight', current_time( 'timestamp' ) );
				} else {
					$this->end_date = strtotime( 'midnight', strtotime( sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) ) ); // WPCS: Input var okay, CSRF okay.
				}
				break;

			case 'year':
				$this->start_date    = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				break;

			case 'last_month':
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
				break;

			case 'month':
				$this->start_date    = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				break;

			case '7day':
				$this->start_date    = strtotime( '-6 days', strtotime( 'midnight', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				break;

			default:
				$this->start_date    = apply_filters( 'pok_report_start_date_' . $current_range, strtotime( '-6 days', strtotime( 'midnight', current_time( 'timestamp' ) ) ) );
				$this->end_date      = apply_filters( 'pok_report_end_date_' . $current_range, strtotime( 'midnight', current_time( 'timestamp' ) ) );
				break;
		}
	}

}
