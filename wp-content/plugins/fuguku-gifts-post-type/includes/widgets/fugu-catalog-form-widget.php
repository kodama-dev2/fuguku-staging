<?php
if (!defined('ABSPATH')) { exit; }

class Fugu_Catalog_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'fugu-catalog-form'; }
    public function get_title() { return __('FUGU Catalog Form', 'fuguku-gift'); }
    public function get_icon() { return 'eicon-mail'; }
    public function get_categories() { return ['fuguku-gifts']; }

    protected function register_controls() {
        $this->start_controls_section('content', [
            'label' => __('Content', 'fuguku-gift'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('pdf_file', [
            'label' => __('Catalog PDF', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'media_types' => ['application/pdf'],
        ]);

        $this->add_control('email_subject', [
            'label' => __('Email Subject', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Your Catalog PDF', 'fuguku-gift'),
            'label_block' => true,
        ]);

        $this->add_control('email_body', [
            'label' => __('Email Body (HTML allowed)', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => __('Thank you! Please find your catalog PDF attached.', 'fuguku-gift'),
            'rows' => 5,
        ]);

        $this->add_control('button_text', [
            'label' => __('Button Text', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Get Catalog', 'fuguku-gift'),
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $pdf_id = intval($s['pdf_file']['id'] ?? 0);
        $pdf_url = esc_url($s['pdf_file']['url'] ?? '');
        $subject = esc_attr($s['email_subject'] ?? 'Your Catalog PDF');
        $body = wp_kses_post($s['email_body'] ?? '');
        $btn = esc_html($s['button_text'] ?? 'Get Catalog');
        $nonce = wp_create_nonce('fuguku_gifts_nonce');
        ?>
        <form class="fugu-catalog-form" method="post">
            <div class="fugu-field"><input type="text" name="name" placeholder="Your Name" required></div>
            <div class="fugu-field"><input type="email" name="email" placeholder="Your Email" required></div>
            <input type="hidden" name="action" value="fugu_catalog_submit">
            <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
            <input type="hidden" name="pdf_id" value="<?php echo esc_attr($pdf_id); ?>">
            <input type="hidden" name="pdf_url" value="<?php echo esc_url($pdf_url); ?>">
            <input type="hidden" name="email_subject" value="<?php echo $subject; ?>">
            <input type="hidden" name="email_body" value="<?php echo esc_attr($body); ?>">
            <button type="submit" class="fugu-btn"><?php echo $btn; ?></button>
            <div class="fugu-msg" style="margin-top:10px"></div>
        </form>
        <script>
        jQuery(function($){
          $('.fugu-catalog-form').on('submit', function(e){
            e.preventDefault();
            var $f = $(this), $btn = $f.find('button'), $msg = $f.find('.fugu-msg');
            $btn.prop('disabled', true).text('Sending...');
            $.post('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', $f.serialize())
             .done(function(resp){
               if(resp && resp.success){
                  $msg.text('Success! Please check your email.');
                  $f[0].reset();
               } else {
                  $msg.text(resp && resp.message ? resp.message : 'Failed.');
               }
             })
             .fail(function(){ $msg.text('Request failed.'); })
             .always(function(){ $btn.prop('disabled', false).text('<?php echo $btn; ?>'); });
          });
        });
        </script>
        <style>
        .fugu-catalog-form{max-width:640px}
        .fugu-catalog-form .fugu-field{margin-bottom:14px}
        .fugu-catalog-form input{width:100%;padding:14px 16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;color:#111;font-size:16px;line-height:1.4;transition:border-color .2s, box-shadow .2s}
        .fugu-catalog-form input::placeholder{color:#9ca3af}
        .fugu-catalog-form input:focus{outline:none;border-color:#222;box-shadow:0 0 0 3px rgba(34,34,34,.08)}
        .fugu-catalog-form .fugu-btn{display:inline-flex;align-items:center;justify-content:center;padding:12px 22px;border-radius:9999px;border:1px solid #111;background:#111;color:#fff;font-weight:600;letter-spacing:.2px;box-shadow:0 2px 8px rgba(0,0,0,.08);transition:background .2s, transform .05s}
        .fugu-catalog-form .fugu-btn:hover{background:#000}
        .fugu-catalog-form .fugu-btn:focus{outline:none;box-shadow:0 0 0 3px rgba(34,34,34,.08)}
        .fugu-catalog-form .fugu-btn:active{transform:translateY(1px)}
        .fugu-catalog-form .fugu-msg{font-size:14px;color:#111}
        </style>
        <?php
    }
}


