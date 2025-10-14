<?php
if (!defined('ABSPATH')) { exit; }

class Fugu_Catalog_Table_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'fugu-catalog-table'; }
    public function get_title() { return __('FUGU Catalog Submissions', 'fuguku-gift'); }
    public function get_icon() { return 'eicon-table'; }
    public function get_categories() { return ['fuguku-gifts']; }

    protected function render() {
        $paged = max(1, intval($_GET['fugu_page'] ?? 1));
        $per_page = 20;
        $args = [
            'post_type' => 'fugu_catalog_submission',
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        $q = new WP_Query($args);
        ?>
        <div class="fugu-catalog-table-wrap">
            <table class="fugu-catalog-table" style="width:100%;border-collapse:collapse">
                <thead>
                    <tr>
                        <th style="text-align:left;border-bottom:1px solid #ddd;padding:8px">Name</th>
                        <th style="text-align:left;border-bottom:1px solid #ddd;padding:8px">Email</th>
                        <th style="text-align:left;border-bottom:1px solid #ddd;padding:8px">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post();
                        $email = get_post_meta(get_the_ID(),'fugu_email',true);
                    ?>
                    <tr>
                        <td style="border-bottom:1px solid #eee;padding:8px"><?php the_title(); ?></td>
                        <td style="border-bottom:1px solid #eee;padding:8px"><?php echo esc_html($email); ?></td>
                        <td style="border-bottom:1px solid #eee;padding:8px"><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endwhile; wp_reset_postdata(); else: ?>
                    <tr><td colspan="3" style="padding:12px">No submissions yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}


