<?php
/**
 * Plugin Name: Custom Post Grid for Elementor
 * Description: A custom post grid widget with pagination for Elementor
 * Version: 1.0
 * Author: NF Tushar
 */

if (!defined('ABSPATH')) {
    exit; 
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
            echo '<div class="custom-post-grid-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<div>' . wp_kses_post(get_the_excerpt()) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>' . esc_html__('No more posts found.', 'custom-post-grid') . '</p>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
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
 