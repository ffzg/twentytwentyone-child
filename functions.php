<?php
/**
 * Twenty Twenty-One Child Theme Functions
 */

// --- 1. Enqueue Parent and Child Stylesheets ---
function twentytwentyone_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyone_child_enqueue_styles' );


// --- 2. Disable Block Editor for Widgets (Replaces the 'Classic Widgets' plugin) ---
function kompk_disable_block_widgets() {
    remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'kompk_disable_block_widgets' );


// --- 3. Register a Proper Sidebar Widget Area ---
function kompk_register_sidebar() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Main Sidebar', 'twentytwentyone-child' ),
            'id'            => 'main-sidebar',
            'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'twentytwentyone-child' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'kompk_register_sidebar' );


// --- 4. Your Existing Custom Menu Swapper for English/Croatian Content ---
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
