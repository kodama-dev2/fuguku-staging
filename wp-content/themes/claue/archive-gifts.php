<?php
/**
 * Template for displaying gift archives - Louis Vuitton Style 14-Card Layout
 * 
 * @package Claue
 * @version 1.2.0
 */

get_header(); ?>

<div class="jas-container">
    <div class="jas-row">
        <div class="jas-col-md-12">
            
            <!-- Page Header -->
            <div class="gifts-header">
                <h1 class="gifts-title"><?php echo esc_html__('Gifts Collection', 'claue'); ?></h1>
                <p class="gifts-subtitle"><?php echo esc_html__('Discover our curated collection of luxury gifts', 'claue'); ?></p>
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
                            <?php
                            $gift_categories = get_terms(array(
                                'taxonomy' => 'gift_category',
                                'hide_empty' => true,
                            ));
                            if (!empty($gift_categories) && !is_wp_error($gift_categories)) {
                                foreach ($gift_categories as $category) {
                                    echo '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                                }
                            }
                            ?>
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

            <!-- Gifts Grid - 14 Card Layout -->
            <div class="gifts-grid gifts-grid-lv">
                <?php 
                // Query gifts ordered by modified date
                $gifts_query = new WP_Query(array(
                    'post_type' => 'gifts',
                    'posts_per_page' => 14,
                    'orderby' => 'modified',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ));

                $counter = 0;
                if ($gifts_query->have_posts()) : 
                    while ($gifts_query->have_posts()) : $gifts_query->the_post(); 
                        $counter++;
                        $is_featured = get_post_meta(get_the_ID(), '_featured_gift', true);
                        
                        // Determine card class based on position
                        if ($counter == 1 || $counter == 4 || $counter == 8 || $counter == 11) {
                            $card_class = 'gift-card portrait-large';
                        } elseif ($counter == 2 || $counter == 3 || $counter == 9 || $counter == 10) {
                            $card_class = 'gift-card regular';
                        } elseif ($counter == 5 || $counter == 7 || $counter == 12 || $counter == 14) {
                            $card_class = 'gift-card landscape-wide';
                        } elseif ($counter == 6 || $counter == 13) {
                            $card_class = 'gift-card portrait-tall';
                        } else {
                            $card_class = 'gift-card regular';
                        }
                        ?>
                        
                        <article class="<?php echo esc_attr($card_class); ?>">
                            
                            <!-- Gift Image -->
                            <div class="gift-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', array('class' => 'gift-thumbnail')); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="gift-placeholder">
                                            <i class="fa fa-gift"></i>
                                        </div>
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Featured Badge -->
                                <?php if ($is_featured) : ?>
                                    <div class="featured-badge">
                                        <i class="fa fa-star"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Gift Info -->
                            <div class="gift-info">
                                <h3 class="gift-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <!-- Gift Price -->
                                <?php 
                                $gift_price = get_post_meta(get_the_ID(), '_gift_price', true);
                                if ($gift_price) : ?>
                                    <div class="gift-price">
                                        <span class="currency">IDR</span>
                                        <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Gift Brand -->
                                <?php 
                                $gift_brand = get_post_meta(get_the_ID(), '_gift_brand', true);
                                if ($gift_brand) : ?>
                                    <div class="gift-brand">
                                        <?php echo esc_html($gift_brand); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Gift Availability -->
                                <?php 
                                $gift_availability = get_post_meta(get_the_ID(), '_gift_availability', true);
                                if ($gift_availability) : ?>
                                    <div class="gift-availability <?php echo esc_attr($gift_availability); ?>">
                                        <?php 
                                        switch ($gift_availability) {
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
                                <?php endif; ?>
                            </div>

                        </article>

                    <?php endwhile; ?>
                    
                    <?php
                    // Add placeholders if needed
                    $total_items = $gifts_query->found_posts;
                    $placeholders_needed = max(0, 14 - $total_items);
                    
                    for ($i = 0; $i < $placeholders_needed; $i++) {
                        $counter++;
                        
                        // Determine placeholder card class based on position
                        if ($counter == 1 || $counter == 4 || $counter == 8 || $counter == 11) {
                            $card_class = 'gift-card portrait-large placeholder';
                        } elseif ($counter == 2 || $counter == 3 || $counter == 9 || $counter == 10) {
                            $card_class = 'gift-card regular placeholder';
                        } elseif ($counter == 5 || $counter == 7 || $counter == 12 || $counter == 14) {
                            $card_class = 'gift-card landscape-wide placeholder';
                        } elseif ($counter == 6 || $counter == 13) {
                            $card_class = 'gift-card portrait-tall placeholder';
                        } else {
                            $card_class = 'gift-card regular placeholder';
                        }
                        ?>
                        
                        <article class="<?php echo esc_attr($card_class); ?>">
                            <div class="gift-image">
                                <div class="gift-placeholder">
                                    <i class="fa fa-gift"></i>
                                </div>
                            </div>
                            <div class="gift-info">
                                <h3 class="gift-title">Coming Soon</h3>
                                <div class="gift-price">
                                    <span class="currency">IDR</span>
                                    <span class="price">-</span>
                                </div>
                            </div>
                        </article>
                        
                    <?php } ?>
                    
                <?php else : ?>
                    
                    <!-- No Gifts Found -->
                    <div class="no-gifts-found">
                        <div class="no-gifts-icon">
                            <i class="fa fa-gift"></i>
                        </div>
                        <h3><?php echo esc_html__('No Gifts Found', 'claue'); ?></h3>
                        <p><?php echo esc_html__('We couldn\'t find any gifts matching your criteria.', 'claue'); ?></p>
                        <a href="<?php echo esc_url(get_post_type_archive_link('gifts')); ?>" class="btn btn-primary">
                            <?php echo esc_html__('View All Gifts', 'claue'); ?>
                        </a>
                    </div>

                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($gifts_query->have_posts()) : ?>
                <div class="gifts-pagination">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '<i class="fa fa-chevron-left"></i> ' . esc_html__('Previous', 'claue'),
                        'next_text' => esc_html__('Next', 'claue') . ' <i class="fa fa-chevron-right"></i>',
                        'type' => 'list',
                    ));
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php get_footer(); ?> 