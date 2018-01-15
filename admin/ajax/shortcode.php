<?php
	/**
	 * Ajax requests linked with shortcodes.
	 *
	 * @author Paul Kashtanoff <paul@byonepress.com>
	 * @copyright (c) 2014, OnePress Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	add_action('wp_ajax_opanda_loader', 'onp_sl_load_ajax_content');
	add_action('wp_ajax_nopriv_opanda_loader', 'onp_sl_load_ajax_content');

	/**
	 * Returns content of a locker shortcode.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function onp_sl_load_ajax_content()
	{

		$hash = isset($_POST['hash'])
			? $_POST['hash']
			: null;
		$lockerId = isset($_POST['lockerId'])
			? intval($_POST['lockerId'])
			: 0;

		if( empty($hash) || empty($lockerId) ) {
			return;
		}

		global $wpdb;

		$content = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s", 'opanda_locker_content_hash_' . $hash));

		//todo: Фильтр opanda_ajax_content устарел
		$content = factory_000_apply_filters_deprecated("opanda_ajax_content", array($content), '1.2.4', 'bizpanda_ajax_content');
		$content = apply_filters('bizpanda_ajax_content', $content);

		echo $content;
		exit;
	}

	/*@mix:place*/