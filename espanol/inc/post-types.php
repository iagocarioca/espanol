<?php
/**
 * CPT Vídeo e taxonomias.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o post type "custom_post" (compatível com o tema-a99) e as taxonomias.
 * Se um plugin já registrar o custom_post (caso da produção), o tema não interfere.
 */
function espanol_register_post_types() {
	if ( ! post_type_exists( 'custom_post' ) ) {
		register_post_type(
			'custom_post',
			apply_filters(
				'espanol_custom_post_args',
				array(
					'labels'        => array(
						'name'          => __( 'Vídeos', 'espanol' ),
						'singular_name' => __( 'Vídeo', 'espanol' ),
						'add_new_item'  => __( 'Adicionar novo vídeo', 'espanol' ),
						'edit_item'     => __( 'Editar vídeo', 'espanol' ),
					),
					'public'        => true,
					'has_archive'   => 'videos',
					'menu_icon'     => 'dashicons-video-alt3',
					'menu_position' => 5,
					'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
					'rewrite'       => array( 'slug' => 'video' ),
					'show_in_rest'  => true,
				)
			)
		);
	}

	// Vídeos usam as taxonomias nativas category e post_tag (como no tema-a99).
	register_taxonomy_for_object_type( 'category', 'custom_post' );
	register_taxonomy_for_object_type( 'post_tag', 'custom_post' );

	register_taxonomy(
		'pornstar',
		espanol_video_types(),
		array(
			'labels'       => array(
				'name'          => __( 'Pornstars', 'espanol' ),
				'singular_name' => __( 'Pornstar', 'espanol' ),
			),
			'hierarchical' => false,
			'public'       => true,
			'rewrite'      => array( 'slug' => 'pornstar' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'channel',
		espanol_video_types(),
		array(
			'labels'       => array(
				'name'          => __( 'Canais', 'espanol' ),
				'singular_name' => __( 'Canal', 'espanol' ),
			),
			'hierarchical' => false,
			'public'       => true,
			'rewrite'      => array( 'slug' => 'canal' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'espanol_register_post_types' );
