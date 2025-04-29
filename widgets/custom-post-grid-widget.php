<?php
class Custom_Post_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom_post_grid';
    }

    public function get_title() {
        return __('NF Custom Post Grid', 'custom-post-grid');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'custom-post-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => __('Post Type', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_post_types(),
                'default' => 'post',
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'date' => __('Date', 'custom-post-grid'),
                    'title' => __('Title', 'custom-post-grid'),
                    'rand' => __('Random', 'custom-post-grid'),
                    'comment_count' => __('Comment Count', 'custom-post-grid'),
                ],
                'default' => 'date',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'ASC' => __('Ascending', 'custom-post-grid'),
                    'DESC' => __('Descending', 'custom-post-grid'),
                ],
                'default' => 'DESC',
            ]
        );

        $this->add_control(
            'show_featured_image',
            [
                'label' => __('Show Featured Image', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Show Title', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label' => __('Show Meta', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => __('Show Date', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => __('Show Author', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_comments',
            [
                'label' => __('Show Comments', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'custom-post-grid'),
                'label_off' => __('Hide', 'custom-post-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'custom-post-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-grid-title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => __('Meta Color', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-grid-meta' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label' => __('Image Height', 'custom-post-grid'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-grid-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
                'condition' => [
                    'show_featured_image' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => $settings['post_type'],
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'paged' => 1,
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<div class="custom-post-grid-wrapper" data-settings=\'' . wp_json_encode($settings) . '\' data-page="1">';
            echo '<div class="custom-post-grid">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_post_item($settings);
            }

            echo '</div>'; // .custom-post-grid

            if ($query->max_num_pages > 1) {
                echo '<button class="post-grid-load-more" data-max-pages="' . esc_attr($query->max_num_pages) . '">' . __('Load More', 'custom-post-grid') . '</button>';
            }

            echo '</div>'; // .custom-post-grid-wrapper
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No posts found.', 'custom-post-grid') . '</p>';
        }
    }

    protected function render_post_item($settings) {
        ?>
        <div class="post-grid-item">
            <?php if ('yes' === $settings['show_featured_image'] && has_post_thumbnail()) : ?>
                <div class="post-grid-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('large'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="post-grid-category">
                <?php 
                $categories = get_the_category();
                if (!empty($categories)) {
                    echo esc_html($categories[0]->name);
                }
                ?>
            </div>
            
            <?php if ('yes' === $settings['show_title']) : ?>
                <h3 class="post-grid-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
            <?php endif; ?>
            
            <?php if ('yes' === $settings['show_meta']) : ?>
                <div class="post-grid-meta">
                    <?php if ('yes' === $settings['show_author']) : ?>
                        <span class="post-grid-author"><?php the_author(); ?></span>
                    <?php endif; ?>
                    
                    <?php if ('yes' === $settings['show_date']) : ?>
                        <span class="post-grid-date"><?php echo get_the_date(); ?></span>
                    <?php endif; ?>
                    
                    <?php if ('yes' === $settings['show_comments']) : ?>
                        <span class="post-grid-comments"><?php echo get_comments_number(); ?> comments</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="post-grid-excerpt">
                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
            </div>
            <hr class="post-grid-divider">
        </div>
        <?php
    }

    private function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }
        return $options;
    }
}