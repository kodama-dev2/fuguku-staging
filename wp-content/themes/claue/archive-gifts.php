<?php
/**
 * Template for displaying gift archives - Louis Vuitton Style Mixed Grid
 * 
 * @package Claue
 * @version 1.1.0
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

            <!-- Gifts Grid - Mixed LV Style -->
            <div class="gifts-grid gifts-grid-lv">
                <?php 
                $counter = 0;
                if (have_posts()) : 
                    while (have_posts()) : the_post(); 
                        $counter++;
                        $is_featured = get_post_meta(get_the_ID(), '_featured_gift', true);
                        $card_class = 'gift-card';
                        if ($is_featured) {
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
                                <?php 
                                $gift_price = get_post_meta(get_the_ID(), '_gift_price', true);
                                if ($gift_price) : ?>
                                    <div class="gift-price">
                                        <span class="currency">IDR</span>
                                        <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php 
                                $gift_brand = get_post_meta(get_the_ID(), '_gift_brand', true);
                                if ($gift_brand) : ?>
                                    <div class="gift-brand">
                                        <?php echo esc_html($gift_brand); ?>
                                    </div>
                                <?php endif; ?>
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
                <?php else : ?>
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
            <?php if (have_posts()) : ?>
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