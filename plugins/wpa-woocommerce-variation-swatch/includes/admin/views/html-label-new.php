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
<div class="form-field wpa-label-wrap">
	<label for="wpa-label"><?php esc_html_e( 'Label', 'wcvs' ); ?></label>
	<input name="wpa_label" id="wpa-label" type="text" value="">
</div>
<div class="form-field wpa-tooltip-wrap">
	<label for="wpa-tooltip"><?php esc_html_e( 'Tooltip', 'wcvs' ); ?></label>
	<input name="wpa_tooltip" id="wpa-tooltip" type="text" value="">
</div>