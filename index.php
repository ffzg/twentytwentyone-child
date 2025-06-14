<?php
get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/content', get_theme_mod( 'display_excerpt_or_full_post', 'excerpt' ) );
	}
	twenty_twenty_one_the_posts_navigation();
} else {
	get_template_part( 'template-parts/content/content-none' );
}
get_sidebar();
get_footer();
