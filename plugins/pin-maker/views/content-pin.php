<?php
/**
 * The template for displaying pin content within loops
 *
 * This template can be overridden by copying it to yourtheme/pin-maker/content-pin.php.
 *
 * @version 1.0.0
 */
?>
<div id="pin-<?php echo wpa_pm_pin_id(); ?>" class="pin__wrapper">
	<?php
		/**
		 * wpa_pm_pin_content hook.
		 *
		 * @hooked wpa_pm_pin_attachment - 5
		 * @hooked wpa_pm_pin_icon - 10
		 * @hooked wpa_pm_pin_style - 15
		 */
		do_action( 'wpa_pm_pin_content' );
	?>
</div>