<?php
/**
 * Register Page Functions
 * Custom registration functionality for IronMan theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Automatically create Register page if it doesn't exist
 */
function create_register_page() {
	// Check if page already exists
	$page = get_page_by_path( 'register' );
	
	if ( ! $page ) {
		// Create new page
		$page_data = array(
			'post_title'    => 'Register',
			'post_content'  => '<!-- wp:template-part {"slug":"register"} /-->',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_name'     => 'register',
			'post_author'   => 1,
			'page_template' => 'register-template.php',
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		);
		
		wp_insert_post( $page_data );
	}
}
add_action( 'after_setup_theme', 'create_register_page' );

/**
 * Register custom page template
 */
function register_custom_page_templates( $templates ) {
	$templates['register-template.php'] = 'Register Template';
	return $templates;
}
add_filter( 'theme_page_templates', 'register_custom_page_templates' );

/**
 * Load custom register template
 */
function load_register_template( $template ) {
	if ( is_page_template( 'register-template.php' ) || is_page( 'register' ) ) {
		$new_template = get_template_directory() . '/custom-templates/register/template.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'load_register_template', 99 );

/**
 * AJAX: Check username availability
 */
add_action( 'wp_ajax_check_username', 'check_username_availability' );
add_action( 'wp_ajax_nopriv_check_username', 'check_username_availability' );
function check_username_availability() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	$username = sanitize_user( $_POST['username'] );
	
	if ( username_exists( $username ) ) {
		wp_send_json_error( array(
			'message' => 'This username is already taken'
		) );
	}
	
	wp_send_json_success( array(
		'message' => 'Username is available'
	) );
}

/**
 * AJAX: Check email availability
 */
add_action( 'wp_ajax_check_email', 'check_email_availability' );
add_action( 'wp_ajax_nopriv_check_email', 'check_email_availability' );
function check_email_availability() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	$email = sanitize_email( $_POST['email'] );
	
	if ( email_exists( $email ) ) {
		wp_send_json_error( array(
			'message' => 'This email is already registered'
		) );
	}
	
	wp_send_json_success( array(
		'message' => 'Email is available'
	) );
}

/**
 * AJAX: Handle user registration
 */
add_action( 'wp_ajax_custom_register_user', 'handle_custom_registration' );
add_action( 'wp_ajax_nopriv_custom_register_user', 'handle_custom_registration' );
function handle_custom_registration() {
	check_ajax_referer( 'custom_register_nonce', 'nonce' );
	
	// Sanitize and validate input data
	$username  = sanitize_user( $_POST['username'] );
	$email     = sanitize_email( $_POST['email'] );
	$password  = $_POST['password']; // Don't sanitize password, keep it as is
	$first_name = sanitize_text_field( $_POST['first_name'] );
	$last_name  = sanitize_text_field( $_POST['last_name'] );
	$gender     = sanitize_text_field( $_POST['gender'] );
	$fitness_goal = sanitize_text_field( $_POST['fitness_goal'] );
	$subscribe  = intval( $_POST['subscribe_newsletter'] );
	
	// Validate required fields
	if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
		wp_send_json_error( array(
			'message' => 'Please fill in all required fields'
		) );
	}
	
	// Validate username format
	if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $username ) ) {
		wp_send_json_error( array(
			'message' => 'Username can only contain letters, numbers, and underscores'
		) );
	}
	
	// Validate username length
	if ( strlen( $username ) < 3 ) {
		wp_send_json_error( array(
			'message' => 'Username must be at least 3 characters long'
		) );
	}
	
	// Validate email format
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array(
			'message' => 'Please enter a valid email address'
		) );
	}
	
	// Validate password length
	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( array(
			'message' => 'Password must be at least 8 characters long'
		) );
	}
	
	// Check if username already exists
	if ( username_exists( $username ) ) {
		wp_send_json_error( array(
			'message' => 'This username is already taken'
		) );
	}
	
	// Check if email already exists
	if ( email_exists( $email ) ) {
		wp_send_json_error( array(
			'message' => 'This email is already registered'
		) );
	}
	
	// Create user
	$user_id = wp_create_user( $username, $password, $email );
	
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array(
			'message' => 'Registration failed: ' . $user_id->get_error_message()
		) );
	}
	
	// Update user profile
	wp_update_user( array(
		'ID'         => $user_id,
		'first_name' => $first_name,
		'last_name'  => $last_name,
		'role'       => 'customer', // WooCommerce customer role
	) );
	
	// Save custom fields
	if ( $gender ) {
		update_user_meta( $user_id, 'gender', $gender );
	}
	if ( $fitness_goal ) {
		update_user_meta( $user_id, 'fitness_goal', $fitness_goal );
	}
	if ( $subscribe ) {
		update_user_meta( $user_id, 'newsletter_subscription', 1 );
	}
	
	// Automatically log in the user
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	
	// Send welcome email (optional)
	wp_new_user_notification( $user_id, null, 'user' );
	
	// Return success response
	wp_send_json_success( array(
		'message' => 'Registration successful! Redirecting...',
		'redirect' => wc_get_page_permalink( 'myaccount' ) // Redirect to my account page
	) );
}

