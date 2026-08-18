<?php
/**
 * Template Name: Página de Categorias
 *
 * @package Espanol
 */

get_header();

$espanol_terms = get_terms(
	array(
		'taxonomy'   => espanol_tax( 'category' ),
		'hide_empty' => false,
		'orderby'    => 'name',
	)
);
?>

<h1 class="page-heading"><?php esc_html_e( 'Categorías Populares', 'espanol' ); ?> <?php espanol_the_icon( 'chev-r' ); ?></h1>

<?php if ( ! is_wp_error( $espanol_terms ) && $espanol_terms ) : ?>
	<div class="cat-grid">
		<?php foreach ( $espanol_terms as $espanol_term ) : ?>
			<a class="cat-card" href="<?php echo esc_url( get_term_link( $espanol_term ) ); ?>">
				<span class="cat-thumb">
					<?php $espanol_img = espanol_get_term_image( $espanol_term ); ?>
					<?php if ( $espanol_img ) : ?>
						<img src="<?php echo esc_url( $espanol_img ); ?>" alt="<?php echo esc_attr( $espanol_term->name ); ?>" loading="lazy">
					<?php endif; ?>
				</span>
				<span>
					<span class="cat-name"><?php echo esc_html( $espanol_term->name ); ?></span>
					<span class="cat-count" style="display:block"><?php echo (int) $espanol_term->count; ?> <?php esc_html_e( 'videos', 'espanol' ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Aún no hay categorías registradas.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
