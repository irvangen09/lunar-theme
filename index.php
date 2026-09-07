<?php
/**
 * Fallback template.
 *
 * @package Lunar
 */

get_header();
?>

<main id="main-content" class="lunar-archive">

	<header class="lunar-archive__header">
		<h1><?php the_archive_title(); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="lunar-archive__excerpt">
					<?php the_excerpt(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>

	<?php else : ?>

		<p><?php esc_html_e( 'Nothing found.', 'lunar' ); ?></p>

	<?php endif; ?>

</main>

<?php
get_footer();