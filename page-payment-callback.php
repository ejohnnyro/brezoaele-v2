<?php
/**
 * Template Name: Payment Callback Webhook (EuPlatesc IPN)
 * @package Brezoaele_V2
 */

// Disable header/footer rendering for webhook
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Log incoming IPN payload for debugging
$log_msg = '--- NEW CALLBACK RECEIVED FROM EUPLATESC ---' . PHP_EOL . json_encode( $_POST ) . PHP_EOL;
error_log( $log_msg );

// 1. Verify signature
if ( ! Brezoaele_EuPlatesc::verify_callback( $_POST ) ) {
	error_log( 'ERROR: EuPlatesc callback signature verification failed.' );
	status_header( 403 );
	echo 'ERROR SIGNATURE';
	exit;
}

$order_id = isset( $_POST['invoice_id'] ) ? sanitize_text_field( $_POST['invoice_id'] ) : '';
$action   = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : ''; // '0' = Approved/Success

if ( '0' === $action && ! empty( $order_id ) ) {
	$order = Brezoaele_Orders_DB::get_order( $order_id );

	if ( $order && 'pending' === $order['status'] ) {
		// Mark order as active
		Brezoaele_Orders_DB::update_status( $order_id, 'active' );

		$item_type = $order['item_type'];
		$item_id   = intval( $order['item_id'] );

		if ( 'anunt_premium' === $item_type && $item_id > 0 ) {
			// Upgrade Announcement to Premium (30 days)
			$expires_date = date( 'Y-m-d H:i:s', strtotime( '+30 days' ) );
			update_post_meta( $item_id, '_anunt_is_premium', '1' );
			update_post_meta( $item_id, '_anunt_premium_expires', $expires_date );
			wp_update_post( array(
				'ID'          => $item_id,
				'post_status' => 'publish',
			) );

			// Send confirmation email
			$subject = '[Brezoaele.ro] Anunțul tău a fost promovat la PREMIUM comanda #' . $order_id;
			$body    = "Salutare,\n\nPlata în valoare de 10 LEI pentru promovarea Premium a anunțului pe Brezoaele.ro a fost procesată cu succes!\n\nAnunțul tău a fost activat și va rămâne în capul listei de anunțuri timp de 30 de zile (până la $expires_date).\n\nPoți vedea factura și starea comandei în contul tău: " . home_url( '/contul-meu/' ) . "\n\nÎți mulțumim,\nEchipa Brezoaele.ro";
			wp_mail( $order['email'], $subject, $body );

		} elseif ( 'afacere_harta' === $item_type && $item_id > 0 ) {
			// Activate Business Listing on Map (365 days / 1 year)
			$expires_date = date( 'Y-m-d H:i:s', strtotime( '+365 days' ) );
			update_post_meta( $item_id, '_firma_subscription_active', '1' );
			update_post_meta( $item_id, '_firma_subscription_expires', $expires_date );
			wp_update_post( array(
				'ID'          => $item_id,
				'post_status' => 'publish',
			) );

			// Send confirmation email
			$subject = '[Brezoaele.ro] Afacerea ta a fost activată pe Harta Serviciilor! Comanda #' . $order_id;
			$body    = "Salutare,\n\nPlata în valoare de 149 LEI pentru adăugarea afacerii pe Harta Serviciilor Brezoaele.ro a fost procesată cu succes!\n\nAfacerea ta este acum vizibilă pe harta interactivă pentru un an de zile (până la $expires_date).\n\nPoți edita profilul afacerii din contul tău: " . home_url( '/contul-meu/' ) . "\n\nÎți mulțumim că sprijini comunitatea locală,\nEchipa Brezoaele.ro";
			wp_mail( $order['email'], $subject, $body );
		}

		error_log( "SUCCESS: Order $order_id processed and activated." );
	}
}

status_header( 200 );
echo 'OK';
exit;
