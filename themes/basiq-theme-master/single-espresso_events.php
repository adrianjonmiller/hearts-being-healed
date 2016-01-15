<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

EE_Registry::instance()->load_helper( 'People_View' );

class TimberEventEspresso extends TimberPost {
	public function event() {
		return EEH_Event_View::get_event( $this->ID );
	}

	public function venue() {
		return EEH_Venue_View::get_event_venues( $this->ID );
	}

	public function people() {
		return EEH_People_View::get_people_for_event( $this->ID );
	}
}

// $post = Timber::query_post();
// $context['post'] = $post;
$context = Timber::get_context();
$context['post'] = new TimberEventEspresso();


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
