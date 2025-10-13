<?php
global $post;

// Get current image.
$attachment_id = get_post_meta( $post->ID, 'wpa_pin_images', true );

if ( $attachment_id ) {
	// Get image source.
	$image_src = wp_get_attachment_url( $attachment_id );
}

// Get general settings.
$settings = get_post_meta( $post->ID, 'wpa_pin_settings', true );

// Get all pins.
$pins = get_post_meta( $post->ID, 'wpa_pin', true );
?>
<script type="text/html" id="wpa_pin_maker_render">
	<div class="pin-maker">
		<div class="pm-setting-bar flex--alone flex--between">
			<div class="pm-shortcode p--r">
				<div class="shortcode__content br--3 bg--w">
					<i class="pin__icon--code"></i>
					<span>[pins id="<?php echo absint( $post->ID ); ?>"]</span>
				</div>
				<span class="shortcode__tooltip p--a"><?php esc_html_e( 'Copy this shortcode and paste it into your post, page, or text widget content', 'pin-maker' ); ?></span>
			</div>
		</div>

		<div class="pm-editor bg--w p--r">
			<input type="hidden" id="wpa_pin_images" name="wpa_pin_images" value="<?php echo absint( $attachment_id ); ?>">

			<?php if ( $attachment_id ) : ?>
				<div class="edit">
					<a class="edit__image p--a" href="#">
						<i class="pin__icon--image"></i>
						<span class="p--a br--3"><?php esc_html_e( 'Change Image', 'pin-maker' ); ?></span>
					</a>

					<div class="edit__wrap">
						<img src="<?php echo esc_url( $image_src ); ?>">
					</div>
				</div>
			<?php else : ?>
				<div class="add flex--center">
					<a href="#" class="add__image d--ib br--50">
						<span class="add__image--jpg d--ib br--5 p--r"></span>
						<span class="add__image--png d--ib br--5 p--r"></span>
						<span class="add__image--gif d--ib br--5 p--r"></span>
					</a>
					<span class="add__image--message d--b mt--10"><?php esc_html_e( 'Drop your images here', 'pin-maker' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</script>

<script type="text/html" id="wpa_pin_maker_render_image_tmpl">
	<div class="edit">
		<a class="edit__image p--a" href="#">
			<i class="pin__icon--image"></i>
			<span class="p--a br--3"><?php esc_html_e( 'Change Image', 'pin-maker' ); ?></span>
		</a>

		<div class="edit__wrap">
			<img src="%URL%">
		</div>
	</div>
</script>

<script type="text/html" id="wpa_pin_maker_render_editor">
	<div class="pin__action">
		<i class="pin__action--edit pin__icon--add flex--alone flex--middle flex--center"></i>
		<div class="pin__action--edit pin__area flex--alone flex--middle flex--center hide"></div>
		<img class="pin__action--edit pin__image hide" src="<?php echo WPA_PM()->plugin_url() . '/assets/img/placeholder.png' ;?>">
		<a class="pin-action--delete p--a" href="#"><i class="pin__icon--bin"></i></a>
		<a class="pin-action--clone p--a" href="#"><i class="pin__icon--clone"></i></a>
	</div>

	<div class="pin__settings box br--5 mt--5 bg--w p--a">
		<div class="box__bar flex--alone flex--between">
			<i class="box__bar--close br--50 p--r"></i>
		</div>

		<ul class="box__nav flex--alone flex--center mg--0">
			<li data-nav="pins" class="box__nav--item box__nav--active"><?php esc_html_e( 'Pin Style', 'pin-maker' ); ?></li>
			<li data-nav="popup" class="box__nav--item"><?php esc_html_e( 'Pin Type', 'pin-maker' ); ?></li>
			<li data-nav="style" class="box__nav--item"><?php esc_html_e( 'Popup Style', 'pin-maker' ); ?></li>
		</ul>

		<div class="box__content">
			<div data-tab="pins" class="box__content--item">
				<div class="flex--alone mb--30">
					<div class="boolean p--r">
						<input type="radio" class="p--a" data-option="settings[pin-type]" value="pin-icon" checked="checked">
						<label class="p--r"><?php esc_html_e( 'Icon', 'pin-maker' ); ?></label>
					</div>
					<div class="boolean p--r ml--20">
						<input type="radio" class="p--a" data-option="settings[pin-type]" value="pin-area">
						<label class="p--r"><?php esc_html_e( 'Area', 'pin-maker' ); ?></label>
					</div>
					<div class="boolean p--r ml--20">
						<input type="radio" class="p--a" data-option="settings[pin-type]" value="pin-image">
						<label class="p--r"><?php esc_html_e( 'Image', 'pin-maker' ); ?></label>
					</div>
				</div>

				<div data-pin-type="pin-icon">
					<div class="flex mb--20">
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Size', 'pin-maker' ); ?></label>
							<select data-option="settings[icon-size]" class="w--100">
								<option value="pin__size--small"><?php esc_html_e( 'Small', 'pin-maker' ); ?></option>
								<option value="pin__size--medium" selected><?php esc_html_e( 'Medium', 'pin-maker' ); ?></option>
								<option value="pin__size--large"><?php esc_html_e( 'Large', 'pin-maker' ); ?></option>
							</select>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Width', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[icon-border-width]" class="input--large" value="0">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Radius', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[icon-border-radius]" class="input--large" value="50">
							</div>
						</div>
					</div>
					<div class="flex mb--20">
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-color]" class="color-picker" data-default-color="#fff" value="#fff">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-border-color]" class="color-picker" data-default-color="rgba(255, 255, 255, .8)" value="rgba(255, 255, 255, .8)">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Background', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-bg-color]" class="color-picker" data-default-color="#65affa" value="#65affa">
							</div>
						</div>
					</div>
					<div class="flex mb--20">
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Color Hover', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-hover-color]" class="color-picker" data-default-color="#fff" value="#fff">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Hover Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-border-hover-color]" class="color-picker" data-default-color="rgba(255, 255, 255, .95)" value="rgba(255, 255, 255, .95)">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Background Hover', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[icon-bg-hover-color]" class="color-picker" data-default-color="#3881ca" value="#3881ca">
							</div>
						</div>
					</div>
				</div>

				<div data-pin-type="pin-area">
					<div class="flex mb--20">
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Custom Text', 'pin-maker' ); ?></label>
							<input type="text" data-option="settings[area-text]" class="w--100" value="">
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Font Size', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[area-text-size]" class="input--large" value="13">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Text Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-text-color]" class="color-picker" data-default-color="#2091c9" value="#2091c9">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Text Hover Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-text-hover-color]" class="color-picker" data-default-color="#fff" value="#fff">
							</div>
						</div>
					</div>
					<div class="flex mb--20">
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Width', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[area-width]" class="input--large" value="32">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Height', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[area-height]" class="input--large" value="32">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Width', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[area-border-width]" class="input--large" value="0">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Radius', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[area-border-radius]" class="input--large" value="50">
							</div>
						</div>
					</div>
					<div class="flex">
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Background', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-bg-color]" class="color-picker" data-default-color="rgba(101, 175, 250, .5)" value="rgba(101, 175, 250, .5)">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Background Hover', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-bg-hover-color]" class="color-picker" data-default-color="rgba(101, 175, 250, .5)" value="rgba(101, 175, 250, .5)">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-border-color]" class="color-picker" data-default-color="rgba(0, 0, 0, .5)" value="rgba(0, 0, 0, .5)">
							</div>
						</div>
						<div class="flex--3">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Hover Color', 'pin-maker' ); ?></label>
							<div class="pm-color-picker p--r">
								<input type="text" data-option="settings[area-border-hover-color]" class="color-picker" data-default-color="rgba(0, 0, 0, .5)" value="rgba(0, 0, 0, .5)">
							</div>
						</div>
					</div>
				</div>

				<div data-pin-type="pin-image">
					<div class="mb--20">
						<label class="d--b mb--5"><?php esc_html_e( 'Upload Image', 'pin-maker' ); ?></label>
						<div class="p--r">
							<input type="text" class="w--100 pr--50" data-option="settings[image-file]" value="">
							<a href="javascript:void(0);" class="p--a upload__image">...</a>
						</div>
					</div>
					<div class="flex">
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Width', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[image-width]" class="input--large" value="32">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Height', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[image-height]" class="input--large" value="32">
							</div>
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Border Radius', 'pin-maker' ); ?></label>
							<div class="input input--number p--r">
								<input type="number" data-option="settings[image-border-radius]" class="input--large" value="50">
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div data-tab="popup" class="box__content--item hide">
				<div class="flex--alone mb--30">
					<div class="boolean p--r">
						<input type="radio" class="p--a" data-option="settings[popup-type]" value="text" checked="checked">
						<label class="p--r"><?php esc_html_e( 'Text', 'pin-maker' ); ?></label>
					</div>
					<div class="boolean p--r ml--20">
						<input type="radio" class="p--a" data-option="settings[popup-type]" value="image">
						<label class="p--r"><?php esc_html_e( 'Image', 'pin-maker' ); ?></label>
					</div>
					<div class="boolean p--r ml--20">
						<input type="radio" class="p--a" data-option="settings[popup-type]" value="link">
						<label class="p--r"><?php esc_html_e( 'Link', 'pin-maker' ); ?></label>
					</div>
					<div class="boolean p--r ml--20">
						<input type="radio" class="p--a" data-option="settings[popup-type]" value="video">
						<label class="p--r"><?php esc_html_e( 'Youtube', 'pin-maker' ); ?></label>
					</div>
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<div class="boolean p--r ml--20">
							<input type="radio" class="p--a" data-option="settings[popup-type]" value="woocommerce">
							<label class="p--r"><?php esc_html_e( 'WooCommerce', 'pin-maker' ); ?></label>
						</div>
					<?php endif; ?>
				</div>

				<div data-popup-type="image|text|video|link" class="mb--20">
					<label class="d--b mb--5"><?php esc_html_e( 'Title', 'pin-maker' ); ?></label>
					<input type="text" data-option="settings[popup-title]" class="w--100" value="">
				</div>

				<div data-popup-type="text">
					<label class="d--b mb--5"><?php esc_html_e( 'Content', 'pin-maker' ); ?></label>
					<textarea class="w--100" data-option="settings[text]" rows="6"></textarea>
				</div>

				<div data-popup-type="image" class="mb--20">
					<label class="d--b mb--5"><?php esc_html_e( 'Upload Image', 'pin-maker' ); ?></label>
					<div class="p--r mb--20">
						<input type="text" class="w--100 pr--50" data-option="settings[image]" value="">
						<a href="javascript:void(0);" class="p--a upload__image">...</a>
					</div>
					<div class="flex">
						<div class="flex--8">
							<label class="d--b mb--5"><?php esc_html_e( 'Link To', 'pin-maker' ); ?></label>
							<input type="text" data-option="settings[image-link-to]" class="w--100" value="">
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Target', 'pin-maker' ); ?></label>
							<select data-option="settings[image-link-target]" class="w--100">
								<option value="_self"><?php esc_html_e( 'Default', 'pin-maker' ); ?></option>
								<option value="_blank"><?php esc_html_e( 'New Window', 'pin-maker' ); ?></option>
							</select>
						</div>
					</div>
				</div>

				<div data-popup-type="link">
					<div class="flex">
						<div class="flex--8">
							<label class="d--b mb--5"><?php esc_html_e( 'Link To', 'pin-maker' ); ?></label>
							<input type="text" data-option="settings[link-link]" class="w--100" value="">
						</div>
						<div class="flex--4">
							<label class="d--b mb--5"><?php esc_html_e( 'Target', 'pin-maker' ); ?></label>
							<select data-option="settings[link-link-target]" class="w--100">
								<option value="_self"><?php esc_html_e( 'Default', 'pin-maker' ); ?></option>
								<option value="_blank"><?php esc_html_e( 'New Window', 'pin-maker' ); ?></option>
							</select>
						</div>
					</div>
				</div>

				<div data-popup-type="video">
					<div class="mb--20">
						<label class="d--b mb--5"><?php esc_html_e( 'Link', 'pin-maker' ); ?></label>
						<input type="text" data-option="settings[video-link]" class="w--100" value="">
					</div>
					<div class="flex--alone">
						<div class="boolean p--r">
							<input type="hidden" data-option="settings[video-autoplay]" value="0">
							<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
							<label class="p--r"><?php esc_html_e( 'Autoplay', 'pin-maker' ); ?></label>
						</div>
						<div class="boolean p--r ml--20">
							<input type="hidden" data-option="settings[video-control]" value="0">
							<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
							<label class="p--r"><?php esc_html_e( 'Enable Control', 'pin-maker' ); ?></label>
						</div>
					</div>
				</div>
				
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<div data-popup-type="woocommerce">
						<div class="mb--20">
							<label class="d--b mb--5"><?php esc_html_e( 'Select product', 'pin-maker' ); ?></label>
							<input type="text" data-option="settings[product]" class="w--100 product__selector" value="">
						</div>
						<div class="flex--alone mb--20">
							<div class="boolean p--r">
								<input type="hidden" data-option="settings[product-thumbnail]" value="1">
								<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
								<label class="p--r"><?php esc_html_e( 'Enable Thumbnail', 'pin-maker' ); ?></label>
							</div>
							<div class="boolean p--r ml--20">
								<input type="hidden" data-option="settings[product-title]" value="1">
								<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
								<label class="p--r"><?php esc_html_e( 'Enable Title', 'pin-maker' ); ?></label>
							</div>
							<div class="boolean p--r ml--20">
								<input type="hidden" data-option="settings[product-price]" value="1">
								<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
								<label class="p--r"><?php esc_html_e( 'Enable Price', 'pin-maker' ); ?></label>
							</div>
							<div class="boolean p--r ml--20">
								<input type="hidden" data-option="settings[product-button]" value="1">
								<input type="checkbox" class="p--a" onchange="jQuery(this).prev().val(this.checked ? 1 : 0);" checked="checked">
								<label class="p--r"><?php esc_html_e( 'Enable Button', 'pin-maker' ); ?></label>
							</div>
						</div>
						<div class="flex--12">
							<label class="d--b mb--5"><?php esc_html_e( 'Target', 'pin-maker' ); ?></label>
							<select data-option="settings[product-link-target]" class="w--100">
								<option value="_self"><?php esc_html_e( 'Default', 'pin-maker' ); ?></option>
								<option value="_blank"><?php esc_html_e( 'New Window', 'pin-maker' ); ?></option>
							</select>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div data-tab="style" class="box__content--item hide">
				<div class="flex mb--20">
					<div class="flex--3">
						<label class="d--b mb--5"><?php esc_html_e( 'Width', 'pin-maker' ); ?></label>
						<div class="input input--number p--r">
							<input type="number" data-option="settings[popup-width]" class="input--large" value="260">
						</div>
					</div>
					<div class="flex--3">
						<label class="d--b mb--5"><?php esc_html_e( 'Position', 'pin-maker' ); ?></label>
						<select data-option="settings[popup-position]" class="w--100">
							<option value="top"><?php esc_html_e( 'Top', 'pin-maker' ); ?></option>
							<option value="right"><?php esc_html_e( 'Right', 'pin-maker' ); ?></option>
							<option value="bottom"><?php esc_html_e( 'Bottom', 'pin-maker' ); ?></option>
							<option value="left"><?php esc_html_e( 'Left', 'pin-maker' ); ?></option>
						</select>
					</div>
					<div class="flex--3">
						<label class="d--b mb--5"><?php esc_html_e( 'Animation', 'pin-maker' ); ?></label>
						<select data-option="settings[popup-anm]" class="w--100">
							<option value="fade"><?php esc_html_e( 'Fade', 'pin-maker' ); ?></option>
							<option value="zoom"><?php esc_html_e( 'Zoom', 'pin-maker' ); ?></option>
							<option value="sup"><?php esc_html_e( 'Slide Up', 'pin-maker' ); ?></option>
							<option value="sdown"><?php esc_html_e( 'Slide Down', 'pin-maker' ); ?></option>
							<option value="sleft"><?php esc_html_e( 'Slide Left', 'pin-maker' ); ?></option>
							<option value="sright"><?php esc_html_e( 'Slide Right', 'pin-maker' ); ?></option>
							<option value="rotate"><?php esc_html_e( 'Rotate', 'pin-maker' ); ?></option>
						</select>
					</div>
					<div class="flex--3">
						<label class="d--b mb--5"><?php esc_html_e( 'Border Radius', 'pin-maker' ); ?></label>
						<div class="input input--number p--r">
							<input type="number" data-option="settings[popup-border-radius]" class="input--large" value="0">
						</div>
					</div>
				</div>
				<div class="flex">
					<div class="flex--4">
						<label class="d--b mb--5"><?php esc_html_e( 'Title Color', 'pin-maker' ); ?></label>
						<div class="pm-color-picker p--r">
							<input type="text" data-option="settings[popup-title-color]" class="color-picker" data-default-color="#2b2b2b" value="#2b2b2b">
						</div>
					</div>
					<div class="flex--4">
						<label class="d--b mb--5"><?php esc_html_e( 'Content Color', 'pin-maker' ); ?></label>
						<div class="pm-color-picker p--r">
							<input type="text" data-option="settings[popup-content-color]" class="color-picker" data-default-color="#878787" value="#878787">
						</div>
					</div>
					<div class="flex--4">
						<label class="d--b mb--5"><?php esc_html_e( 'Background', 'pin-maker' ); ?></label>
						<div class="pm-color-picker p--r">
							<input type="text" data-option="settings[popup-bg-color]" class="color-picker" data-default-color="#fff" value="#fff">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" data-option="top" value="<%= top %>">
	<input type="hidden" data-option="left" value="<%= left %>">
	<input type="hidden" data-option="settings[id]" value="">
</script>

<script type="text/javascript">
	jQuery(function($) {
		$(window).load(function() {
			// Override default UI.
			var form = $( $( '#wpa_pin_maker_render' ).text() ).prepend( $( '#post-body-content' ).children( 'input[type="hidden"]' ) );

			$('#screen-meta, #screen-meta-links, #post-status-info').remove();

			$('#wp-content-wrap').replaceWith(form);

			// Trigger event to initialize application.
			setTimeout( function() {
				$( document ).trigger( 'wpa_pin_maker_init' );
			}, 500 );

			// Pass data to client-side.
			window.wpa_pin_settings = <?php echo json_encode( $settings ? $settings : new stdClass() ); ?>;
			window.wpa_pin = <?php echo json_encode( $pins ? array_values( $pins ) : array() ); ?>;
		});
	});
</script>
