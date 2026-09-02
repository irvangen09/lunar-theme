<?php
/**
 * Wiki Article template. Selected via the single_template filter
 * (inc/single-template.php), not automatic file-matching.
 *
 * Sidebar markup comes before content because the layout collapses to
 * a single column on mobile, where source order drives visual order.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

get_header();

while ( have_posts() ) :
	the_post();

	lunar_breadcrumb();

	$lunar_content_type_slug  = function_exists( 'lunar_wiki_get_taxonomy_slug_content_type' )
		? lunar_wiki_get_taxonomy_slug_content_type()
		: '';
	$lunar_content_type_terms = $lunar_content_type_slug
		? get_the_terms( get_the_ID(), $lunar_content_type_slug )
		: false;

	$lunar_update_notes      = function_exists( 'lunar_wiki_get_update_notes_meta_key' )
		? get_post_meta( get_the_ID(), lunar_wiki_get_update_notes_meta_key(), true )
		: '';
	$lunar_update_notes_list = array();

	if ( ! empty( trim( (string) $lunar_update_notes ) ) ) {
		$lunar_update_notes_list = array_filter( array_map( 'trim', explode( "\n", $lunar_update_notes ) ) );
	}

	$lunar_article_layout = lunar_get_article_layout();
	?>

	<main id="main-content" class="lunar-article">
		<article <?php post_class( 'lunar-article__entry' ); ?> id="post-<?php the_ID(); ?>">

			<?php if ( is_array( $lunar_content_type_terms ) && ! empty( $lunar_content_type_terms ) ) : ?>
				<span class="lunar-badge lunar-badge--category">
					<?php echo esc_html( $lunar_content_type_terms[0]->name ); ?>
				</span>
			<?php endif; ?>

			<h1 class="lunar-article__title"><?php the_title(); ?></h1>

			<?php lunar_render_byline(); ?>

			<?php if ( has_excerpt() ) : ?>
				<p class="lunar-article__tagline"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<div class="lunar-article__layout">
				<?php if ( $lunar_article_layout['has_infobox'] ) : ?>
					<aside class="lunar-article__sidebar">
						<?php echo $lunar_article_layout['infobox_html']; ?>
					</aside>
				<?php endif; ?>

				<div class="lunar-article__content">
					<?php echo $lunar_article_layout['content_html']; ?>
				</div>
			</div>

			<?php lunar_render_author_box(); ?>

			<footer class="lunar-article__meta">
				<p class="lunar-article__updated">
					<?php
					printf(
						/* translators: %s: last modified date */
						esc_html__( 'Terakhir diperbarui: %s', 'lunar' ),
						esc_html( get_the_modified_date() )
					);
					?>
				</p>

				<?php if ( ! empty( $lunar_update_notes_list ) ) : ?>
					<ul class="lunar-article__update-notes">
						<?php foreach ( $lunar_update_notes_list as $lunar_note_line ) : ?>
							<li><?php echo esc_html( $lunar_note_line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</footer>

		</article>
	</main>

	<?php
endwhile;

get_footer();