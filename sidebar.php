<?php
/**
 * The sidebar containing the main widget area.
 * This file overrides the parent theme's sidebar.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Twenty_Twenty_One_Child
 */

// If the sidebar has no widgets, do nothing.
if ( ! is_active_sidebar( 'main-sidebar' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area">

    <!-- EXAMPLE: Adding custom hard-coded content -->
    <section id="custom-notice" class="widget widget_text">
        <h2 class="widget-title">Važna Obavijest</h2>
        <div class="textwidget">
            <p>Sve obavijesti o ispitima nalaze se na glavnoj stranici.</p>
        </div>
    </section>
    <!-- End of custom content -->

	<?php dynamic_sidebar( 'main-sidebar' ); // This line loads your widgets from the admin screen ?>
</aside><!-- #secondary -->

