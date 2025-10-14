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

        $this->add_control('from_name', [
            'label' => __('From Name (optional)', 'fuguku-gift'),
            'type'  => \Elementor\Controls_Manager::TEXT,
            'placeholder' => get_bloginfo('name'),
        ]);

        $this->add_control('from_email', [
            'label' => __('From Email (optional)', 'fuguku-gift'),
            'type'  => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'no-reply@' . parse_url(home_url(), PHP_URL_HOST),
            'description' => __('Gunakan alamat email domain Anda agar tidak masuk spam.', 'fuguku-gift'),
        ]);

        $this->add_control('button_text', [
            'label' => __('Button Text', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Get Catalog', 'fuguku-gift'),
        ]);

        $this->end_controls_section();

        // Style: Inputs
        $this->start_controls_section('style_inputs', [
            'label' => __('Inputs', 'fuguku-gift'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('input_text_color', [
            'label' => __('Text Color', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form input' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control('input_bg_color', [
            'label' => __('Background', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form input' => 'background: {{VALUE}};' ],
        ]);
        $this->add_control('input_border_color', [
            'label' => __('Border', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form input' => 'border-color: {{VALUE}};' ],
        ]);
        $this->add_control('input_radius', [
            'label' => __('Border Radius', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min'=>0,'max'=>40]],
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form input' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_control('input_padding', [
            'label' => __('Padding', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'input_typo',
            'selector' => '{{WRAPPER}} .fugu-catalog-form input',
        ]);
        $this->end_controls_section();

        // Style: Button
        $this->start_controls_section('style_button', [
            'label' => __('Button', 'fuguku-gift'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('btn_text_color', [
            'label' => __('Text Color', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form .fugu-btn' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control('btn_bg_color', [
            'label' => __('Background', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form .fugu-btn' => 'background: {{VALUE}}; border-color: {{VALUE}};' ],
        ]);
        $this->add_control('btn_radius', [
            'label' => __('Border Radius', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min'=>0,'max'=>60]],
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form .fugu-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_control('btn_padding', [
            'label' => __('Padding', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'selectors' => [ '{{WRAPPER}} .fugu-catalog-form .fugu-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'btn_typo',
            'selector' => '{{WRAPPER}} .fugu-catalog-form .fugu-btn',
        ]);
        $this->end_controls_section();

        // Helper options
        $this->start_controls_section('helper_opts', [
            'label' => __('Helper/Download', 'fuguku-gift'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('show_helper', [
            'label' => __('Show Helper Text', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('helper_text', [
            'label' => __('Helper Text', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'rows' => 2,
            'default' => __('Don’t see our email? Please check your Spam/Promotions tab.', 'fuguku-gift'),
            'condition' => [ 'show_helper' => 'yes' ],
        ]);
        $this->add_control('show_direct_link', [
            'label' => __('Show Direct Download Link', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [ 'show_helper' => 'yes' ],
        ]);
        $this->add_control('direct_link_text', [
            'label' => __('Direct Link Text', 'fuguku-gift'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('or click here to download the catalog', 'fuguku-gift'),
            'condition' => [ 'show_helper' => 'yes', 'show_direct_link' => 'yes' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $pdf_id = intval($s['pdf_file']['id'] ?? 0);
        $pdf_url = esc_url($s['pdf_file']['url'] ?? '');
        $subject = esc_attr($s['email_subject'] ?? 'Your Catalog PDF');
        $body = wp_kses_post($s['email_body'] ?? '');
        $from_name = esc_attr($s['from_name'] ?? '');
        $from_email = esc_attr($s['from_email'] ?? '');
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
            <input type="hidden" name="from_name" value="<?php echo $from_name; ?>">
            <input type="hidden" name="from_email" value="<?php echo $from_email; ?>">
            <button type="submit" class="fugu-btn"><?php echo $btn; ?></button>
            <div class="fugu-msg" style="margin-top:10px"></div>
        </form>
        <?php
        $show_helper = $s['show_helper'] ?? 'yes';
        $helper_text = $s['helper_text'] ?? '';
        $show_direct = $s['show_direct_link'] ?? 'yes';
        $link_text  = $s['direct_link_text'] ?? '';
        if ($show_helper === 'yes') : ?>
            <div class="fugu-help" style="margin-top:10px;font-size:14px;color:#4b5563">
                <?php echo wp_kses_post($helper_text ?: __('Don’t see our email? Please check your Spam/Promotions tab.', 'fuguku-gift')); ?>
                <?php if ($show_direct === 'yes' && $pdf_url) : ?>
                    &nbsp;<a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="nofollow noopener" style="text-decoration:underline;color:#111"><?php echo esc_html($link_text ?: __('or click here to download the catalog', 'fuguku-gift')); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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


