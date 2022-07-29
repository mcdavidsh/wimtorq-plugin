<?php
add_action( 'admin_menu', 'wimtorq_admin_menu' );
if (!function_exists('wimtorq_admin_menu')) {
	function wimtorq_admin_menu() {
		$page_title = 'The Wimtorq Plugin';
		$menu_title = 'The Wimtorq Plugin';
		$capability = 'manage_options';
		$menu_slug  = 'wimtorq-plugin';
		$function   = 'wimtorq_settings';
		$icon_url   = 'dashicons-rest-api';
		$position   = 4;
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	}
}
