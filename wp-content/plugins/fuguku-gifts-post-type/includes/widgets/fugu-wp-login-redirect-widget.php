<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fugu_WP_Login_Redirect_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'fugu-wp-login-redirect';
    }

    public function get_title() {
        return __('FUGU WP Login Redirect', 'fuguku-gift');
    }

    public function get_icon() {
        return 'eicon-lock-user';
    }

    public function get_categories() {
        return ['fuguku-gifts'];
    }

    public function get_keywords() {
        return ['login', 'register', 'wp login', 'account'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_remember',
            [
                'label' => __('Show Remember Me', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_lost_password',
            [
                'label' => __('Show Lost Password', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'register_label',
            [
                'label' => __('Register Label', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Register', 'fuguku-gift'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'register_url',
            [
                'label' => __('Register URL', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => home_url('/my-account/'),
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $wrapper_id = 'fugu-login-' . esc_attr($this->get_id());
        $register_label = !empty($settings['register_label']) ? $settings['register_label'] : __('Register', 'fuguku-gift');
        $register_url = !empty($settings['register_url']['url']) ? $settings['register_url']['url'] : home_url('/my-account/');

        $login_args = [
            'echo' => false,
            'remember' => ('yes' === ($settings['show_remember'] ?? 'yes')),
            'form_id' => $wrapper_id . '-form',
            'id_username' => $wrapper_id . '-user-login',
            'id_password' => $wrapper_id . '-user-pass',
            'id_remember' => $wrapper_id . '-rememberme',
            'id_submit' => $wrapper_id . '-wp-submit',
            'label_username' => __('Username or Email Address', 'fuguku-gift'),
            'label_password' => __('Password', 'fuguku-gift'),
            'label_remember' => __('Remember Me', 'fuguku-gift'),
            'label_log_in' => __('Log In', 'fuguku-gift'),
            'value_remember' => true,
        ];

        echo '<div class="fugu-wp-login-redirect-widget" id="' . esc_attr($wrapper_id) . '">';
        echo wp_login_form($login_args);
        echo '<p class="fugu-login-links">';

        if ('yes' === ($settings['show_lost_password'] ?? 'yes')) {
            echo '<a class="fugu-lost-password" href="' . esc_url(wp_lostpassword_url()) . '">' . esc_html__('Lost your password?', 'fuguku-gift') . '</a>';
            echo ' | ';
        }

        $register_attrs = '';
        if (!empty($settings['register_url']['is_external'])) {
            $register_attrs .= ' target="_blank"';
        }
        if (!empty($settings['register_url']['nofollow'])) {
            $register_attrs .= ' rel="nofollow"';
        }

        echo '<a class="fugu-register-link" href="' . esc_url($register_url) . '"' . $register_attrs . '>' . esc_html($register_label) . '</a>';
        echo '</p>';
        echo '</div>';
    }
}
