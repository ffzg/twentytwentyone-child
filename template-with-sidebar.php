<?php
/**
 * Template Name: Page with Sidebar
 *
 * This template is used for pages that should display the main sidebar.
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        // Start the standard WordPress Loop to display page content.
        while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/content/content-page' );

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }

        endwhile; // End the loop.
        ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php
// This is the crucial line that calls the sidebar
get_sidebar();
?>

<?php
get_footer();
