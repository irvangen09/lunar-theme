<?php
/**
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lunar_author_id = get_queried_object_id();

lunar_breadcrumb();
?>

<main id="main-content" class="lunar-archive">

	<header class="lunar-archive__header lunar-archive__header--author">
		<?php lunar_render_author_box( $lunar_author_id, true ); ?>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="lunar-archive-list">
			<?php
			while ( have_posts() ) :
				the_post();
				lunar_render_archive_list_item();
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Belum ada artikel dari penulis ini.', 'lunar' ); ?></p>

	<?php endif; ?>

</main>

<?php
get_footer();