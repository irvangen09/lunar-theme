<?php
/**
 * Fallback template.
 *
 * @package Lunar
 */

get_header();
?>

<main id="primary" class="site-main">

	<?php if ( have_posts() ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-content">
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