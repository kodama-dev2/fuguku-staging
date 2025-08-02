<?php
/**
 * Share Handler for Gifts
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Share Handler Class
 */
class Fuguku_Gifts_Share_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_fuguku_share_gift', array($this, 'share_gift'));
        add_action('wp_ajax_nopriv_fuguku_share_gift', array($this, 'share_gift'));
    }

    /**
     * Share Gift AJAX Handler
     */
    public function share_gift() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        $gift_id = intval($_POST['gift_id'] ?? 0);
        $platform = sanitize_text_field($_POST['platform'] ?? '');
        
        if (!$gift_id) {
            wp_send_json(array('success' => false, 'message' => 'Invalid gift ID'));
        }

        $gift_post = get_post($gift_id);
        if (!$gift_post || $gift_post->post_type !== 'gifts') {
            wp_send_json(array('success' => false, 'message' => 'Gift not found'));
        }

        $share_url = $this->get_share_url($gift_id, $platform);
        
        wp_send_json(array(
            'success' => true,
            'url' => $share_url,
            'message' => 'Share URL generated successfully',
        ));
    }

    /**
     * Get Share URL for Platform
     */
    private function get_share_url($gift_id, $platform) {
        $gift_url = get_permalink($gift_id);
        $gift_title = get_the_title($gift_id);
        $gift_excerpt = get_the_excerpt($gift_id);
        $gift_image = get_the_post_thumbnail_url($gift_id, 'medium');
        
        $share_text = $gift_title . ' - ' . $gift_excerpt;
        
        switch ($platform) {
            case 'facebook':
                return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($gift_url);
                
            case 'twitter':
                $twitter_text = substr($share_text, 0, 100) . '...';
                return 'https://twitter.com/intent/tweet?text=' . urlencode($twitter_text) . '&url=' . urlencode($gift_url);
                
            case 'whatsapp':
                $whatsapp_text = $share_text . ' ' . $gift_url;
                return 'https://wa.me/?text=' . urlencode($whatsapp_text);
                
            case 'telegram':
                $telegram_text = $share_text . ' ' . $gift_url;
                return 'https://t.me/share/url?url=' . urlencode($gift_url) . '&text=' . urlencode($share_text);
                
            case 'email':
                $email_subject = 'Check out this gift: ' . $gift_title;
                $email_body = $share_text . "\n\n" . $gift_url;
                return 'mailto:?subject=' . urlencode($email_subject) . '&body=' . urlencode($email_body);
                
            case 'copy':
                return $gift_url;
                
            default:
                return $gift_url;
        }
    }

    /**
     * Generate Share Buttons HTML
     */
    public static function get_share_buttons($gift_id) {
        $platforms = array(
            'facebook' => array(
                'name' => 'Facebook',
                'icon' => 'fa-facebook',
                'color' => '#1877f2'
            ),
            'twitter' => array(
                'name' => 'Twitter',
                'icon' => 'fa-twitter',
                'color' => '#1da1f2'
            ),
            'whatsapp' => array(
                'name' => 'WhatsApp',
                'icon' => 'fa-whatsapp',
                'color' => '#25d366'
            ),
            'telegram' => array(
                'name' => 'Telegram',
                'icon' => 'fa-telegram',
                'color' => '#0088cc'
            ),
            'email' => array(
                'name' => 'Email',
                'icon' => 'fa-envelope',
                'color' => '#6c757d'
            ),
            'copy' => array(
                'name' => 'Copy Link',
                'icon' => 'fa-link',
                'color' => '#6c757d'
            )
        );

        $html = '<div class="gift-share-buttons">';
        
        foreach ($platforms as $platform => $data) {
            $html .= sprintf(
                '<button class="gift-share-btn %s" data-platform="%s" data-gift-id="%d" title="Share on %s">
                    <i class="fa %s"></i>
                    <span>%s</span>
                </button>',
                esc_attr($platform),
                esc_attr($platform),
                $gift_id,
                esc_attr($data['name']),
                esc_attr($data['icon']),
                esc_html($data['name'])
            );
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Copy to Clipboard JavaScript
     */
    public static function get_copy_script() {
        return "
        <script>
        jQuery(document).ready(function($) {
            $('.gift-share-btn').on('click', function(e) {
                e.preventDefault();
                
                var platform = $(this).data('platform');
                var giftId = $(this).data('gift-id');
                var button = $(this);
                
                if (platform === 'copy') {
                    // Copy to clipboard
                    var giftUrl = window.location.href;
                    
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(giftUrl).then(function() {
                            showNotification('Link copied to clipboard!', 'success');
                        }).catch(function() {
                            // Fallback for older browsers
                            copyToClipboard(giftUrl);
                        });
                    } else {
                        // Fallback for older browsers
                        copyToClipboard(giftUrl);
                    }
                } else {
                    // Share via AJAX
                    $.ajax({
                        url: fuguku_gifts_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'fuguku_share_gift',
                            nonce: fuguku_gifts_ajax.nonce,
                            gift_id: giftId,
                            platform: platform
                        },
                        success: function(response) {
                            if (response.success) {
                                if (platform === 'email') {
                                    window.location.href = response.url;
                                } else {
                                    window.open(response.url, '_blank', 'width=600,height=400');
                                }
                            }
                        }
                    });
                }
            });
            
            function copyToClipboard(text) {
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('Link copied to clipboard!', 'success');
            }
            
            function showNotification(message, type) {
                var notification = $('<div class=\"gifts-notification ' + type + '\">' + message + '</div>');
                $('body').append(notification);
                
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
        </script>
        ";
    }
}

// Initialize share handler
new Fuguku_Gifts_Share_Handler(); 