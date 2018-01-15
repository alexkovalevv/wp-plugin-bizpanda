<?php

	/**
	 * Theme Manager Class
	 *
	 * Manages themes available to use.
	 *
	 * @since 3.3.3
	 */
	class OPanda_ThemeManager {

		/**
		 * The flat to used to call the hook 'onp_sl_register_themes' once.
		 *
		 * @since 3.3.3
		 * @var bool
		 */
		private static $themesRegistered = false;

		/**
		 * Contains an array of registred themes.
		 *
		 * @since 3.3.3
		 * @var mixed[]
		 */
		private static $themes;


		/**
		 * Returns all registered themes.
		 *
		 * @since 3.3.3
		 * @param string $format the format of the output array, available values: 'dropdown'.
		 * @return mixed[]
		 */
		public static function getThemes($item = null, $format = null)
		{
			$themes = array();

			if( !self::$themesRegistered ) {

				//todo: хук является устаревшим onp_sl_register_themes
				factory_000_do_action_deprecated("onp_sl_register_themes", array($item), '1.2.4', "bizpanda_register_themes");

				do_action('bizpanda_register_themes', $item);

				self::$themesRegistered = true;
			}

			$themes = self::$themes;

			if( $item ) {

				$allThemes = $themes;
				$themes = array();

				foreach($allThemes as $themeName => $themeData) {
					if( isset($themeData['items']) && !in_array($item, $themeData['items']) ) {
						continue;
					}
					$themes[$themeName] = $themeData;
				}
			}

			if( 'dropdown' === $format ) {
				$output = array();
				foreach($themes as $theme) {
					$output[] = array(
						'title' => $theme['title'],
						'value' => $theme['name'],
						'hint' => isset($theme['hint'])
							? $theme['hint']
							: null,
						'data' => array(
							'preview' => isset($theme['preview'])
								? $theme['preview']
								: null,
							'previewHeight' => isset($theme['previewHeight'])
								? $theme['previewHeight']
								: null,
							'colors' => isset($theme['colors']) && is_array($theme['colors'])
								? htmlentities(json_encode($theme['colors']))
								: '[]',
						)
					);
				}

				return $output;
			}

			/*@mix:place*/

			return $themes;
		}

		/**
		 * Registers a new theme.
		 *
		 * @since 3.3.3
		 * @param mixed $themeOptions
		 * @return void
		 */
		public static function registerTheme($themeOptions)
		{
			self::$themes[$themeOptions['name']] = $themeOptions;
		}

		/**
		 * Returns editable options for a given theme.
		 *
		 * @since 3.3.3
		 * @param string $themeName A theme name for which we need to return the options.
		 * @return mixed[]
		 */
		public static function getEditableOptions($themeName)
		{
			$themes = self::getThemes();

			if( isset($themes[$themeName]) ) {

				$path = $themes[$themeName]['path'] . '/editable-options.php';
				if( !file_exists($path) ) {
					return false;
				}

				require_once $path;
			}

			$options = array();

			//todo: Функция onp_sl_get_{$themeName}_theme_editable_options устарела
			$functionToCall = 'onp_sl_get_' . str_replace('-', '_', $themeName) . '_theme_editable_options';

			if( function_exists($functionToCall) ) {
				$options = $functionToCall();
			}

			//todo: Альтернативная функция onp_sl_get_{$themeName}_theme_editable_options
			$alternateFunctionToCall = 'bizpanda_get_' . str_replace('-', '_', $themeName) . '_theme_editable_options';
			if( function_exists($alternateFunctionToCall) ) {
				$options = $alternateFunctionToCall();
			}

			//todo: Фильтр onp_sl_editable_{$themeName}_theme_options устарел
			$options = factory_000_apply_filters_deprecated('onp_sl_editable_' . $themeName . '_theme_options', array(
				$options,
				$themeName
			), '1.2.4', 'bizpanda_editable_' . $themeName . '_theme_options');

			$options = apply_filters('bizpanda_editable_' . $themeName . '_theme_options', $options, $themeName);

			//todo: Фильтр onp_sl_editable_theme_options устарел
			$options = factory_000_apply_filters_deprecated('onp_sl_editable_theme_options', array(
				$options,
				$themeName
			), '1.2.4', 'bizpanda_editable_theme_options');

			$options = apply_filters('bizpanda_editable_theme_options', $options, $themeName);

			return $options;
		}

		/**
		 * Returns CSS converting rules.
		 *
		 * @since 3.3.3
		 * @param string $themeName A theme name for which we need to return the rules.
		 * @return mixed[]
		 */
		public static function getRulesToGenerateCSS($themeName)
		{
			$themes = self::getThemes();

			if( isset($themes[$themeName]) ) {

				$path = $themes[$themeName]['path'] . '/css-rules.php';
				if( !file_exists($path) ) {
					return false;
				}

				require_once $path;
			}

			$rules = array();

			//todo: Функция onp_sl_get_{$themeName}_theme_css_rules устарела
			$functionToCall = 'onp_sl_get_' . str_replace('-', '_', $themeName) . '_theme_css_rules';
			if( function_exists($functionToCall) ) {
				$rules = $functionToCall();
			}

			//todo: Алтернативная функция onp_sl_get_{$themeName}_theme_css_rules устарела
			$alternateFunctionToCall = 'bizpanda_get_' . str_replace('-', '_', $themeName) . '_theme_css_rules';
			if( function_exists($alternateFunctionToCall) ) {
				$rules = $alternateFunctionToCall();
			}

			//todo: Фильтр onp_sl_{$themeName}_theme_css_rules устарел
			$rules = factory_000_apply_filters_deprecated('onp_sl_' . $themeName . '_theme_css_rules', array(
				$rules,
				$themeName
			), '1.2.4', 'bizpanda_' . $themeName . '_theme_css_rules');

			$rules = apply_filters('bizpanda_' . $themeName . '_theme_css_rules', $rules, $themeName);

			//todo: Фильтр onp_sl_theme_css_rules устарел
			$rules = factory_000_apply_filters_deprecated('onp_sl_theme_css_rules', array(
				$rules,
				$themeName
			), '1.2.4', 'bizpanda_theme_css_rules');

			$rules = apply_filters('bizpanda_theme_css_rules', $rules, $themeName);

			/*@mix:place*/

			return $rules;
		}
	}

	/*@mix:place*/

	/**
	 * Helper which returns a set of editable options for changing background.
	 *
	 * @since 1.0.2
	 * @param type $name A name base for the options.
	 */
	function opanda_background_editor_options($name, $sets = array())
	{

		$defaultType = isset($sets['default'])
			? $sets['default']['type']
			: 'color';

		$options = array(
			'type' => 'control-group',
			'name' => $name . '_type',
			'default' => $name . '_' . $defaultType . '_item',
			'title' => isset($sets['title'])
				? $sets['title']
				: null,
			'items' => array(
				array(
					'type' => 'control-group-item',
					'title' => __('Color', 'bizpanda'),
					'name' => $name . '_color_item',
					'items' => array(
						array(
							'type' => 'color-and-opacity',
							'name' => $name . '_color',
							'title' => __('Set up color and opacity:', 'bizpanda'),
							'default' => (isset($sets['default']) && $defaultType == 'color')
								? $sets['default']['value']
								: null
						)
					)
				),
				array(
					'type' => 'control-group-item',
					'title' => __('Gradient', 'bizpanda'),
					'name' => $name . '_gradient_item',
					'items' => array(
						array(
							'type' => 'gradient',
							'name' => $name . '_gradient',
							'title' => __('Set up gradient', 'bizpanda'),
							'default' => (isset($sets['default']) && $defaultType == 'gradient')
								? $sets['default']['value']
								: null
						)
					)
				),
				array(
					'type' => 'control-group-item',
					'title' => __('Pattern', 'bizpanda'),
					'name' => $name . '_image_item',
					'items' => array(
						array(
							'type' => 'pattern',
							'name' => $name . '_image',
							'title' => __('Set up pattern', 'bizpanda'),
							'default' => (isset($sets['default']) && $defaultType == 'image')
								? $sets['default']['value']
								: null,
							'patterns' => (isset($sets['patterns']))
								? $sets['patterns']
								: array()
						)
					)
				)
			)
		);

		return $options;
	}