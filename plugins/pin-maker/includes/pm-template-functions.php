<?php
/**
 * PinMaker Template functions
 * Functions for the templating system.
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'wpa_pm_pin_attachment' ) ) {
	/**
	 * Get the product thumbnail for the loop.
	 *
	 * @since 1.0.0
	 */
	function wpa_pm_pin_attachment() {
		// Get attachment image.
		$attachment_id = get_post_meta( wpa_pm_pin_id(), 'wpa_pin_images', true );

		if ( $attachment_id ) {
			$props = function_exists( 'wc_get_product_attachment_props' ) ? wc_get_product_attachment_props( $attachment_id ) : '';

			echo apply_filters(
				'wpa_pin_image_attachment_html',
				sprintf(
					'<div class="pin__image">%s</div>', wp_get_attachment_image( $attachment_id, apply_filters( 'wpa_pin_image_size', 'full' ), false, '' )
				)
			);
		}
	}
}

if ( ! function_exists( 'wpa_pm_pin_icon' ) ) {
	/**
	 * Output the pin.
	 *
	 * @since 1.0.0
	 */
	function wpa_pm_pin_icon() {
		$html = '';

		// Get all pins.
		$pins = get_post_meta( wpa_pm_pin_id(), 'wpa_pin', true );

		if ( $pins ) {
			foreach ( $pins as $pin ) {
				$setting    = $pin['settings'];
				$pin_type   = $setting['pin-type'];
				$popup_type = $setting['popup-type'];
				$setting['product-link-target'] = !empty($setting['product-link-target']) ? $setting['product-link-target'] : '';


				switch ( $pin_type ) {
					case 'pin-icon':
						$html .= '<div class="pin__type pin__type--icon pin__item--' . esc_attr( $pin['settings']['id'] ) . '">';
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '<a target="' . esc_attr( $setting['link-link-target'] ) . '" href="' . esc_url( $setting['link-link'] ) . '">';
							}
								$html .= '<i class="pin__icon--add ' . esc_attr( $setting['icon-size'] ) . '"></i>';
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '</a>';
							}
							if ( $setting['popup-title'] && $popup_type != 'woocommerce' ) {
								$html .= '<div class="pin__title">' . esc_html( $setting['popup-title'] ) . '</div>';
							}

							if ( class_exists( 'WooCommerce' ) && $popup_type == 'woocommerce' ) {
								$html .= '<div class="pin__title">' . get_the_title( $setting['product'] ) . '</div>';
							}

							if ( $popup_type != 'link' ) {
								$html .= '<div class="pin__popup pin__popup--' . esc_attr( $setting['popup-position'] ) . ' pin__popup--' . esc_attr( $setting['popup-anm'] ) . '">';
									if ( $setting['popup-title'] && $popup_type != 'link' && $popup_type != 'woocommerce' ) {
										$html .= '<div class="popup__title">' . esc_html( $setting['popup-title'] ) . '</div>';
									}

									switch ( $popup_type ) {
										case 'text':
											if ( $setting['text'] ) {
												$html .= '<div class="popup__content">' . do_shortcode( $setting['text'] ) . '</div>';
											}
											break;
										
										case 'image':
											if ( $setting['image'] ) {
												$html .= '<div class="popup__content">';
													if ( $setting['image-link-to'] ) {
														$html .= '<a target="' . esc_attr( $setting['image-link-target'] ) . '" href="' . esc_url( $setting['image-link-to'] ) . '">';
													}
														$html .= '<img src="' . esc_url( $setting['image'] ) . '" width="" height="" alt="' . esc_attr( $setting['popup-title'] ) . '" />';
													if ( $setting['image-link-to'] ) {
														$html .= '</a>';
													}
												$html .= '</div>';
											}
											break;

										case 'video':
											if ( $setting['video-link'] ) {
												$link = explode( 'v=', $setting['video-link'] );
												if ( isset( $link[1] ) ) {
													$link = explode( '&', $link[1] );

													$id = $link[0];

													if ( ! $id ) {
														$link = explode( '/', $link );
														$id = $link[ count( $link ) - 1 ];
													}
												}
												if ( $id ) {
													$html .= '<div class="popup__content">';
														$html .= '<iframe src="https://www.youtube.com/embed/' . $id . '?rel=0' . ( $setting['video-control'] ? '&controls=1' : '&controls=0' ) . ( $setting['video-autoplay'] ? '&autoplay=1' : '' ) . '" frameborder="0"></iframe>';
													$html .= '</div>';
												}
											}
											break;

										case 'woocommerce':
											if ( class_exists( 'WooCommerce' ) && $setting['product'] ) {
												$p_id = $setting['product'];
												$pin_product = wc_get_product( $p_id );

												if ( $pin_product ) {
													$html .= '<div class="popup__content popup__content--product">';
														if ( $setting['product-thumbnail'] ) {
															$html .= get_the_post_thumbnail( $p_id, 'shop_catalog' );
														}

														if ( $setting['product-title'] ) {
															$html .= '<h3 class="pin-product-title">' . get_the_title( $p_id ) . '</h3>';
														}

														if ( $pin_product->get_price_html() && $setting['product-price'] ) {
															$html .= '<span>' . $pin_product->get_price_html() . '</span>';
														}

														if ( $setting['product-button'] ) {
															$html .= '<div class="pin-product-button">';
																$html .= '<a href="' . get_permalink( $p_id ) . '" target="' . esc_attr( $setting['product-link-target'] ) . '">' . esc_html__( 'Detail', 'pin-maker' ) . '</a>';
																if ( $pin_product->is_type( 'simple' ) ) {
																	$html .= '<a href="' . esc_url( $pin_product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $p_id ) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
																} else {
																	$html .= '<a href="' . esc_url( get_permalink( $p_id ) ) . '" target="' . esc_attr( $setting['product-link-target'] ) . '">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
																}
															$html .= '</div>';
														}
													$html .= '</div>';
												}
											}
											break;
									}
								$html .= '</div>';
							}
						$html .= '</div>';
						break;
					
					case 'pin-area':
						$html .= '<div class="pin__type pin__type--area pin__item--' . esc_attr( $pin['settings']['id'] ) . '">';
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '<a target="' . esc_attr( $setting['link-link-target'] ) . '" href="' . esc_url( $setting['link-link'] ) . '">';
							}
								if ( $setting['area-text'] ) {
									$html .= '<span>' . esc_html( $setting['area-text'] ) . '</span>';
								}
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '</a>';
							}
							if ( $setting['popup-title'] && $popup_type != 'woocommerce' ) {
								$html .= '<div class="pin__title">' . esc_html( $setting['popup-title'] ) . '</div>';
							}

							if ( class_exists( 'WooCommerce' ) && $popup_type == 'woocommerce' ) {
								$html .= '<div class="pin__title">' . get_the_title( $setting['product'] ) . '</div>';
							}

							$html .= '<div class="pin__popup pin__popup--' . esc_attr( $setting['popup-position'] ) . ' pin__popup--' . esc_attr( $setting['popup-anm'] ) . '">';
								if ( $setting['popup-title'] && $popup_type != 'link' && $popup_type != 'woocommerce' ) {
									$html .= '<div class="popup__title">' . esc_html( $setting['popup-title'] ) . '</div>';
								}

								switch ( $popup_type ) {
									case 'text':
										if ( $setting['text'] ) {
											$html .= '<div class="popup__content">' . do_shortcode( $setting['text'] ) . '</div>';
										}
										break;
									
									case 'image':
										if ( $setting['image'] ) {
											$html .= '<div class="popup__content">';
												if ( $setting['image-link-to'] ) {
													$html .= '<a target="' . esc_attr( $setting['image-link-target'] ) . '" href="' . esc_url( $setting['image-link-to'] ) . '">';
												}
													$html .= '<img src="' . esc_url( $setting['image'] ) . '" width="" height="" alt="' . esc_attr( $setting['popup-title'] ) . '" />';
												if ( $setting['image-link-to'] ) {
													$html .= '</a>';
												}
											$html .= '</div>';
										}
										break;

									case 'video':
										if ( $setting['video-link'] ) {
											$link = explode( 'v=', $setting['video-link'] );
											if ( isset( $link[1] ) ) {
												$link = explode( '&', $link[1] );

												$id = $link[0];

												if ( ! $id ) {
													$link = explode( '/', $link );
													$id = $link[ count( $link ) - 1 ];
												}
											}
											if ( $id ) {
												$html .= '<div class="popup__content">';
													$html .= '<iframe src="https://www.youtube.com/embed/' . $id . '?rel=0' . ( $setting['video-control'] ? '&controls=1' : '&controls=0' ) . ( $setting['video-autoplay'] ? '&autoplay=1' : '' ) . '" frameborder="0"></iframe>';
												$html .= '</div>';
											}
										}
										break;

									case 'woocommerce':
										if ( class_exists( 'WooCommerce' ) && $setting['product'] ) {
											$p_id = $setting['product'];
											$pin_product = wc_get_product( $p_id );

											if ( $pin_product ) {
												$html .= '<div class="popup__content popup__content--product">';
													if ( $setting['product-thumbnail'] ) {
														$html .= get_the_post_thumbnail( $p_id, 'shop_catalog' );
													}

													if ( $setting['product-title'] ) {
														$html .= '<h3 class="pin-product-title">' . get_the_title( $p_id ) . '</h3>';
													}

													if ( $pin_product->get_price_html() && $setting['product-price'] ) {
														$html .= '<span>' . $pin_product->get_price_html() . '</span>';
													}

													if ( $setting['product-button'] ) {
														$html .= '<div  class="pin-product-button">';
															$html .= '<a href="' . get_permalink( $p_id ) . '" target="' . esc_attr( $setting['product-link-target'] ) . '">' . esc_html__( 'Detail', 'pin-maker' ) . '</a>';
															if ( $pin_product->is_type( 'simple' ) ) {
																$html .= '<a href="' . esc_url( $pin_product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $p_id ) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
															} else {
																$html .= '<a href="' . esc_url( get_permalink( $p_id ) ) . '" target="' . esc_attr( $setting['product-link-target'] ) . '">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
															}
														$html .= '</div>';
													}
												$html .= '</div>';
											}
										}
										break;
								}
							$html .= '</div>';
						$html .= '</div>';
						break;

					case 'pin-image':
						$html .= '<div class="pin__type pin__type--image pin__item--' . esc_attr( $pin['settings']['id'] ) . '">';
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '<a target="' . esc_attr( $setting['link-link-target'] ) . '" href="' . esc_url( $setting['link-link'] ) . '">';
							}
								if ( $setting['image-file'] ) {
									$html .= '<img src="' . esc_url( $setting['image-file'] ) . '" width="' . esc_attr( $setting['image-width'] ) . '" height="' . esc_attr( $setting['image-height'] ) . '" alt="Pin Image" />';
								}
							if ( $popup_type == 'link' && $setting['link-link'] ) {
								$html .= '</a>';
							}
							if ( $setting['popup-title'] && $popup_type != 'woocommerce' ) {
								$html .= '<div class="pin__title">' . esc_html( $setting['popup-title'] ) . '</div>';
							}

							if ( class_exists( 'WooCommerce' ) && $popup_type == 'woocommerce' ) {
								$html .= '<div class="pin__title">' . get_the_title( $setting['product'] ) . '</div>';
							}

							$html .= '<div class="pin__popup pin__popup--' . esc_attr( $setting['popup-position'] ) . ' pin__popup--' . esc_attr( $setting['popup-anm'] ) . '">';
								if ( $setting['popup-title'] && $popup_type != 'link' && $popup_type != 'woocommerce' ) {
									$html .= '<div class="popup__title">' . esc_html( $setting['popup-title'] ) . '</div>';
								}

								switch ( $popup_type ) {
									case 'text':
										if ( $setting['text'] ) {
											$html .= '<div class="popup__content">' . do_shortcode( $setting['text'] ) . '</div>';
										}
										break;
									
									case 'image':
										if ( $setting['image'] ) {
											$html .= '<div class="popup__content">';
												if ( $setting['image-link-to'] ) {
													$html .= '<a target="' . esc_attr( $setting['image-link-target'] ) . '" href="' . esc_url( $setting['image-link-to'] ) . '">';
												}
													$html .= '<img src="' . esc_url( $setting['image'] ) . '" width="" height="" alt="' . esc_attr( $setting['popup-title'] ) . '" />';
												if ( $setting['image-link-to'] ) {
													$html .= '</a>';
												}
											$html .= '</div>';
										}
										break;

									case 'video':
										if ( $setting['video-link'] ) {
											$link = explode( 'v=', $setting['video-link'] );
											if ( isset( $link[1] ) ) {
												$link = explode( '&', $link[1] );

												$id = $link[0];

												if ( ! $id ) {
													$link = explode( '/', $link );
													$id = $link[ count( $link ) - 1 ];
												}
											}
											if ( $id ) {
												$html .= '<div class="popup__content">';
													$html .= '<iframe src="https://www.youtube.com/embed/' . $id . '?rel=0' . ( $setting['video-control'] ? '&controls=1' : '&controls=0' ) . ( $setting['video-autoplay'] ? '&autoplay=1' : '' ) . '" frameborder="0"></iframe>';
												$html .= '</div>';
											}
										}
										break;

									case 'woocommerce':
										if ( class_exists( 'WooCommerce' ) && $setting['product'] ) {
											$p_id = $setting['product'];
											$pin_product = wc_get_product( $p_id );

											if ( $pin_product ) {
												$html .= '<div class="popup__content popup__content--product">';
													if ( $setting['product-thumbnail'] ) {
														$html .= get_the_post_thumbnail( $p_id, 'shop_catalog' );
													}

													if ( $setting['product-title'] ) {
														$html .= '<h3>' . get_the_title( $p_id ) . '</h3>';
													}

													if ( $pin_product->get_price_html() && $setting['product-price'] ) {
														$html .= '<span>' . $pin_product->get_price_html() . '</span>';
													}

													if ( $setting['product-button'] ) {
														$html .= '<div>';
															$html .= '<a href="' . get_permalink( $p_id ) . '">' . esc_html__( 'Detail', 'pin-maker' ) . '</a>';
															if ( $pin_product->is_type( 'simple' ) ) {
																$html .= '<a href="' . esc_url( $pin_product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $p_id ) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
															} else {
																$html .= '<a href="' . esc_url( get_permalink( $p_id ) ) . '">' . esc_html__( 'Buy Now', 'pin-maker' ) . '</a>';
															}
														$html .= '</div>';
													}
												$html .= '</div>';
											}
										}
										break;
								}
							$html .= '</div>';
						$html .= '</div>';
						break;
				}
			}
		}
		echo apply_filters( 'wpa_pm_pin_icon', $html );
	}
}

