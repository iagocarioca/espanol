<?php
/**
 * Template Name: Página de Tags
 *
 * @package Espanol
 */

get_header();

$espanol_terms = get_terms(
	array(
		'taxonomy'   => espanol_tax( 'tag' ),
		'hide_empty' => false,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 300,
	)
);
?>

<h1 class="page-heading"><?php espanol_the_icon( 'tag' ); ?> <?php esc_html_e( 'Tags', 'espanol' ); ?></h1>

<?php if ( ! is_wp_error( $espanol_terms ) && $espanol_terms ) : ?>
	<div class="chips-row" style="justify-content:flex-start;margin-top:14px">
		<?php foreach ( $espanol_terms as $espanol_term ) : ?>
			<a class="chip" href="<?php echo esc_url( get_term_link( $espanol_term ) ); ?>"><?php echo esc_html( $espanol_term->name ); ?> <span style="color:var(--muted)">(<?php echo (int) $espanol_term->count; ?>)</span></a>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Aún no hay tags registradas.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
