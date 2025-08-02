<?php
/**
 * Template for displaying single gift posts
 * 
 * @package Claue
 * @version 1.0.0
 */

get_header(); ?>

<div class="jas-container">
    <div class="jas-row">
        <div class="jas-col-md-12">
            
            <?php while (have_posts()) : the_post(); ?>
                
                <article class="gift-single">
                    
                    <!-- Breadcrumb -->
                    <div class="gift-breadcrumb">
                        <a href="<?php echo esc_url(get_post_type_archive_link('gifts')); ?>">
                            <?php echo esc_html__('Gifts', 'claue'); ?>
                        </a>
                        <span class="separator">/</span>
                        <span class="current"><?php the_title(); ?></span>
                    </div>

                    <div class="gift-content-wrapper">
                        
                        <!-- Gift Gallery -->
                        <div class="gift-gallery">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="gift-main-image">
                                    <?php the_post_thumbnail('large', array('class' => 'gift-featured-image')); ?>
                                </div>
                            <?php else : ?>
                                <div class="gift-placeholder-large">
                                    <i class="fa fa-gift"></i>
                                    <p><?php echo esc_html__('No Image Available', 'claue'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Featured Badge -->
                            <?php if (get_post_meta(get_the_ID(), '_featured_gift', true)) : ?>
                                <div class="featured-badge-large">
                                    <i class="fa fa-star"></i>
                                    <span><?php echo esc_html__('Featured', 'claue'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Gift Details -->
                        <div class="gift-details">
                            
                            <!-- Gift Title -->
                            <h1 class="gift-title"><?php the_title(); ?></h1>
                            
                            <!-- Gift Categories -->
                            <?php 
                            $gift_categories = get_the_terms(get_the_ID(), 'gift_category');
                            if ($gift_categories && !is_wp_error($gift_categories)) : ?>
                                <div class="gift-categories">
                                    <?php foreach ($gift_categories as $category) : ?>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="gift-category-tag">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Gift Price -->
                            <?php 
                            $gift_price = get_post_meta(get_the_ID(), '_gift_price', true);
                            if ($gift_price) : ?>
                                <div class="gift-price-large">
                                    <span class="currency">IDR</span>
                                    <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Gift Brand -->
                            <?php 
                            $gift_brand = get_post_meta(get_the_ID(), '_gift_brand', true);
                            if ($gift_brand) : ?>
                                <div class="gift-brand-large">
                                    <strong><?php echo esc_html__('Brand:', 'claue'); ?></strong>
                                    <span><?php echo esc_html($gift_brand); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Gift Availability -->
                            <?php 
                            $gift_availability = get_post_meta(get_the_ID(), '_gift_availability', true);
                            if ($gift_availability) : ?>
                                <div class="gift-availability-large <?php echo esc_attr($gift_availability); ?>">
                                    <strong><?php echo esc_html__('Availability:', 'claue'); ?></strong>
                                    <?php 
                                    switch ($gift_availability) {
                                        case 'in_stock':
                                            echo '<span class="status in-stock"><i class="fa fa-check-circle"></i> ' . esc_html__('In Stock', 'claue') . '</span>';
                                            break;
                                        case 'limited':
                                            echo '<span class="status limited"><i class="fa fa-exclamation-triangle"></i> ' . esc_html__('Limited Stock', 'claue') . '</span>';
                                            break;
                                        case 'out_of_stock':
                                            echo '<span class="status out-of-stock"><i class="fa fa-times-circle"></i> ' . esc_html__('Out of Stock', 'claue') . '</span>';
                                            break;
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Gift Description -->
                            <div class="gift-description">
                                <?php the_content(); ?>
                            </div>
                            
                            <!-- Gift Tags -->
                            <?php 
                            $gift_tags = get_the_terms(get_the_ID(), 'gift_tag');
                            if ($gift_tags && !is_wp_error($gift_tags)) : ?>
                                <div class="gift-tags">
                                    <strong><?php echo esc_html__('Tags:', 'claue'); ?></strong>
                                    <?php foreach ($gift_tags as $tag) : ?>
                                        <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="gift-tag">
                                            <?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Action Buttons -->
                            <div class="gift-actions">
                                <button class="btn btn-primary gift-action-btn">
                                    <i class="fa fa-shopping-cart"></i>
                                    <?php echo esc_html__('Add to Cart', 'claue'); ?>
                                </button>
                                <button class="btn btn-secondary gift-action-btn">
                                    <i class="fa fa-heart"></i>
                                    <?php echo esc_html__('Add to Wishlist', 'claue'); ?>
                                </button>
                                <button class="btn btn-outline gift-action-btn">
                                    <i class="fa fa-share"></i>
                                    <?php echo esc_html__('Share', 'claue'); ?>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>

                    <!-- Related Gifts -->
                    <div class="related-gifts">
                        <h3><?php echo esc_html__('Related Gifts', 'claue'); ?></h3>
                        <div class="related-gifts-grid">
                            <?php
                            $related_gifts = new WP_Query(array(
                                'post_type' => 'gifts',
                                'posts_per_page' => 4,
                                'post__not_in' => array(get_the_ID()),
                                'meta_query' => array(
                                    array(
                                        'key' => '_featured_gift',
                                        'value' => '1',
                                        'compare' => '='
                                    )
                                )
                            ));
                            
                            if ($related_gifts->have_posts()) :
                                while ($related_gifts->have_posts()) : $related_gifts->the_post(); ?>
                                    
                                    <div class="related-gift-card">
                                        <div class="related-gift-image">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium', array('class' => 'related-gift-thumbnail')); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-gift-info">
                                            <h4 class="related-gift-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <?php 
                                            $related_price = get_post_meta(get_the_ID(), '_gift_price', true);
                                            if ($related_price) : ?>
                                                <div class="related-gift-price">
                                                    <span class="currency">IDR</span>
                                                    <span class="price"><?php echo esc_html(number_format($related_price, 0, ',', '.')); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                <?php endwhile;
                                wp_reset_postdata();
                            endif; ?>
                        </div>
                    </div>

                </article>

            <?php endwhile; ?>

        </div>
    </div>
</div>

<?php get_footer(); ?> 