<?php
/**
 * Demo Page Template - Louis Vuitton Style Mixed Grid
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

get_header(); ?>

<div class="jas-container">
    <div class="jas-row">
        <div class="jas-col-md-12">
            
            <!-- Page Header -->
            <div class="gifts-header">
                <h1 class="gifts-title"><?php echo esc_html__('Luxury Gifts Collection', 'claue'); ?></h1>
                <p class="gifts-subtitle"><?php echo esc_html__('Discover our curated collection of premium luxury gifts', 'claue'); ?></p>
            </div>

            <!-- Filter Bar -->
            <div class="gifts-filter-bar">
                <div class="filter-left">
                    <div class="filter-dropdown">
                        <button class="filter-btn">
                            <span><?php echo esc_html__('Categories', 'claue'); ?></span>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <div class="filter-dropdown-content">
                            <a href="#" class="active"><?php echo esc_html__('All Gifts', 'claue'); ?></a>
                            <a href="#"><?php echo esc_html__('Watches', 'claue'); ?></a>
                            <a href="#"><?php echo esc_html__('Handbags', 'claue'); ?></a>
                            <a href="#"><?php echo esc_html__('Jewelry', 'claue'); ?></a>
                            <a href="#"><?php echo esc_html__('Perfumes', 'claue'); ?></a>
                            <a href="#"><?php echo esc_html__('Accessories', 'claue'); ?></a>
                        </div>
                    </div>
                </div>
                
                <div class="filter-right">
                    <button class="filter-btn">
                        <i class="fa fa-filter"></i>
                        <span><?php echo esc_html__('Filters', 'claue'); ?></span>
                    </button>
                </div>
            </div>

            <!-- Demo Gifts Grid - Mixed LV Style -->
            <div class="gifts-grid gifts-grid-lv">
                <?php 
                // Sample gifts data
                $demo_gifts = array(
                    array(
                        'title' => 'Luxury Watch Collection',
                        'price' => '25000000',
                        'brand' => 'Rolex',
                        'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=400&h=400&fit=crop',
                        'featured' => true,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Designer Handbag',
                        'price' => '15000000',
                        'brand' => 'Louis Vuitton',
                        'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'limited'
                    ),
                    array(
                        'title' => 'Premium Perfume Set',
                        'price' => '3500000',
                        'brand' => 'Chanel',
                        'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Diamond Necklace',
                        'price' => '45000000',
                        'brand' => 'Cartier',
                        'image' => 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Luxury Sunglasses',
                        'price' => '2800000',
                        'brand' => 'Gucci',
                        'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'limited'
                    ),
                    array(
                        'title' => 'Premium Wine Collection',
                        'price' => '8500000',
                        'brand' => 'Dom Pérignon',
                        'image' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Designer Wallet',
                        'price' => '4200000',
                        'brand' => 'Hermès',
                        'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Luxury Cufflinks',
                        'price' => '1800000',
                        'brand' => 'Montblanc',
                        'image' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'limited'
                    ),
                    array(
                        'title' => 'Premium Leather Belt',
                        'price' => '3200000',
                        'brand' => 'Bottega Veneta',
                        'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Luxury Pen Set',
                        'price' => '1200000',
                        'brand' => 'Montblanc',
                        'image' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    ),
                    array(
                        'title' => 'Designer Scarf',
                        'price' => '2800000',
                        'brand' => 'Hermès',
                        'image' => 'https://images.unsplash.com/photo-1573408301185-9146fe634ad0?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'limited'
                    ),
                    array(
                        'title' => 'Premium Tea Set',
                        'price' => '8500000',
                        'brand' => 'TWG',
                        'image' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=400&h=400&fit=crop',
                        'featured' => false,
                        'availability' => 'in_stock'
                    )
                );

                $counter = 0;
                foreach ($demo_gifts as $gift) : 
                    $counter++;
                    $card_class = 'gift-card';
                    if ($gift['featured']) {
                        $card_class .= ' featured-lv';
                    } elseif ($counter % 6 == 0) {
                        $card_class .= ' medium-lv';
                    } else {
                        $card_class .= ' regular-lv';
                    }
                    ?>
                    
                    <article class="<?php echo esc_attr($card_class); ?>">
                        
                        <!-- Gift Image -->
                        <div class="gift-image">
                            <a href="#">
                                <img src="<?php echo esc_url($gift['image']); ?>" alt="<?php echo esc_attr($gift['title']); ?>" class="gift-thumbnail">
                            </a>
                            
                            <!-- Featured Badge -->
                            <?php if ($gift['featured']) : ?>
                                <div class="featured-badge">
                                    <i class="fa fa-star"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Gift Info -->
                        <div class="gift-info">
                            <h3 class="gift-title">
                                <a href="#"><?php echo esc_html($gift['title']); ?></a>
                            </h3>
                            
                            <!-- Gift Price -->
                            <div class="gift-price">
                                <span class="currency">IDR</span>
                                <span class="price"><?php echo esc_html(number_format($gift['price'], 0, ',', '.')); ?></span>
                            </div>
                            
                            <!-- Gift Brand -->
                            <div class="gift-brand">
                                <?php echo esc_html($gift['brand']); ?>
                            </div>
                            
                            <!-- Gift Availability -->
                            <div class="gift-availability <?php echo esc_attr($gift['availability']); ?>">
                                <?php 
                                switch ($gift['availability']) {
                                    case 'in_stock':
                                        echo '<i class="fa fa-check-circle"></i> ' . esc_html__('In Stock', 'claue');
                                        break;
                                    case 'limited':
                                        echo '<i class="fa fa-exclamation-triangle"></i> ' . esc_html__('Limited Stock', 'claue');
                                        break;
                                    case 'out_of_stock':
                                        echo '<i class="fa fa-times-circle"></i> ' . esc_html__('Out of Stock', 'claue');
                                        break;
                                }
                                ?>
                            </div>
                        </div>

                    </article>

                <?php endforeach; ?>
            </div>

            <!-- Demo Info -->
            <div class="demo-info" style="text-align: center; margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h3><?php echo esc_html__('Demo Layout - Louis Vuitton Style Mixed Grid', 'claue'); ?></h3>
                <p><?php echo esc_html__('This is a demo page showing the Louis Vuitton inspired mixed grid layout. The layout includes:', 'claue'); ?></p>
                <ul style="list-style: none; padding: 0; margin: 20px 0;">
                    <li style="margin: 10px 0;">✅ <strong>Featured Cards (2x2)</strong> - Large cards for premium items</li>
                    <li style="margin: 10px 0;">✅ <strong>Regular Cards (1x1)</strong> - Standard size cards</li>
                    <li style="margin: 10px 0;">✅ <strong>Medium Cards (1x1.5)</strong> - Every 6th card is medium size</li>
                    <li style="margin: 10px 0;">✅ <strong>Responsive Design</strong> - Adapts to all screen sizes</li>
                </ul>
                <p><em><?php echo esc_html__('To use this layout in Elementor, add the "Gift Grid" widget and select "Louis Vuitton Mixed Grid" layout type.', 'claue'); ?></em></p>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?> 