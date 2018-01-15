<?php
	/**
	 * Ajax requests to get a list of Panda Items to insert.
	 *
	 * @author Paul Kashtanoff <paul@byonepress.com>
	 * @copyright (c) 2014, OnePress Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	/**
	 * Returns a list of the lockers.
	 */
	function opanda_ajax_get_lockers()
	{
		$lockers = get_posts(array(
			'post_type' => OPANDA_POST_TYPE,
			'meta_key' => 'opanda_item',
			'meta_value' => OPanda_Items::getAvailableNames(),
			'numberposts' => -1
		));

		$result = array();
		foreach($lockers as $locker) {

			$bulkMode = null;
			$bulkLockers = get_option('onp_sl_bulk_lockers', array());

			if( !empty($bulkLockers) && isset($bulkLockers[$locker->ID]) ) {
				$bulkMode = isset($bulkLockers[$locker->ID]['way']) && !empty($bulkLockers[$locker->ID]['way'])
					? $bulkLockers[$locker->ID]['way']
					: null;
			}

			$userTrigger = false;
			$openLockerTrigger = get_post_meta($locker->ID, 'opanda_open_locker_trigger', true);
			$openLockerWay = get_post_meta($locker->ID, 'opanda_open_locker_way', true);

			if( !empty($openLockerTrigger) && ($openLockerTrigger == 'click' || $openLockerTrigger == 'hover') ) {
				if( !empty($openLockerWay) && $openLockerWay == 'shortcode' ) {
					$userTrigger = true;
				}
			}

			$itemType = get_post_meta($locker->ID, 'opanda_item', true);
			$item = OPanda_Items::getItem($itemType);

			$result[] = array(
				'id' => $locker->ID,
				'title' => empty($locker->post_title)
					? '(no titled, ID=' . $locker->ID . ')'
					: $locker->post_title,
				'shortcode' => $item['shortcode'],
				'isDefault' => get_post_meta($locker->ID, 'opanda_is_default', true),
				'bulkMode' => $bulkMode,
				'userTrigger' => $userTrigger
			);
		}

		echo json_encode($result);
		die();
	}

	add_action('wp_ajax_get_opanda_lockers', 'opanda_ajax_get_lockers');

	/*@mix:place*/