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
        $home_url = home_url('/');
        $logout_url = wp_logout_url($home_url);

        echo '<div class="fugu-wp-login-redirect-widget" id="' . esc_attr($wrapper_id) . '">';

        if (is_user_logged_in()) {
            echo '<div class="fugu-login-state">';
            echo '<p class="fugu-login-state-text">' . esc_html__('You already login', 'fuguku-gift') . '</p>';
            echo '<a class="fugu-auth-button fugu-logout-button" href="' . esc_url($logout_url) . '">' . esc_html__('Log Out', 'fuguku-gift') . '</a>';
            echo '</div>';
        } else {
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
                'redirect' => $home_url,
            ];

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
        }

        echo '</div>';
        ?>
        <style>
            #<?php echo esc_attr($wrapper_id); ?> .login-username,
            #<?php echo esc_attr($wrapper_id); ?> .login-password,
            #<?php echo esc_attr($wrapper_id); ?> .login-remember,
            #<?php echo esc_attr($wrapper_id); ?> .login-submit {
                margin: 0 0 18px;
            }

            #<?php echo esc_attr($wrapper_id); ?> label {
                display: block;
                margin-bottom: 8px;
                color: #2f2f32;
                font-size: 22px;
                font-weight: 500;
                line-height: 1.2;
            }

            #<?php echo esc_attr($wrapper_id); ?> input[type="text"],
            #<?php echo esc_attr($wrapper_id); ?> input[type="email"],
            #<?php echo esc_attr($wrapper_id); ?> input[type="password"] {
                width: 100%;
                min-height: 54px;
                border: 1px solid #d7d7dc;
                border-radius: 8px;
                background: #fff;
                padding: 12px 14px;
                font-size: 18px;
                color: #1f1f22;
                box-sizing: border-box;
            }

            #<?php echo esc_attr($wrapper_id); ?> input[type="text"]:focus,
            #<?php echo esc_attr($wrapper_id); ?> input[type="email"]:focus,
            #<?php echo esc_attr($wrapper_id); ?> input[type="password"]:focus {
                outline: none;
                border-color: #b8b8bf;
            }

            #<?php echo esc_attr($wrapper_id); ?> .login-remember label {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 20px;
                margin: 0;
                cursor: pointer;
            }

            #<?php echo esc_attr($wrapper_id); ?> .login-remember input[type="checkbox"] {
                width: 22px;
                height: 22px;
                margin: 0;
            }

            #<?php echo esc_attr($wrapper_id); ?> .login-submit {
                margin-top: 12px;
            }

            #<?php echo esc_attr($wrapper_id); ?> .button,
            #<?php echo esc_attr($wrapper_id); ?> .fugu-auth-button {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                min-width: 190px;
                min-height: 68px;
                border: 2px solid #141417;
                border-radius: 999px;
                background: #fff;
                color: #141417;
                font-size: 42px;
                font-weight: 500;
                line-height: 1;
                text-decoration: none;
                padding: 0 28px;
                cursor: pointer;
                transition: all .2s ease;
            }

            #<?php echo esc_attr($wrapper_id); ?> .button:hover,
            #<?php echo esc_attr($wrapper_id); ?> .fugu-auth-button:hover {
                background: #141417;
                color: #fff;
            }

            #<?php echo esc_attr($wrapper_id); ?> .fugu-login-links {
                margin: 18px 0 0;
                font-size: 16px;
                color: #3a3a3d;
            }

            #<?php echo esc_attr($wrapper_id); ?> .fugu-login-links a {
                color: #2f2f32;
                text-decoration: none;
            }

            #<?php echo esc_attr($wrapper_id); ?> .fugu-login-links a:hover {
                text-decoration: underline;
            }

            #<?php echo esc_attr($wrapper_id); ?> .fugu-login-state-text {
                margin: 0 0 14px;
                font-size: 24px;
                color: #2f2f32;
            }
        </style>
        <?php
    }
}
