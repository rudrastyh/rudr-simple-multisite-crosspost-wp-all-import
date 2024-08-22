<?php
/*
 * Plugin name: Simple Multisite Crossposting - WP All Import
 * Author: Misha Rudrastyh
 * Author URI: https://rudrastyh.com
 * Description: Allows to automatically crosspost posts when running the import in WP All Import
 * Plugin URI: https://rudrastyh.com/support/wp-all-import-crossposting
 * Version: 1.0
 * Network: true
 */

add_action( 'pmxi_saved_post', function( $post_id, $xml_node, $is_update ) {

	if( ! class_exists( 'Rudr_Simple_Multisite_Crosspost' ) ) {
		return;
	}

	//file_put_contents( __DIR__ . '/log.txt', $post_id, FILE_APPEND );

	$post = get_post( $post_id );
	if( ! $post ) {
		return;
	}

	$allowed_post_statuses = ( $allowed_post_statuses = get_site_option( 'rudr_smc_post_statuses' ) ) ? $allowed_post_statuses : array( 'publish' );
	if( ! in_array( $post->post_status, $allowed_post_statuses ) ) {
		return;
	}

	if( function_exists( 'wc_get_product' ) && 'product' === $post->post_type ) {
		$c = new Rudr_Simple_Multisite_Woo_Crosspost();
	} else {
		$c = new Rudr_Simple_Multisite_Crosspost();
	}

	$blog_ids = $c->get_blog_ids_for_object( $post_id );

	if( function_exists( 'wc_get_product' ) && 'product' === $post->post_type ) {
		$c->crosspost_product( $post_id, $blog_ids );
	} else {
		$c->crosspost( $post, $blog_ids );
	}

}, 999, 3 );