if ( ! function_exists( 'wpa_pm_pin_style' ) ) {
	/**
	 * Output the style for pin.
	 *
	 * @since 1.0.0
	 */
	function wpa_pm_pin_style() {
		$css = array();

		// Get pin ID
		$id = wpa_pm_pin_id();

		// Get all pins.
		$pins = get_post_meta( wpa_pm_pin_id(), 'wpa_pin', true );

		if ( $pins ) {
			$css[] = '<style type="text/css" scoped>';
				foreach ( $pins as $pin ) {
					$setting    = $pin['settings'];
					$popup_type = $setting['popup-type'];

					// Type icon
					if ( $setting['pin-type'] == 'pin-icon' ) {
						$css[] = '.pin__item--' . $pin['settings']['id'] . ' {';

							if ( $pin['top'] ) {
								$css[] = 'top: ' . esc_attr( $pin['top'] ) . ';';
							}

							if ( $pin['left'] ) {
								$css[] = 'left: ' . esc_attr( $pin['left'] ) . ';';
							}

						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__icon--add {';

							if ( $setting['icon-border-width'] ) {
								$css[] = 'border: ' . esc_attr( $setting['icon-border-width'] ) . 'px solid ' . esc_attr( $setting['icon-border-color'] ) . ';';
							}

							if ( $setting['icon-border-radius'] ) {
								$css[] = 'border-radius: ' . esc_attr( $setting['icon-border-radius'] ) . 'px;';
							}

							if ( $setting['icon-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['icon-color'] ) . ';';
							}

							if ( $setting['icon-bg-color'] ) {
								$css[] = 'background: ' . esc_attr( $setting['icon-bg-color'] ) . ';';
							}

						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__icon--add:hover {';

							if ( $setting['icon-border-width'] && $setting['icon-border-hover-color'] ) {
								$css[] = 'border-color: ' . esc_attr( $setting['icon-border-hover-color'] ) . ';';
							}

							if ( $setting['icon-hover-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['icon-hover-color'] ) . ';';
							}

							if ( $setting['icon-bg-hover-color'] ) {
								$css[] = 'background: ' . esc_attr( $setting['icon-bg-hover-color'] ) . ';';
							}

						$css[] = '}';

						if ( $popup_type == 'video' ) {

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup iframe {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
									$css[] = 'height: ' . esc_attr( $setting['popup-width'] / 1.7777777778 ) . 'px;';
								}

							$css[] = '}';

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 + 15 ) . 'px);';
								}
							$css[] = '}';

						} else {
							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
								}

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 ) . 'px);';
								}
							$css[] = '}';
						}

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__title {';
							if ( $setting['popup-title-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-title-color'] ) . ';';
							}
						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__content {';
							if ( $setting['popup-content-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-content-color'] ) . ';';
							}
						$css[] = '}';
					}

					// Type area
					if ( $setting['pin-type'] == 'pin-area' ) {
						$css[] = '.pin__item--' . $pin['settings']['id'] . ' {';

							if ( $pin['top'] ) {
								$css[] = 'top: ' . esc_attr( $pin['top'] ) . ';';
							}

							if ( $pin['left'] ) {
								$css[] = 'left: ' . esc_attr( $pin['left'] ) . ';';
							}

							if ( $setting['area-text'] ) {
								if ( $setting['area-text-size'] ) {
									$css[] = 'font-size: ' . esc_attr( $setting['area-text-size'] ) . 'px;';
								}

								if ( $setting['area-text-color'] ) {
									$css[] = 'color: ' . esc_attr( $setting['area-text-color'] ) . ';';
								}
							}

							if ( $setting['area-width'] ) {
								$css[] = 'width: ' . esc_attr( $setting['area-width'] ) . 'px;';
							}

							if ( $setting['area-height'] ) {
								$css[] = 'height: ' . esc_attr( $setting['area-height'] ) . 'px;';
							}

							if ( $setting['area-border-width'] ) {
								$css[] = 'border: ' . esc_attr( $setting['area-border-width'] ) . 'px solid ' . esc_attr( $setting['area-border-color'] ) . ';';
							}

							if ( $setting['area-border-radius'] ) {
								$css[] = 'border-radius: ' . esc_attr( $setting['area-border-radius'] ) . 'px;';
							}

							if ( $setting['area-bg-color'] ) {
								$css[] = 'background: ' . esc_attr( $setting['area-bg-color'] ) . ';';
							}

						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ':hover {';

							if ( $setting['area-text'] && $setting['area-text-hover-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['area-text-hover-color'] ) . ';';
							}

							if ( $setting['area-border-width'] && $setting['area-border-hover-color'] ) {
								$css[] = 'border-color: ' . esc_attr( $setting['area-border-hover-color'] ) . ';';
							}

							if ( $setting['area-bg-hover-color'] ) {
								$css[] = 'background: ' . esc_attr( $setting['area-bg-hover-color'] ) . ';';
							}
						$css[] = '}';

						if ( $popup_type == 'video' ) {

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup iframe {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
									$css[] = 'height: ' . esc_attr( $setting['popup-width'] / 1.7777777778 ) . 'px;';
								}

							$css[] = '}';

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 + 15 ) . 'px);';
								}
							$css[] = '}';

						} else {
							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
								}

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 ) . 'px);';
								}
							$css[] = '}';
						}

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__title {';
							if ( $setting['popup-title-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-title-color'] ) . ';';
							}
						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__content {';
							if ( $setting['popup-content-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-content-color'] ) . ';';
							}
						$css[] = '}';
					}

					// Type image
					if ( $setting['pin-type'] == 'pin-image' ) {
						$css[] = '.pin__item--' . $pin['settings']['id'] . ' {';

							if ( $pin['top'] ) {
								$css[] = 'top: ' . esc_attr( $pin['top'] ) . ';';
							}

							if ( $pin['left'] ) {
								$css[] = 'left: ' . esc_attr( $pin['left'] ) . ';';
							}

						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' > img, .pin__item--' . $pin['settings']['id'] . ' > a > img {';

							if ( $setting['image-width'] ) {
								$css[] = 'width: ' . esc_attr( $setting['image-width'] ) . 'px;';
							}

							if ( $setting['image-height'] ) {
								$css[] = 'height: ' . esc_attr( $setting['image-height'] ) . 'px;';
							}

							if ( $setting['image-border-radius'] ) {
								$css[] = 'border-radius: ' . esc_attr( $setting['image-border-radius'] ) . 'px;';
							}

						$css[] = '}';

						if ( $popup_type == 'video' ) {

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup iframe {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
									$css[] = 'height: ' . esc_attr( $setting['popup-width'] / 1.7777777778 ) . 'px;';
								}

							$css[] = '}';

							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 + 15 ) . 'px);';
								}
							$css[] = '}';

						} else {
							$css[] = '.pin__item--' . $pin['settings']['id'] . ' .pin__popup {';

								if ( $setting['popup-width'] ) {
									$css[] = 'width: ' . esc_attr( $setting['popup-width'] ) . 'px;';
								}

								if ( $setting['popup-border-radius'] ) {
									$css[] = 'border-radius: ' . esc_attr( $setting['popup-border-radius'] ) . 'px;';
								}

								if ( $setting['popup-bg-color'] ) {
									$css[] = 'background: ' . esc_attr( $setting['popup-bg-color'] ) . ';';
								}

							$css[] = '}';

							$css[] = '
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--top,
								.pin__item--' . $pin['settings']['id'] . ' .pin__popup--bottom {';
								if ( $setting['popup-width'] ) {
									$css[] = 'left: calc(50% - ' . esc_attr( $setting['popup-width'] / 2 ) . 'px);';
								}
							$css[] = '}';
						}

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__title {';
							if ( $setting['popup-title-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-title-color'] ) . ';';
							}
						$css[] = '}';

						$css[] = '.pin__item--' . $pin['settings']['id'] . ' .popup__content {';
							if ( $setting['popup-content-color'] ) {
								$css[] = 'color: ' . esc_attr( $setting['popup-content-color'] ) . ';';
							}
						$css[] = '}';
					}
				}
			$css[] = '</style>';
		}

		echo preg_replace( '/\n|\t/i', '', implode( '', $css ) );
	}
}