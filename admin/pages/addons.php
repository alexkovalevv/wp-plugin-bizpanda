<?php
	
	/**
	 * The add-ons page.
	 *
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017, OnePress Ltd
	 *
	 * @since 1.0.0
	 */
	class Opanda_PageAddons extends FactoryAddons000_AdminPageAddons {
		
		public function __construct($plugin)
		{
			$this->menuPostType = OPANDA_POST_TYPE;
			
			if( !current_user_can('administrator') ) {
				$this->capabilitiy = "bizpanda_view_addons_page";
			}
			
			$this->id = "bizpanda_addons";
			
			$this->menuTitle = __('Add-ons', 'bizpanda');
			
			if( defined('ONP_LOCAL_ADDONS') ) {
				
				if( get_locale() == 'ru_RU' ) {
					$this->get_addons_url = OPANDA_BIZPANDA_URL . '/addons.ru_RU.json';
				} else {
					$this->get_addons_url = OPANDA_BIZPANDA_URL . '/addons.json';
				}
			} else {
				$this->get_addons_url = 'http://test.sociallocker.ru/addons_json.php?lang=' . get_locale();
			}
			
			parent::__construct($plugin);
		}
		
		public function getAddonInstance($addon_name)
		{
			$plugins = BizPanda::getInstalledPlugins();
			$plugins_name = array_column($plugins, 'name');
			
			if( in_array('bizpanda-' . $addon_name . '-addon', $plugins_name) ) {
				$result_key = array_search('bizpanda-' . $addon_name . '-addon', array_column($plugins, 'name'));
				if( $result_key !== false && isset($plugins[$result_key]) ) {
					return $plugins[$result_key]['plugin'];
				}
			}
			
			return null;
		}
		
		public function getPlugins()
		{
			return array(
				'sociallocker' => array(
					'title' => 'Social Locker'
				),
				'optin-panda' => array(
					'title' => 'Opt-In Panda'
				)
			);
		}
	}
	
	FactoryPages000::register($bizpanda, 'Opanda_PageAddons');