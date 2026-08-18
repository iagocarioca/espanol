<?php
/**
 * Template padrão de páginas (conteúdo legal, institucionais).
 *
 * @package Espanol
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="page-content">
		<h1 class="page-heading"><?php the_title(); ?></h1>
		<div class="page-body"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
