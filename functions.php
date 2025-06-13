<?php
/**
 * Twenty Twenty-One Child Theme Functions
 */

// Enqueue parent theme's stylesheet
function twentytwentyone_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyone_child_enqueue_styles' );


/**
 * Custom Menu Swapper for Kompk Migration
 *
 * This function hooks into the 'wp_nav_menu_args' filter.
 * It checks if the page being displayed uses our custom English template.
 * If it does, it forces WordPress to use the 'Main Menu' instead of the default.
 */
function kompk_swap_english_menu( $args ) {
    // We only want to modify the 'primary' menu location.
    if ( isset($args['theme_location']) && $args['theme_location'] === 'primary' ) {
        
        // Check if we are on a page that is using our custom template.
        if ( is_page_template( 'template-english-section.php' ) ) {
            // If so, override the menu argument to use the English menu.
            $args['menu'] = 'Main Menu';
        }
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'kompk_swap_english_menu' );
