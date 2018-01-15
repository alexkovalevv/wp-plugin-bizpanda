<?php

	// a condition which allows to create the BizPanda instance only once
	if( defined('OPANDA_ACTIVE') ) {
		BizPanda::countCallerPlugin();

		return;
	}
	define('OPANDA_ACTIVE', true);
	define('BIZPANDA_VERSION', 126);

	define('OPANDA_WORDPRESS', true);
	define('OPANDA_POST_TYPE', 'opanda-item');

	define('OPANDA_BIZPANDA_DIR', dirname(__FILE__));
	define('OPANDA_BIZPANDA_URL', plugins_url(null, __FILE__));

	// creating a plugin via the factory
	require('libs/factory/core/boot.php');
	global $optinpanda;

	#comp remove
	// the following constants are used to debug features of diffrent builds
	// on developer machines before compiling the plugin

	// build: free, premium, ultimate
	if( !defined('BUILD_TYPE') ) {
		define('BUILD_TYPE', 'premium');
	}
	#endcomp

	load_plugin_textdomain('bizpanda', false, dirname(plugin_basename(__FILE__)) . '/langs');

	global $bizpanda;

	if( onp_lang('ru_RU') ) {
		$bizpanda = new Factory000_Plugin(__FILE__, array(
			'name' => 'bizpanda',
			'version' => implode('.', str_split(BIZPANDA_VERSION)),
			'updates' => OPANDA_BIZPANDA_DIR . '/plugin/updates/',
			'styleroller' => 'https://sociallocker.ru/styleroller'
		));
	} else {
		$bizpanda = new Factory000_Plugin(__FILE__, array(
			'name' => 'bizpanda',
			'version' => implode('.', str_split(BIZPANDA_VERSION)),
			'updates' => OPANDA_BIZPANDA_DIR . '/plugin/updates/',
			'styleroller' => 'http://api.byonepress.com/public/1.0/get/?product=styleroller'
		));
	}

	// requires factory modules
	$bizpanda->load(array(
		array('libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'),
		array('libs/factory/font-awesome', 'factory_fontawesome_000', 'admin'),
		array('libs/factory/forms', 'factory_forms_000', 'admin'),
		array('libs/factory/notices', 'factory_notices_000', 'admin'),
		array('libs/factory/pages', 'factory_pages_000', 'admin'),
		array('libs/factory/viewtables', 'factory_viewtables_000', 'admin'),
		array('libs/factory/metaboxes', 'factory_metaboxes_000', 'admin'),
		array('libs/factory/shortcodes', 'factory_shortcodes_000'),
		array('libs/factory/types', 'factory_types_000'),
		//array('libs/factory/addons', 'factory_addons_000', 'admin'),
	));

	#comp merge
	require(OPANDA_BIZPANDA_DIR . '/includes/functions.php');
	require(OPANDA_BIZPANDA_DIR . '/includes/panda-items.php');

	if( onp_lang('ru_RU') ) {
		if( !defined('OPANDA_SLA_MEMBERLUX_PLUGIN_DIR') ) {
			require(OPANDA_BIZPANDA_DIR . '/includes/assets.php');
			require(OPANDA_BIZPANDA_DIR . '/includes/shortcodes.php');
		} else {
			// Мост для плагина MemberLux
			require(OPANDA_SLA_MEMBERLUX_PLUGIN_DIR . '/extras/boot.php');
		}

		require(OPANDA_BIZPANDA_DIR . '/extras/wppage/boot.php');
	} else {
		require(OPANDA_BIZPANDA_DIR . '/includes/assets.php');
		require(OPANDA_BIZPANDA_DIR . '/includes/shortcodes.php');
	}

	require(OPANDA_BIZPANDA_DIR . '/includes/post-types.php');
	#endcomp

	if( is_admin() ) {
		require(OPANDA_BIZPANDA_DIR . '/admin/boot.php');
	}