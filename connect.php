<?php

	if( !function_exists('bizpanda_compability_note') ) {

		global $bizpanda_issue_plugin;

		function bizpanda_compability_note()
		{
			$count = 0;

			if( method_exists('BizPanda', 'getInstalledPlugins') ) {

				$plugins = BizPanda::getInstalledPlugins();
				$count = count($plugins);

				$titles = array();
				foreach($plugins as $plugin)
					$titles[] = $plugin['plugin']->options['title'];
				$titles = implode(',', $titles);
			} else {

				$count = 1;

				if( BizPanda::hasPlugin('optinpanda') ) {
					$titles = 'Opt-In Panda';
				} else {
					$titles = 'Social Locker';
				}
			}

			global $bizpanda_issue_plugin;

			echo '<div id="message" class="error" style="padding: 10px;">';
			if( $count > 1 ) {
				printf(__('Unable to activate <strong>%s</strong>. Please make sure that the following plugins are updated to the latest versions: <strong>%s</strong>. Deactivate %s and try to update the specified plugins.'), $bizpanda_issue_plugin, $titles, $bizpanda_issue_plugin);
			} else printf(__('Unable to activate <strong>%s</strong>. Please make sure that the following plugin is updated to the latest version: <strong>%s</strong>. Deactivate %s and try to update the specified plugin.'), $bizpanda_issue_plugin, $titles, $bizpanda_issue_plugin);
			echo '</div>';
		}

		function bizpanda_validate($requiredVersion, $pluginTitle)
		{
			$invalid = !defined('BIZPANDA_VERSION') || BIZPANDA_VERSION < $requiredVersion;

			//todo: We eliminate compatibility problems with plugins that have an old factory.
			$sl_bizpanda_ver_old = defined('SOCIALLOCKER_BIZPANDA_VERSION') && SOCIALLOCKER_BIZPANDA_VERSION < 126;
			$op_bizpanda_ver_old = defined('OPTINPANDA_BIZPANDA_VERSION') && OPTINPANDA_BIZPANDA_VERSION < 126;

			if( $sl_bizpanda_ver_old && defined('SOCIALLOCKER_DIR') ) {
				$sociallocker_base = plugin_basename(SOCIALLOCKER_DIR . '/sociallocker-next.php');
				remove_action('activate_' . $sociallocker_base, 'onp_sl_activation');
				register_activation_hook(SOCIALLOCKER_DIR . '/sociallocker-next.php', 'bizpanda_compatibility_activation');
			}
			if( $op_bizpanda_ver_old && defined('OPTINPANDA_DIR') ) {
				$optinpanda_base = plugin_basename(OPTINPANDA_DIR . '/optinpanda.php');
				remove_action('activate_' . $optinpanda_base, 'onp_sl_activation');
				register_activation_hook(OPTINPANDA_DIR . '/optinpanda.php', 'bizpanda_compatibility_activation');
			}

			if( ($invalid || $sl_bizpanda_ver_old || $op_bizpanda_ver_old) && is_admin() ) {

				global $bizpanda_issue_plugin;
				$bizpanda_issue_plugin = $pluginTitle;

				add_action('admin_notices', 'bizpanda_compability_note');
			}

			return !$invalid;
		}
	}

	//todo: We eliminate compatibility problems with plugins that have an old factory.
	$sl_bizpanda_ver_old = defined('SOCIALLOCKER_BIZPANDA_VERSION') && SOCIALLOCKER_BIZPANDA_VERSION < 126;
	$op_bizpanda_ver_old = defined('OPTINPANDA_BIZPANDA_VERSION') && OPTINPANDA_BIZPANDA_VERSION < 126;

	if( $sl_bizpanda_ver_old || $op_bizpanda_ver_old ) {
		wp_die(__('Old versions of Bizpanda(less 1.2.6) were found on your site. Please deactivate old version Social Locker or Optin panda for stable work of the activated plugin. You can not use incompatible plugins.', 'bizpanda'));
	}

	if( !function_exists('bizpanda_compatibility_activation') ) {
		function bizpanda_compatibility_activation()
		{
			//todo: We eliminate compatibility problems with plugins that have an old factory.
			$sl_bizpanda_ver_old = defined('SOCIALLOCKER_BIZPANDA_VERSION') && SOCIALLOCKER_BIZPANDA_VERSION < 126;
			$op_bizpanda_ver_old = defined('OPTINPANDA_BIZPANDA_VERSION') && OPTINPANDA_BIZPANDA_VERSION < 126;

			if( $sl_bizpanda_ver_old || $op_bizpanda_ver_old ) {
				wp_die(__('Old versions of Bizpanda(less 1.2.6) were found on your site. Please deactivate old version Social Locker or Optin panda for stable work of the activated plugin. You can not use incompatible plugins.', 'bizpanda'));
			}
		}
	}

	// we don't have to register another version of bizpanda,
	// if some version was already registered, so skip the code below
	if( defined('OPANDA_ACTIVE') ) {
		return;
	}

	global $bizpanda_versions;

	if( !$bizpanda_versions ) {
		$bizpanda_versions = array('free' => array(), 'premium' => array(), 'ultimate' => array());
	}

	if( onp_build('free') ) {
		$bizpanda_versions['free']['126'] = dirname(__FILE__) . '/boot.php';
	} else {
		$bizpanda_versions['premium']['126'] = dirname(__FILE__) . '/boot.php';
	}

	/*if( onp_build('ultimate', 'premium') ) {
		$bizpanda_versions['premium']['126'] = dirname(__FILE__) . '/boot.php';
	}*/

	if( !function_exists('bizpanda_connect') ) {

		function bizpanda_connect()
		{

			if( !defined('OPANDA_ACTIVE') ) {

				global $bizpanda_versions;

				if( !empty($bizpanda_versions['premium']) ) {
					$assembly = "premium";
				} else {
					$assembly = "free";
				}

				$keys = array_keys($bizpanda_versions[$assembly]);
				sort($keys);

				$version = end($keys);
				require $bizpanda_versions[$assembly][$version];
			}

			do_action('bizpanda_init');
		}

		add_action('plugins_loaded', 'bizpanda_connect');
	}
