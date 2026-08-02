<?php
/**
 * User Authentication & Google Sign-In Handler for Brezoaele.ro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Brezoaele_Auth {

	public static function init() {
		add_action( 'wp_ajax_nopriv_brezoaele_classic_login', array( __CLASS__, 'handle_classic_login' ) );
		add_action( 'wp_ajax_nopriv_brezoaele_classic_register', array( __CLASS__, 'handle_classic_register' ) );
		add_action( 'wp_ajax_nopriv_brezoaele_google_login', array( __CLASS__, 'handle_google_login' ) );
		add_action( 'wp_ajax_brezoaele_google_login', array( __CLASS__, 'handle_google_login' ) );
		add_action( 'admin_init', array( __CLASS__, 'restrict_admin_access' ) );
	}

	/**
	 * Restrict subscriber users from accessing /wp-admin/
	 */
	public static function restrict_admin_access() {
		if ( is_admin() && ! current_user_can( 'edit_posts' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_redirect( home_url( '/contul-meu/' ) );
			exit;
		}
	}

	/**
	 * Classic Login Handler
	 */
	public static function handle_classic_login() {
		check_ajax_referer( 'brezoaele_auth_nonce', 'security' );

		$email    = sanitize_email( $_POST['email'] ?? '' );
		$password = $_POST['password'] ?? '';

		if ( empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => 'Te rugăm să completezi emailul și parola.' ) );
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			wp_send_json_error( array( 'message' => 'Nu există niciun cont asociat acestei adrese de email.' ) );
		}

		$creds = array(
			'user_login'    => $user->user_login,
			'user_password' => $password,
			'remember'      => true,
		);

		$signon = wp_signon( $creds, is_ssl() );
		if ( is_wp_error( $signon ) ) {
			wp_send_json_error( array( 'message' => 'Parolă incorectă. Te rugăm să încerci din nou.' ) );
		}

		wp_send_json_success( array( 'message' => 'Conectare reușită! Te redirecționăm...', 'redirect' => home_url( '/contul-meu/' ) ) );
	}

	/**
	 * Classic Registration Handler
	 */
	public static function handle_classic_register() {
		check_ajax_referer( 'brezoaele_auth_nonce', 'security' );

		$fname    = sanitize_text_field( $_POST['fname'] ?? '' );
		$lname    = sanitize_text_field( $_POST['lname'] ?? '' );
		$email    = sanitize_email( $_POST['email'] ?? '' );
		$password = $_POST['password'] ?? '';

		if ( empty( $fname ) || empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => 'Toate câmpurile obligatorii trebuie completate.' ) );
		}

		if ( email_exists( $email ) ) {
			wp_send_json_error( array( 'message' => 'Adresa de email este deja înregistrată. Te rugăm să te conectezi.' ) );
		}

		$username = strtolower( sanitize_user( $fname . $lname . rand( 100, 999 ) ) );
		$user_id  = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
		}

		wp_update_user( array(
			'ID'           => $user_id,
			'first_name'   => $fname,
			'last_name'    => $lname,
			'display_name' => $fname . ' ' . $lname,
			'role'         => 'subscriber',
		) );

		// Auto login after registration
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true );

		wp_send_json_success( array( 'message' => 'Contul tău a fost creat cu succes! Te redirecționăm...', 'redirect' => home_url( '/contul-meu/' ) ) );
	}

	/**
	 * Google Sign-In Handler
	 */
	public static function handle_google_login() {
		check_ajax_referer( 'brezoaele_auth_nonce', 'security' );

		$credential = $_POST['credential'] ?? '';
		$email      = sanitize_email( $_POST['email'] ?? '' );
		$name       = sanitize_text_field( $_POST['name'] ?? '' );

		if ( empty( $email ) ) {
			wp_send_json_error( array( 'message' => 'Datele primite de la Google sunt incomplete.' ) );
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			// Create user from Google Account
			$username = strtolower( sanitize_user( explode( '@', $email )[0] . rand( 100, 999 ) ) );
			$random_pw = wp_generate_password( 16, true );
			$user_id   = wp_create_user( $username, $random_pw, $email );

			if ( is_wp_error( $user_id ) ) {
				wp_send_json_error( array( 'message' => 'Eroare la crearea contului cu Google.' ) );
			}

			wp_update_user( array(
				'ID'           => $user_id,
				'display_name' => ! empty( $name ) ? $name : $username,
				'role'         => 'subscriber',
			) );

			$user = get_user_by( 'id', $user_id );
		}

		// Log in user
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );

		wp_send_json_success( array( 'message' => 'Autentificare cu Google reușită!', 'redirect' => home_url( '/contul-meu/' ) ) );
	}
}

Brezoaele_Auth::init();
