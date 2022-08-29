<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_action;
use function add_filter;
use function add_query_arg;
use function admin_url;
use function esc_attr;
use function esc_html;
use function esc_url;
use function get_permalink;
use function is_admin;
use function wp_nonce_field;

add_filter( 'render_block_blockify/form', NS . 'render_form_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_form_block( string $content, array $block ): string {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return $content;
	}

	if ( is_admin() || ! $content ) {
		return $content;
	}

	global $post;

	$dom = dom( $content );

	/**
	 * @var DOMElement $form
	 */
	$form = $dom->getElementsByTagName( 'form' )->item( 0 );

	if ( ! $form ) {
		return $content;
	}

	$form->setAttribute( 'method', 'POST' );
	$form->setAttribute( 'action', esc_url( admin_url( 'admin-post.php' ) ) );

	$fragment = $form->ownerDocument->createDocumentFragment();

	$hidden_fields = [
		'action'          => 'blockify_form',
		'success_message' => $block['attrs']['successMessage'] ?? __( 'Thank you for subscribing!', 'blockify' ),
		'redirect_page'   => $block['attrs']['redirectPage'] ?? $post->ID ?? false,
		'insert_user'     => $block['attrs']['insertUser'] ?? true,
	];

	foreach ( $hidden_fields as $name => $value ) {
		if ( $value ) {
			$field = $dom->createElement( 'input' );
			$field->setAttribute( 'type', 'hidden' );
			$field->setAttribute( 'name', $name );
			$field->setAttribute( 'value', (string) $value );
			$fragment->appendChild( $field );
		}
	}

	$fragment->appendXML( wp_nonce_field( 'blockify_form', 'nonce' ) );
	$form->insertBefore( $fragment, $form->firstChild );

	$content = $dom->saveHTML();

	if ( isset( $_GET['error_message'] ) ) {
		$content = '<div class="blockify-error">' . esc_attr( $_GET['error_message'] ) . '</div>' . $content;
	}

	if ( isset( $_GET['success_message'] ) ) {
		$content = '<div class="blockify-success">' . esc_attr( $_GET['success_message'] ) . '</div>';
	}

	return $content;
}

//add_filter( 'render_block_blockify/text-area', NS . 'render_text_area_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.2.0
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_text_area_block( string $content, array $block ): string {
	if ( ! $content) {
		return '';
	}

	$dom = dom( $content );

	/* @var \DOMElement $first_child */
	$first_child = $dom->firstChild;

	/* @var \DOMElement $text_area */
	$text_area   = $first_child->getElementsByTagName( 'textarea' )->item( 0 );

	if ($block['attrs']['minHeight'] ?? false) {
		$text_area->setAttribute(
			'style',
			$text_area->getAttribute('style') . ';min-height:' . $block['attrs']['minHeight']
		);
	}

	return $dom->saveHTML();
}

add_action( 'admin_post_blockify_form', NS . 'handle_form_submission' );
/**
 * Handles form submissions (creates new WordPress subscriber).
 *
 * @since 0.0.1
 *
 * @return void
 * @todo  Add mailchimp support.
 *
 */
function handle_form_submission(): void {
	$referer = isset( $_POST['_wp_http_referer'] ) ? esc_url( $_POST['_wp_http_referer'] ) : '';

	$redirect_page = home_url( $referer );

	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'blockify_form' ) ) {
		wp_safe_redirect( add_query_arg(
			[ 'error_message' => __( 'Invalid nonce.', 'blockify' ) ],
			$redirect_page
		) );

		die;
	}

	status_header( 200 );

	if ( isset( $_POST['user_email'] ) && ! apply_filters( 'blockify_return_if_no_email', true ) ) {
		return;
	}

	$user_pass       = $_POST['user_pass'] ?? wp_generate_password();
	$user_url        = $_POST['user_url'] ?? ' ';
	$user_email      = $_POST['user_email'] ?? ' ';
	$first_name      = $_POST['first_name'] ?? ' ';
	$last_name       = $_POST['last_name'] ?? ' ';
	$description     = $_POST['description'] ?? ' ';
	$agreement       = $_POST['checkbox'] ?? ' ';
	$insert_user     = $_POST['insert_user'] ?? ' ';
	$success_message = $_POST['success_message'] ?? ' ';

	// Sanitized by wp_insert_user.
	$user_data = [
		'user_pass'   => $user_pass,
		'user_url'    => $user_url,
		'user_email'  => $user_email,
		'first_name'  => $first_name,
		'last_name'   => $last_name,
		'description' => $description,

		// Generated.
		'user_login'  => $user_email,
	];

	foreach ( $user_data as $key => $value ) {
		if ( ! $value ) {
			unset( $user_data[ $key ] );
		}
	}

	if ( isset( $_POST['redirect_page'] ) && (int) $_POST['redirect_page'] ) {
		$redirect_page = get_permalink( (int) $_POST['redirect_page'] );
	}

	if ( $insert_user && ! get_user_by( 'email', $user_email ) && isset( $user_data['user_login'] ) ) {
		$user = wp_insert_user( apply_filters( SLUG . 'insert_user_data', $user_data ) );

		if ( $agreement ) {
			add_user_meta( $user, 'agreement', $agreement, true );
		}

		wp_safe_redirect( add_query_arg(
			[ 'success_message' => esc_html( $success_message ) ],
			$redirect_page
		) );

	} else {
		wp_safe_redirect( add_query_arg(
			[ 'error_message' => __( 'User with email already exists.', 'blockify' ) ],
			$redirect_page
		) );
	}

	die;
}
