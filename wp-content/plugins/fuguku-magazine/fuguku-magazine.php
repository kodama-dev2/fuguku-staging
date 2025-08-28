<?php
/**
 * Plugin Name: Fuguku Magazine
 * Description: Custom post type "Magazine" with categories and LV-style permalinks for Fuguku. Elementor-compatible with essential meta fields.
 * Version: 1.5.0
 * Author: Fuguku Dev Team
 * License: GPLv2 or later
 *
 * Version: 1.5.0
 * Last Updated: 2025-08-28 18:55
 * Description: Add [mag_image] — simple image widget-style shortcode with configurable width and aspect ratio using object-fit: cover; works with direct attachment ID, URL, or gallery2 index. Keeps v1.2.0 improvements.
 * Version History:
 * v1.5.0 - Dummy stability release (same code as v1.3.0); deployment sync only
 * v1.3.0 - New [mag_image] shortcode (width + aspect-ratio + cover; id/src/index sources)
 * v1.2.0 - Shortcode index attr + gallery2 layouts per image (cover)
 * v1.1.0 - Gallery1/2 meta + shortcodes, repeatable section titles/subtitles
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
        add_action('init', [self::class, 'register_rewrite']);
        add_action('init', [self::class, 'register_shortcodes']);
        add_filter('post_type_link', [self::class, 'filter_post_type_link'], 10, 2);
        add_action('add_meta_boxes', [self::class, 'register_meta_boxes']);
        add_action('save_post_' . self::CPT, [self::class, 'save_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_admin_assets']);
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

    /**
     * Add rewrite tag and explicit rule for /magazine/<category>/<post-slug>
     */
    public static function register_rewrite(): void {
        // Allow %magazine_category% placeholder to be parsed
        add_rewrite_tag('%' . self::TAX . '%', '([^/]+)', self::TAX . '=');

        // Single: /magazine/{category}/{postname}
        add_rewrite_rule(
            '^magazine/([^/]+)/([^/]+)/?$',
            'index.php?post_type=' . self::CPT . '&name=$matches[2]&' . self::TAX . '=$matches[1]',
            'top'
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

        add_meta_box(
            'magazine_galleries_sections',
            'Magazine Galleries & Sections',
            [self::class, 'render_galleries_sections_meta_box'],
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

        // Galleries: expect comma-separated IDs
        if (array_key_exists('mag_gallery1_ids', $_POST)) {
            $ids = array_filter(array_map('intval', array_filter(array_map('trim', explode(',', (string) $_POST['mag_gallery1_ids'])))));
            update_post_meta($post_id, 'mag_gallery1_ids', $ids);
        }
        if (array_key_exists('mag_gallery2_ids', $_POST)) {
            $ids = array_slice(
                array_filter(array_map('intval', array_filter(array_map('trim', explode(',', (string) $_POST['mag_gallery2_ids']))))),
                0,
                6
            );
            update_post_meta($post_id, 'mag_gallery2_ids', $ids);
        }

        // Repeatable titles/subtitles
        if (array_key_exists('mag_section_titles', $_POST)) {
            $titles = array_values(array_filter(array_map('sanitize_text_field', (array) $_POST['mag_section_titles'])));
            update_post_meta($post_id, 'mag_section_titles', $titles);
        }
        if (array_key_exists('mag_section_subtitles', $_POST)) {
            $subtitles = array_values(array_filter(array_map('sanitize_text_field', (array) $_POST['mag_section_subtitles'])));
            update_post_meta($post_id, 'mag_section_subtitles', $subtitles);
        }
    }

    public static function enqueue_admin_assets(string $hook): void {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== self::CPT) {
            return;
        }
        wp_enqueue_media();
    }

    public static function render_galleries_sections_meta_box(\WP_Post $post): void {
        wp_nonce_field('magazine_meta_save', 'magazine_meta_nonce');

        $g1 = get_post_meta($post->ID, 'mag_gallery1_ids', true);
        $g2 = get_post_meta($post->ID, 'mag_gallery2_ids', true);
        $g1 = is_array($g1) ? $g1 : [];
        $g2 = is_array($g2) ? $g2 : [];
        $titles = get_post_meta($post->ID, 'mag_section_titles', true);
        $subs   = get_post_meta($post->ID, 'mag_section_subtitles', true);
        $titles = is_array($titles) ? $titles : [];
        $subs   = is_array($subs) ? $subs : [];

        $g1_csv = implode(',', array_map('intval', $g1));
        $g2_csv = implode(',', array_map('intval', $g2));
        ?>
        <style>
            .mag-gallery-grid-admin{display:flex;gap:8px;flex-wrap:wrap}
            .mag-gallery-grid-admin img{width:60px;height:60px;object-fit:cover;border:1px solid #e2e2e2}
            .mag-repeater-row{display:flex;gap:8px;margin-bottom:6px}
            .mag-repeater-row input{width:100%}
        </style>
        <h4>Gallery 1 (unlimited, 5-col grid with load more)</h4>
        <p>
            <input type="hidden" id="mag_gallery1_ids" name="mag_gallery1_ids" value="<?php echo esc_attr($g1_csv); ?>" />
            <button type="button" class="button" id="mag_gallery1_select">Select Images</button>
        </p>
        <div id="mag_gallery1_preview" class="mag-gallery-grid-admin">
            <?php foreach ($g1 as $id): $src = wp_get_attachment_image_url((int) $id, 'thumbnail'); if ($src): ?>
                <img src="<?php echo esc_url($src); ?>" />
            <?php endif; endforeach; ?>
        </div>
        <hr/>
        <h4>Gallery 2 (max 6 images)</h4>
        <p>
            <input type="hidden" id="mag_gallery2_ids" name="mag_gallery2_ids" value="<?php echo esc_attr($g2_csv); ?>" />
            <button type="button" class="button" id="mag_gallery2_select">Select Images (max 6)</button>
        </p>
        <div id="mag_gallery2_preview" class="mag-gallery-grid-admin">
            <?php foreach ($g2 as $id): $src = wp_get_attachment_image_url((int) $id, 'thumbnail'); if ($src): ?>
                <img src="<?php echo esc_url($src); ?>" />
            <?php endif; endforeach; ?>
        </div>
        <hr/>
        <h4>Section Titles (multiple)</h4>
        <div id="mag_titles_repeater">
            <?php if (!$titles) { $titles = ['']; }
            foreach ($titles as $val): ?>
                <div class="mag-repeater-row"><input type="text" name="mag_section_titles[]" value="<?php echo esc_attr((string) $val); ?>" /><button type="button" class="button mag-remove">Remove</button></div>
            <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" id="mag_titles_add">+ Add Title</button></p>
        <h4>Section Subtitles (multiple)</h4>
        <div id="mag_subs_repeater">
            <?php if (!$subs) { $subs = ['']; }
            foreach ($subs as $val): ?>
                <div class="mag-repeater-row"><input type="text" name="mag_section_subtitles[]" value="<?php echo esc_attr((string) $val); ?>" /><button type="button" class="button mag-remove">Remove</button></div>
            <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" id="mag_subs_add">+ Add Subtitle</button></p>

        <script>
        (function($){
            function openPicker(targetInputId, previewId, multiple, max){
                const input = $('#'+targetInputId);
                const preview = $('#'+previewId);
                const frame = wp.media({
                    title: 'Select Images',
                    button: { text: 'Use Images' },
                    library: { type: 'image' },
                    multiple: multiple
                });
                frame.on('select', function(){
                    const selection = frame.state().get('selection');
                    const ids = [];
                    preview.empty();
                    selection.each(function(att){
                        if (max && ids.length >= max) return;
                        ids.push(att.get('id'));
                        const thumb = att.get('sizes') && att.get('sizes').thumbnail ? att.get('sizes').thumbnail.url : att.get('url');
                        preview.append($('<img/>',{src:thumb}));
                    });
                    input.val(ids.join(','));
                });
                frame.open();
            }
            $('#mag_gallery1_select').on('click', function(){ openPicker('mag_gallery1_ids','mag_gallery1_preview', true, null); });
            $('#mag_gallery2_select').on('click', function(){ openPicker('mag_gallery2_ids','mag_gallery2_preview', true, 6); });

            function bindRepeater(container){
                container.on('click', '.mag-remove', function(){ $(this).closest('.mag-repeater-row').remove(); });
            }
            bindRepeater($('#mag_titles_repeater'));
            bindRepeater($('#mag_subs_repeater'));
            $('#mag_titles_add').on('click', function(){ $('#mag_titles_repeater').append('<div class="mag-repeater-row"><input type="text" name="mag_section_titles[]" value="" /><button type="button" class="button mag-remove">Remove</button></div>'); });
            $('#mag_subs_add').on('click', function(){ $('#mag_subs_repeater').append('<div class="mag-repeater-row"><input type="text" name="mag_section_subtitles[]" value="" /><button type="button" class="button mag-remove">Remove</button></div>'); });
        })(jQuery);
        </script>
        <?php
    }

    public static function register_shortcodes(): void {
        add_shortcode('mag_gallery1', [self::class, 'shortcode_gallery1']);
        add_shortcode('mag_gallery2', [self::class, 'shortcode_gallery2']);
        add_shortcode('mag_section_titles', [self::class, 'shortcode_section_titles']);
        add_shortcode('mag_section_subtitles', [self::class, 'shortcode_section_subtitles']);
        add_shortcode('mag_image', [self::class, 'shortcode_image']);
    }

    public static function shortcode_gallery1($atts = []): string {
        if (!is_singular(self::CPT)) { return ''; }
        $atts = shortcode_atts(['columns' => 5, 'initial' => 15, 'step' => 15, 'index' => ''], $atts, 'mag_gallery1');
        $post_id = get_the_ID();
        $ids = get_post_meta($post_id, 'mag_gallery1_ids', true);
        $ids = is_array($ids) ? array_filter(array_map('intval', $ids)) : [];
        if (!$ids) { return ''; }
        // Single image mode by index (1-based)
        $index = trim((string) $atts['index']) !== '' ? max(1, (int) $atts['index']) : 0;
        if ($index > 0) {
            $i = $index - 1;
            if (!array_key_exists($i, $ids)) { return ''; }
            $src = wp_get_attachment_image_url((int) $ids[$i], 'large');
            if (!$src) { return ''; }
            return '<img class="mag-g1-single" src="' . esc_url($src) . '" loading="lazy" style="width:100%;height:auto;display:block" />';
        }
        $columns = max(1, (int) $atts['columns']);
        $initial = max(1, (int) $atts['initial']);
        $step    = max(1, (int) $atts['step']);
        ob_start();
        ?>
        <style>
        .mag-g1-grid{display:grid;grid-template-columns:repeat(<?php echo (int)$columns; ?>, 1fr);gap:8px}
        .mag-g1-grid img{width:100%;height:auto;display:block}
        .mag-g1-loadmore{text-align:center;margin-top:16px}
        </style>
        <div class="mag-g1-grid" data-initial="<?php echo (int)$initial; ?>" data-step="<?php echo (int)$step; ?>">
            <?php foreach ($ids as $i => $id): $src = wp_get_attachment_image_url($id, 'large'); if ($src): ?>
                <img src="<?php echo esc_url($src); ?>" loading="lazy" style="<?php echo $i >= $initial ? 'display:none' : ''; ?>" />
            <?php endif; endforeach; ?>
        </div>
        <?php if (count($ids) > $initial): ?>
        <div class="mag-g1-loadmore"><button type="button" class="button">Load more</button></div>
        <script>
        (function(){
            const grid=document.currentScript.previousElementSibling.previousElementSibling; // grid div
            const btn=document.currentScript.previousElementSibling.querySelector('button');
            if(!grid||!btn) return;
            const step=parseInt(grid.getAttribute('data-step'))||15;
            btn.addEventListener('click',()=>{
                const imgs=grid.querySelectorAll('img[style*="display:none"]');
                let shown=0;
                imgs.forEach(img=>{ if(shown<step){ img.style.display=''; shown++; } });
                if(grid.querySelectorAll('img[style*="display:none"]').length===0){ btn.parentElement.style.display='none'; }
            });
        })();
        </script>
        <?php endif; ?>
        <?php
        return (string) ob_get_clean();
    }

    public static function shortcode_gallery2($atts = []): string {
        if (!is_singular(self::CPT)) { return ''; }
        $atts = shortcode_atts([
            'index'   => '',                // optional: show single image by 1-based index
            'layout'  => 'square',          // used when index is specified
            'layouts' => '',                // comma-separated layouts for each image (order-based)
        ], $atts, 'mag_gallery2');
        $post_id = get_the_ID();
        $ids = get_post_meta($post_id, 'mag_gallery2_ids', true);
        $ids = is_array($ids) ? array_slice(array_filter(array_map('intval', $ids)), 0, 6) : [];
        if (!$ids) { return ''; }

        // Helpers
        $normalize_layout = function(string $token): string {
            $token = strtolower(trim($token));
            $allowed = ['square','portrait','landscape','tall','wide'];
            return in_array($token, $allowed, true) ? $token : 'square';
        };

        // Single image mode
        $index = trim((string) $atts['index']) !== '' ? max(1, (int) $atts['index']) : 0;
        if ($index > 0) {
            $i = $index - 1;
            if (!array_key_exists($i, $ids)) { return ''; }
            $layout = $normalize_layout((string) $atts['layout']);
            $src = wp_get_attachment_image_url((int) $ids[$i], 'large');
            if (!$src) { return ''; }
            ob_start();
            ?>
            <style>
            .mag-g2-item{position:relative;width:100%;overflow:hidden}
            .mag-g2-item img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
            .mag-g2-item.is-square{aspect-ratio:1/1}
            .mag-g2-item.is-portrait{aspect-ratio:3/4}
            .mag-g2-item.is-landscape{aspect-ratio:4/3}
            .mag-g2-item.is-tall{aspect-ratio:1/2}
            .mag-g2-item.is-wide{aspect-ratio:2/1}
            </style>
            <div class="mag-g2-item <?php echo 'is-' . esc_attr($layout); ?>">
                <img src="<?php echo esc_url($src); ?>" loading="lazy" />
            </div>
            <?php
            return (string) ob_get_clean();
        }

        // Multi image grid mode
        $layouts = array_map($normalize_layout, array_filter(array_map('trim', explode(',', (string) $atts['layouts']))));
        ob_start();
        ?>
        <style>
        .mag-g2-grid{display:grid;grid-template-columns:repeat(3, 1fr);gap:8px}
        .mag-g2-item{position:relative;width:100%;overflow:hidden}
        .mag-g2-item img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
        .mag-g2-item.is-square{aspect-ratio:1/1}
        .mag-g2-item.is-portrait{aspect-ratio:3/4}
        .mag-g2-item.is-landscape{aspect-ratio:4/3}
        .mag-g2-item.is-tall{aspect-ratio:1/2}
        .mag-g2-item.is-wide{aspect-ratio:2/1}
        @media (min-width:768px){.mag-g2-grid{grid-template-columns:repeat(3,1fr)}}
        @media (min-width:1024px){.mag-g2-grid{grid-template-columns:repeat(3,1fr)}}
        </style>
        <div class="mag-g2-grid">
            <?php foreach ($ids as $idx => $id): $src = wp_get_attachment_image_url($id, 'large'); if ($src):
                $layout = isset($layouts[$idx]) ? $layouts[$idx] : 'square'; ?>
                <div class="mag-g2-item <?php echo 'is-' . esc_attr($layout); ?>">
                    <img src="<?php echo esc_url($src); ?>" loading="lazy" />
                </div>
            <?php endif; endforeach; ?>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public static function shortcode_section_titles($atts = []): string {
        if (!is_singular(self::CPT)) { return ''; }
        $atts = shortcode_atts(['index' => ''], $atts, 'mag_section_titles');
        $vals = get_post_meta(get_the_ID(), 'mag_section_titles', true);
        $vals = is_array($vals) ? array_values(array_filter(array_map('sanitize_text_field', $vals))) : [];
        if (!$vals) { return ''; }
        $index = trim((string) $atts['index']) !== '' ? max(1, (int) $atts['index']) : 0;
        if ($index > 0) {
            $i = $index - 1;
            if (!array_key_exists($i, $vals)) { return ''; }
            return esc_html((string) $vals[$i]);
        }
        return '<ul class="mag-section-titles"><li>'.implode('</li><li>', array_map('esc_html', $vals)).'</li></ul>';
    }

    public static function shortcode_section_subtitles($atts = []): string {
        if (!is_singular(self::CPT)) { return ''; }
        $atts = shortcode_atts(['index' => ''], $atts, 'mag_section_subtitles');
        $vals = get_post_meta(get_the_ID(), 'mag_section_subtitles', true);
        $vals = is_array($vals) ? array_values(array_filter(array_map('sanitize_text_field', $vals))) : [];
        if (!$vals) { return ''; }
        $index = trim((string) $atts['index']) !== '' ? max(1, (int) $atts['index']) : 0;
        if ($index > 0) {
            $i = $index - 1;
            if (!array_key_exists($i, $vals)) { return ''; }
            return esc_html((string) $vals[$i]);
        }
        return '<ul class="mag-section-subtitles"><li>'.implode('</li><li>', array_map('esc_html', $vals)).'</li></ul>';
    }

    /**
     * [mag_image] — Simple widget-like image with cover behavior and configurable width + aspect ratio.
     * Usage examples:
     *  - [mag_image id=123 ratio="3/4" width="50%"]
     *  - [mag_image src="https://.../img.jpg" ratio="2/1" width="100%"]
     *  - [mag_image index=2 source=gallery2 ratio="1/1" width="100%"]
     */
    public static function shortcode_image($atts = []): string {
        if (!is_singular(self::CPT)) { return ''; }
        $atts = shortcode_atts([
            'id'     => '',         // attachment ID
            'src'    => '',         // image URL
            'index'  => '',         // select from gallery when provided
            'source' => 'gallery2', // gallery2|gallery1 when using index
            'ratio'  => '1/1',      // aspect ratio string or token (square|portrait|landscape|tall|wide)
            'width'  => '100%',     // container width
            'alt'    => '',
            'class'  => '',
        ], $atts, 'mag_image');

        $normalize_ratio = function(string $token): string {
            $token = strtolower(trim($token));
            $map = [
                'square'    => '1/1',
                'portrait'  => '3/4',
                'landscape' => '4/3',
                'tall'      => '1/2',
                'wide'      => '2/1',
            ];
            if (isset($map[$token])) { return $map[$token]; }
            // Validate custom ratio like "w/h"
            if (preg_match('/^\s*(\d+(?:\.\d+)?)\s*\/\s*(\d+(?:\.\d+)?)\s*$/', $token)) {
                return $token;
            }
            return '1/1';
        };

        $src = '';
        $alt = sanitize_text_field((string) $atts['alt']);

        // Priority: id > src > index+source
        $id = (int) $atts['id'];
        if ($id > 0) {
            $src = (string) wp_get_attachment_image_url($id, 'full');
            if (!$alt) { $alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: ''; }
        } elseif (!empty($atts['src'])) {
            $src = esc_url_raw((string) $atts['src']);
        } elseif (trim((string) $atts['index']) !== '') {
            $index = max(1, (int) $atts['index']);
            $source = strtolower((string) $atts['source']) === 'gallery1' ? 'gallery1' : 'gallery2';
            $meta_key = $source === 'gallery1' ? 'mag_gallery1_ids' : 'mag_gallery2_ids';
            $ids = get_post_meta(get_the_ID(), $meta_key, true);
            $ids = is_array($ids) ? array_values(array_filter(array_map('intval', $ids))) : [];
            $i = $index - 1;
            if (array_key_exists($i, $ids)) {
                $src = (string) wp_get_attachment_image_url((int) $ids[$i], 'full');
                if (!$alt) { $alt = get_post_meta((int) $ids[$i], '_wp_attachment_image_alt', true) ?: ''; }
            }
        }

        if (!$src) { return ''; }

        $ratio = $normalize_ratio((string) $atts['ratio']);
        $width = trim((string) $atts['width']);
        if ($width === '') { $width = '100%'; }
        // Basic CSS length validation fallback
        if (!preg_match('/^(?:\d+(?:\.\d+)?(?:px|rem|em|vw|vh|%)|auto)$/', $width)) {
            $width = '100%';
        }

        $classes = trim('mag-image ' . sanitize_html_class((string) $atts['class']));

        ob_start();
        ?>
        <style>
        .mag-image{display:block}
        .mag-image-inner{position:relative;width:100%;overflow:hidden}
        .mag-image-inner{aspect-ratio:var(--ratio,1/1)}
        .mag-image-inner img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
        </style>
        <div class="<?php echo esc_attr($classes); ?>" style="width: <?php echo esc_attr($width); ?>;">
            <div class="mag-image-inner" style="--ratio: <?php echo esc_attr($ratio); ?>;">
                <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" />
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}

Fuguku_Magazine::init();
register_activation_hook(__FILE__, ['Fuguku_Magazine', 'activate']);
register_deactivation_hook(__FILE__, ['Fuguku_Magazine', 'deactivate']);

