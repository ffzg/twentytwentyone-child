<?php
/**
 * Template Name: English Section Template
 *
 * This template is used as a marker for all English-language pages.
 * The actual menu swapping logic is handled by a filter function
 * in the child theme's functions.php file, which checks for this template.
 *
 * It simply loads the standard header, footer, and the default page content loop.
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
get_footer();