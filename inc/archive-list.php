<?php
/**
 * Shared archive list item renderer — used by the Game archive and the
 * Author archive, which otherwise duplicated this markup verbatim.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Must be called inside the loop, after the_post().
 *
 * @param bool $show_game_suffix Whether to append "(Game Name)" after the
 *                                title — only relevant where an archive
 *                                can list posts spanning multiple games.
 */
function lunar_render_archive_list_item( bool $show_game_suffix = true ): void {
	$lunar_content_type_slug  = function_exists( 'lunar_wiki_get_taxonomy_slug_content_type' )
		? lunar_wiki_get_taxonomy_slug_content_type()
		: '';
	$lunar_content_type_terms = $lunar_content_type_slug
		? get_the_terms( get_the_ID(), $lunar_content_type_slug )
		: false;
	$lunar_article_game_term  = null;

	if ( $show_game_suffix && function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		$lunar_article_game_terms = get_the_terms( get_the_ID(), lunar_wiki_get_taxonomy_slug_game() );

		if ( is_array( $lunar_article_game_terms ) && ! empty( $lunar_article_game_terms ) ) {
			$lunar_article_game_term = $lunar_article_game_terms[0];
		}
	}
	?>
	<div class="lunar-archive-list-item">
		<?php if ( is_array( $lunar_content_type_terms ) && ! empty( $lunar_content_type_terms ) ) : ?>
			<span class="lunar-archive-list-item__badge">
				<?php echo esc_html( $lunar_content_type_terms[0]->name ); ?>
			</span>
		<?php endif; ?>
		<a class="lunar-archive-list-item__title" href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
			<?php if ( $lunar_article_game_term ) : ?>
				<span class="lunar-archive-list-item__game">(<?php echo esc_html( $lunar_article_game_term->name ); ?>)</span>
			<?php endif; ?>
		</a>
	</div>
	<?php
}