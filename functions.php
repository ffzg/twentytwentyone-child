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


/**
 * Enable Categories for Media Library items (Attachments).
 * This is necessary for the featured documents migration.
 */
function kompk_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
add_action( 'init', 'kompk_add_categories_to_attachments' );



/**
 * A custom widget to display attachments from a specific category.
 */
class Kompk_Featured_Docs_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'kompk_featured_docs_widget',
            'Istaknuti Dokumenti (Kompk)',
            array( 'description' => 'Prikazuje listu dokumenata iz kategorije "Istaknuti Dokumenti".' ) 
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $query_args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
	    'posts_per_page' => 20, // Show up to 10 documents
	    'orderby'        => 'ID',      // --- FIX: Sort by the Post ID ---
	    'order'          => 'DESC',    // --- FIX: In descending order ---
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => 'istaknuti-dokumenti',
                ),
            ),
        );
        $attachments = new WP_Query( $query_args );

        if ( $attachments->have_posts() ) {
            echo '<ul>';
            while ( $attachments->have_posts() ) {
                $attachments->the_post();
                //echo '<li><a href="' . esc_url( wp_get_attachment_url() ) . '">' . get_the_title() . '</a></li>';
                // --- START OF NEW LOGIC ---
                $file_url = wp_get_attachment_url();
                // Get the file extension (e.g., 'pdf', 'docx')
                $file_extension = strtolower( pathinfo( $file_url, PATHINFO_EXTENSION ) );
    
                // Normalize Word extensions
                if ( 'docx' === $file_extension ) {
                    $file_extension = 'doc';
                }
                
                // Create a specific CSS class for the icon
                $icon_class = 'doc-icon doc-icon-' . $file_extension;
                // --- END OF NEW LOGIC ---
    
                // Modified output to include a span with the icon class
                echo '<li><span class="' . esc_attr( $icon_class ) . '"></span><a href="' . esc_url( $file_url ) . '">' . get_the_title() . '</a></li>';

            }
            echo '</ul>';
        } else {
            echo '<p>Nema istaknutih dokumenata.</p>';
        }
        wp_reset_postdata();

        echo $args['after_widget'];
    }
    
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Istaknuti Dokumenti';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Naslov:</label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

function register_kompk_widget() {
    register_widget( 'Kompk_Featured_Docs_Widget' );
}
add_action( 'widgets_init', 'register_kompk_widget' );



/**
 * Add "Categories" column to the Media Library list view
 * to make managing featured documents easier.
 */
function kompk_add_media_category_column( $columns ) {
    $columns['categories'] = 'Kategorije';
    return $columns;
}
add_filter( 'manage_media_columns', 'kompk_add_media_category_column' );

/**
 * Display the category links in the new column.
 */
function kompk_media_category_column_content( $column_name, $id ) {
    if( 'categories' == $column_name ) {
        $terms = get_the_terms( $id, 'category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term_links = array();
            foreach ( $terms as $term ) {
                $term_links[] = $term->name;
            }
            echo implode( ', ', $term_links );
        }
    }
}
add_action( 'manage_media_custom_column', 'kompk_media_category_column_content', 10, 2 );

/**
 * Make the new "Categories" column sortable.
 */
function kompk_make_media_category_column_sortable( $columns ) {
    $columns['categories'] = 'categories';
    return $columns;
}
add_filter( 'manage_upload_sortable_columns', 'kompk_make_media_category_column_sortable' );


/**
 * ===================================================================
 * Add Media Library Filtering by Category for Featured Documents
 * ===================================================================
 */

/**
 * Action 1: Add a category filter dropdown to the Media Library admin page.
 */
function kompk_add_media_category_filter() {
    global $pagenow;
    // Only show this on the main Media Library page (upload.php)
    if ( 'upload.php' === $pagenow ) {
        $dropdown_options = array(
            'show_option_all' => 'Sve kategorije',
            'taxonomy'        => 'category',
            'name'            => 'cat',
            'orderby'         => 'name',
            'selected'        => ( isset($_GET['cat']) ? $_GET['cat'] : 0 ),
            'hierarchical'    => true,
            'show_count'      => true,
            'hide_empty'      => false, // Show our category even if it has 0 items
        );
        wp_dropdown_categories( $dropdown_options );
    }
}
add_action( 'restrict_manage_posts', 'kompk_add_media_category_filter' );

/**
 * Action 2: Process the filter when the user clicks the "Filter" button.
 */
function kompk_filter_media_by_category_query( $query ) {
    global $pagenow;
    // Check this is the main query on the upload.php admin page
    if ( is_admin() && $query->is_main_query() && 'upload.php' === $pagenow ) {
        $category_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
        
        if ( $category_id > 0 ) {
            $query->set( 'cat', $category_id );
        }
    }
}
add_action( 'pre_get_posts', 'kompk_filter_media_by_category_query' );

