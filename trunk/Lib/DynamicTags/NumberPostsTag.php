<?php

namespace DynamicConditions\Lib\DynamicTags;

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicTags\Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

Class NumberPostsTag extends \Elementor\Core\DynamicTags\Tag {
    const WRAPPED_TAG = false;
    public static $dtCount = 0;

    public function get_name() {
        return 'numberposts';
    }

    public function get_title() {
        return __( 'Number posts', 'dynamicconditions' );
    }

    public function get_group() {
        return [ Module::POST_GROUP ];
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function register_controls() {

        $this->add_control(
            'category',
            [
                'label' => __( 'Category', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => [],
                'options' => $this->getCategories(),
                'multiple' => true,
            ]
        );
        $this->add_control(
            'posttypes',
            [
                'label' => __( 'Post-Types', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => [],
                'options' => get_post_types(),
                'multiple' => true,
            ]
        );
    }

    /**
     * Get a list of all categories
     *
     * @return array
     */
    private function getCategories() {
        $result = [];
        foreach ( get_categories() as $category ) {
            $result[$category->term_id] = $category->name;
        }

        return $result;
    }


    /**
     * Print the number of posts in category/post-type
     */
    public function render() {
        $settings = $this->get_settings();
        $posts = get_posts( [
            'category' => implode( ',', $settings['category'] ),
            'post_type' => empty( $settings['posttypes'] ) ? 'any' : $settings['posttypes'],
            'numberposts' => -1,
            'posts_per_page' => -1,
            'fields' => 'ids',
        ] );

        echo count( $posts );
    }
}