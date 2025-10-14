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
            // Show all statuses because front-end inserts may become draft/pending if user is not logged in
            'post_status' => ['publish','pending','draft'],
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        $q = new WP_Query($args);
        ?>
        <div class="fugu-catalog-table-wrap" style="max-width:960px">
            <table class="fugu-catalog-table" style="width:100%;border-collapse:separate;border-spacing:0;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
                <thead>
                    <tr style="background:#f9fafb">
                        <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:12px 14px;font-weight:600;color:#111">Name</th>
                        <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:12px 14px;font-weight:600;color:#111">Email</th>
                        <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:12px 14px;font-weight:600;color:#111">Date</th>
                        <th style="text-align:left;border-bottom:1px solid #e5e7eb;padding:12px 14px;font-weight:600;color:#111">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post();
                        $email = get_post_meta(get_the_ID(),'fugu_email',true);
                    ?>
                    <tr>
                        <td style="border-bottom:1px solid #f3f4f6;padding:12px 14px"><?php the_title(); ?></td>
                        <td style="border-bottom:1px solid #f3f4f6;padding:12px 14px"><?php echo esc_html($email); ?></td>
                        <td style="border-bottom:1px solid #f3f4f6;padding:12px 14px"><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                        <td style="border-bottom:1px solid #f3f4f6;padding:12px 14px">
                            <?php $sent = get_post_meta(get_the_ID(), 'fugu_email_sent', true);
                            echo $sent ? '<span style="color:#16a34a">Sent</span>' : '<span style="color:#dc2626">Failed</span>'; ?>
                        </td>
                    </tr>
                    <?php endwhile; wp_reset_postdata(); else: ?>
                    <tr><td colspan="4" style="padding:12px">No submissions yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}


