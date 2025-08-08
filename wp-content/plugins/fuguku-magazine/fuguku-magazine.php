<?php
/**
 * Plugin Name: Fuguku Magazine
 * Description: Custom post type "Magazine" with categories and LV-style permalinks for Fuguku. Elementor-compatible with essential meta fields.
 * Version: 1.0.0
 * Author: Fuguku Dev Team
 * License: GPLv2 or later
 *
 * Version: 1.0.0
 * Last Updated: 2025-08-08 00:00
 * Description: Initial release — CPT `magazine`, taxonomy `magazine_category`, seeded terms (Fashion Shows, Arts and Culture, Sustainability), custom permalinks, meta fields, REST/Elementor support.
 * Version History:
 * v1.0.0 - Initial CPT, taxonomy, term seeding, permalinks, meta boxes, REST support
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Fuguku_Magazine {
    private const CPT = 'magazine';
    private const TAX = 'magazine_category';

    public static function init(): void {
        add_action('init', [self::class, 'register_types']);
        add_filter('post_type_link', [self::class, 'filter_post_type_link'], 10, 2);
        add_action('add_meta_boxes', [self::class, 'register_meta_boxes']);
        add_action('save_post_' . self::CPT, [self::class, 'save_meta'], 10, 2);
    }

    public static function activate(): void {
        self::register_types();

        $defaults = [
            ['name' => 'Fashion Shows',    'slug' => 'fashion-shows'],
            ['name' => 'Arts and Culture', 'slug' => 'arts-and-culture'],
            ['name' => 'Sustainability',   'slug' => 'sustainability'],
        ];
        foreach ($defaults as $term) {
            if (!term_exists($term['slug'], self::TAX)) {
                wp_insert_term($term['name'], self::TAX, ['slug' => $term['slug']]);
            }
        }

        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    public static function register_types(): void {
        register_taxonomy(
            self::TAX,
            [self::CPT],
            [
                'label' => 'Magazine Categories',
                'labels' => [
                    'name'          => 'Magazine Categories',
                    'singular_name' => 'Magazine Category',
                ],
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'hierarchical'      => true,
                'rewrite'           => [
                    'slug'         => 'magazine',
                    'with_front'   => false,
                    'hierarchical' => true,
                ],
            ]
        );

        register_post_type(
            self::CPT,
            [
                'label'  => 'Magazine',
                'labels' => [
                    'name'               => 'Magazine',
                    'singular_name'      => 'Magazine Post',
                    'add_new'            => 'Add New',
                    'add_new_item'       => 'Add New Magazine Post',
                    'edit_item'          => 'Edit Magazine Post',
                    'new_item'           => 'New Magazine Post',
                    'view_item'          => 'View Magazine Post',
                    'search_items'       => 'Search Magazine',
                    'not_found'          => 'No magazine posts found',
                    'not_found_in_trash' => 'No magazine posts found in Trash',
                    'all_items'          => 'All Magazine Posts',
                ],
                'public'             => true,
                'has_archive'        => 'magazine',
                'rewrite'            => [
                    'slug'       => 'magazine/%' . self::TAX . '%',
                    'with_front' => false,
                ],
                'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'],
                'show_in_rest'       => true,
                'menu_position'      => 20,
                'menu_icon'          => 'dashicons-media-document',
                'taxonomies'         => [self::TAX],
            ]
        );
    }

    public static function filter_post_type_link(string $permalink, \WP_Post $post): string {
        if ($post->post_type !== self::CPT) {
            return $permalink;
        }
        $needle = '%' . self::TAX . '%';
        if (strpos($permalink, $needle) === false) {
            return $permalink;
        }
        $terms = get_the_terms($post, self::TAX);
        $slug  = 'uncategorized';
        if ($terms && !is_wp_error($terms)) {
            $first = current($terms);
            if ($first && isset($first->slug)) {
                $slug = sanitize_title($first->slug);
            }
        }
        return str_replace($needle, $slug, $permalink);
    }

    public static function register_meta_boxes(): void {
        add_meta_box(
            'magazine_meta',
            'Magazine Details',
            [self::class, 'render_meta_box'],
            self::CPT,
            'normal',
            'default'
        );
    }

    public static function render_meta_box(\WP_Post $post): void {
        wp_nonce_field('magazine_meta_save', 'magazine_meta_nonce');

        $subtitle = get_post_meta($post->ID, 'mag_subtitle', true);
        $readTime = get_post_meta($post->ID, 'mag_read_time', true);
        $heroType = get_post_meta($post->ID, 'mag_hero_type', true) ?: 'image';
        $heroImg  = (int) get_post_meta($post->ID, 'mag_hero_image_id', true);
        $heroVid  = get_post_meta($post->ID, 'mag_hero_video_url', true);
        ?>
        <p>
            <label for="mag_subtitle">Subtitle</label><br/>
            <input type="text" id="mag_subtitle" name="mag_subtitle" value="<?php echo esc_attr((string) $subtitle); ?>" class="regular-text" />
        </p>
        <p>
            <label for="mag_read_time">Read Time (minutes)</label><br/>
            <input type="number" min="0" id="mag_read_time" name="mag_read_time" value="<?php echo esc_attr((string) $readTime); ?>" class="small-text" />
        </p>
        <p>
            <label>Hero Type</label><br/>
            <label><input type="radio" name="mag_hero_type" value="image" <?php checked($heroType, 'image'); ?> /> Image</label>
            <label><input type="radio" name="mag_hero_type" value="video" <?php checked($heroType, 'video'); ?> /> Video</label>
        </p>
        <p>
            <label for="mag_hero_image_id">Hero Image (Attachment ID)</label><br/>
            <input type="number" id="mag_hero_image_id" name="mag_hero_image_id" value="<?php echo esc_attr((string) $heroImg); ?>" class="small-text" />
        </p>
        <p>
            <label for="mag_hero_video_url">Hero Video URL</label><br/>
            <input type="url" id="mag_hero_video_url" name="mag_hero_video_url" value="<?php echo esc_url((string) $heroVid); ?>" class="regular-text code" />
        </p>
        <p class="description">All fields are available to Elementor via “Post Custom Field”.</p>
        <?php
    }

    public static function save_meta(int $post_id, \WP_Post $post): void {
        if (!isset($_POST['magazine_meta_nonce']) || !wp_verify_nonce((string) $_POST['magazine_meta_nonce'], 'magazine_meta_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $map = [
            'mag_subtitle'       => fn($v) => sanitize_text_field((string) $v),
            'mag_read_time'      => fn($v) => (string) max(0, (int) $v),
            'mag_hero_type'      => fn($v) => in_array($v, ['image','video'], true) ? $v : 'image',
            'mag_hero_image_id'  => fn($v) => (string) max(0, (int) $v),
            'mag_hero_video_url' => fn($v) => esc_url_raw((string) $v),
        ];
        foreach ($map as $key => $sanitize) {
            if (array_key_exists($key, $_POST)) {
                update_post_meta($post_id, $key, $sanitize($_POST[$key]));
            }
        }
    }
}

Fuguku_Magazine::init();
register_activation_hook(__FILE__, ['Fuguku_Magazine', 'activate']);
register_deactivation_hook(__FILE__, ['Fuguku_Magazine', 'deactivate']);

