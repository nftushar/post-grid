<?php
/**
 * Plugin Name: Custom Post Grid for Elementor
 * Description: A custom post grid widget with pagination for Elementor
 * Version: 1.0
 * Author: NF Tushar
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function register_custom_post_grid_widget() {
    require_once __DIR__ . '/widgets/custom-post-grid-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register(new \Custom_Post_Grid_Widget());
}
add_action('elementor/widgets/register', 'register_custom_post_grid_widget');


add_action('wp_ajax_custom_post_grid_load_more', 'custom_post_grid_load_more');
add_action('wp_ajax_nopriv_custom_post_grid_load_more', 'custom_post_grid_load_more');

function custom_post_grid_load_more() {
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $settings = isset($_POST['settings']) ? $_POST['settings'] : [];
    
    if (!is_array($settings) || empty($settings['post_type'])) {
        wp_send_json_error('Invalid settings data');
    }

    $args = [
        'post_type'      => sanitize_text_field($settings['post_type']),
        'posts_per_page' => intval($settings['posts_per_page']),
        'orderby'        => sanitize_text_field($settings['orderby']),
        'order'          => sanitize_text_field($settings['order']),
        'paged'          => $paged,
    ];

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Use the same rendering function as the initial load
            echo '<div class="post-grid-item">';
            
            if ('yes' === $settings['show_featured_image'] && has_post_thumbnail()) {
                echo '<div class="post-grid-image">';
                echo '<a href="' . get_the_permalink() . '">';
                the_post_thumbnail('large');
                echo '</a>';
                echo '</div>';
            }
            
            if ('yes' === $settings['show_title']) {
                echo '<h3 class="post-grid-title">';
                echo '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
                echo '</h3>';
            }
            
            if ('yes' === $settings['show_desc']) {
                echo '<div class="post-grid-excerpt">';
                echo wp_trim_words(get_the_excerpt(), 20, '...');
                echo '</div>';
            }
            
            if ('yes' === $settings['show_meta']) {
                echo '<div class="post-grid-meta">';
                
                if ('yes' === $settings['show_author']) {
                    echo '<span class="post-grid-author">' . get_the_author() . '</span>';
                }
                
                if ('yes' === $settings['show_date']) {
                    echo '<span class="post-grid-date">' . get_the_date() . '</span>';
                }
                
                if ('yes' === $settings['show_comments']) {
                    echo '<span class="post-grid-comments">' . get_comments_number() . ' comments</span>';
                }
                
                echo '</div>';
            }
            
            echo '<hr class="post-grid-divider">';
            echo '</div>';
        }
    } else {
        echo '<p>' . esc_html__('No more posts found.', 'custom-post-grid') . '</p>';
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'max_pages' => $query->max_num_pages
    ]);
}



function custom_post_grid_assets() {
    wp_enqueue_style(
        'custom-post-grid-css',
        plugins_url('widgets/assets/css/post-grid.css', __FILE__)
    );

    wp_enqueue_script(
        'custom-post-grid-js',
        plugins_url('widgets/assets/js/post-grid.js', __FILE__),
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script('custom-post-grid-js', 'custom_post_grid_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
    
    
}
add_action('wp_enqueue_scripts', 'custom_post_grid_assets');
 