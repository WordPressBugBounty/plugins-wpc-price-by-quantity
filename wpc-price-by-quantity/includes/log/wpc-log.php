<?php
defined( 'ABSPATH' ) || exit;

register_activation_hook( defined( 'WPCPQ_LITE' ) ? WPCPQ_LITE : WPCPQ_FILE, 'wpcpq_activate' );
register_deactivation_hook( defined( 'WPCPQ_LITE' ) ? WPCPQ_LITE : WPCPQ_FILE, 'wpcpq_deactivate' );
add_action( 'admin_init', 'wpcpq_check_version' );

function wpcpq_check_version() {
	if ( ! empty( get_option( 'wpcpq_version' ) ) && ( get_option( 'wpcpq_version' ) < WPCPQ_VERSION ) ) {
		wpc_log( 'wpcpq', 'upgraded' );
		update_option( 'wpcpq_version', WPCPQ_VERSION, false );
	}
}

function wpcpq_activate() {
	wpc_log( 'wpcpq', 'installed' );
	update_option( 'wpcpq_version', WPCPQ_VERSION, false );
}

function wpcpq_deactivate() {
	wpc_log( 'wpcpq', 'deactivated' );
}

if ( ! function_exists( 'wpc_log' ) ) {
	function wpc_log( $prefix, $action ) {
		$logs = get_option( 'wpc_logs', [] );
		$user = wp_get_current_user();

		if ( ! isset( $logs[ $prefix ] ) ) {
			$logs[ $prefix ] = [];
		}

		$logs[ $prefix ][] = [
			'time'   => current_time( 'mysql' ),
			'user'   => $user->display_name . ' (ID: ' . $user->ID . ')',
			'action' => $action
		];

		update_option( 'wpc_logs', $logs, false );
	}
}