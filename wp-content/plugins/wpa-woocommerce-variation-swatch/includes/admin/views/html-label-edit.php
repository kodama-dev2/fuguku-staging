<?php
/**
 * Description
 *
 * @package WPA_WCVS
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined('ABSPATH' ) ) {
	exit;
}
?>
<tr class="form-field wpa-label-wrap">
	<th scope="row"><label for="wpa-label"><?php esc_html_e( 'Label', 'wcvs' ); ?></label></th>
	<td>
		<input name="wpa_label" id="wpa-label" type="text" value="<?php
			echo esc_attr( get_term_meta( $tag->term_id, 'wpa_label', true ) );
		?>">
	</td>
</tr>
<tr class="form-field wpa-tooltip-wrap">
	<th scope="row"><label for="wpa-tooltip"><?php esc_html_e( 'Tooltip', 'wcvs' ); ?></label></th>
	<td>
		<input name="wpa_tooltip" id="wpa-tooltip" type="text" value="<?php
			echo esc_attr( get_term_meta( $tag->term_id, 'wpa_tooltip', true ) );
		?>">
	</td>
</tr>