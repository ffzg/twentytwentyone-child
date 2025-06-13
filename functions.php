<?php
/**
 * Twenty Twenty-One Child Theme Functions
 */

function twentytwentyone_child_theme_setup() {
    // 1. Add support for a custom logo.
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 60,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // 2. Register our new utility menu for the header.
    register_nav_menus( array(
        'utility' => esc_html__( 'Utility Menu', 'twentytwentyone-child' ),
    ) );
}
add_action( 'after_setup_theme', 'twentytwentyone_child_theme_setup' );

// Enqueue parent and child stylesheets correctly
function twentytwentyone_child_enqueue_styles() {
    $parent_style_handle = 'twenty-twenty-one-style';

    wp_enqueue_style( $parent_style_handle, get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'twentytwentyone-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style_handle ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyone_child_enqueue_styles', 20 );

/**
 * Custom Menu Swapper for Kompk Migration
 */
function kompk_swap_english_menu( $args ) {
    if ( isset($args['theme_location']) && $args['theme_location'] === 'primary' ) {
        if ( is_page_template( 'template-english-section.php' ) ) {
            $args['menu'] = 'Main Menu';
        }
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'kompk_swap_english_menu' );