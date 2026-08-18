<?php
/**
 * Template fallback.
 *
 * @package Espanol
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<div class="video-grid" style="margin-top:16px">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( in_array( get_post_type(), espanol_video_types(), true ) ) {
				espanol_video_card();
			} else {
				?>
				<article class="video-card">
					<h3 class="vc-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</article>
				<?php
			}
		endwhile;
		?>
	</div>
	<?php espanol_pagination(); ?>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Nada encontrado.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
