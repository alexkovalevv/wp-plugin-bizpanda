<?php

	/**
	 * Opt-In Panda Type
	 * Declaration for custom post type of Social Locler.
	 * @link http://codex.wordpress.org/Post_Types
	 */
	class OPanda_PandaItemType extends FactoryTypes000_Type {

		/**
		 * Custom post name.
		 * @var string
		 */
		public $name = 'opanda-item';

		/**
		 * Singular title for labels of the type in the admin panel.
		 * @var string
		 */
		public $singularTitle = 'Opt-In Panda';

		/**
		 * Plural title for labels of the type in the admin panel.
		 * @var string
		 */
		public $pluralTitle = 'Opt-In Pandas';

		/**
		 * Template that defines a set of type options.
		 * Allowed values: public, private, internal.
		 * @var string
		 */
		public $template = 'private';

		/**
		 * Capabilities for roles that have access to manage the type.
		 * @link http://codex.wordpress.org/Roles_and_Capabilities
		 * @var array
		 */
		public $capabilities = array('administrator');

		public function useit()
		{
			if( onp_build('free', 'offline') ) {
				return true;
			}
			if( onp_license('paid', 'trial') ) {
				return true;
			}

			return false;
		}

		function __construct($plugin)
		{
			parent::__construct($plugin);

			$this->pluralTitle = __('Lockers', 'bizpanda');
			$this->singularTitle = __('Locker', 'bizpanda');
		}

		/**
		 * Type configurator.
		 */
		public function configure()
		{
			global $bizpanda;

			/**
			 * Labels
			 */

			$pluralName = $this->pluralTitle;
			$singularName = $this->singularTitle;

			$labels = array(
				'singular_name' => $this->singularTitle,
				'name' => $this->pluralTitle,
				'all_items' => sprintf(__('All Lockers', 'bizpanda'), $pluralName),
				'add_new' => sprintf(__('+ New Locker', 'bizpanda'), $singularName),
				'add_new_item' => sprintf(__('Add new', 'bizpanda'), $singularName),
				'edit' => sprintf(__('Edit', 'bizpanda')),
				'edit_item' => sprintf(__('Edit Item', 'bizpanda'), $singularName),
				'new_item' => sprintf(__('New Item', 'bizpanda'), $singularName),
				'view' => sprintf(__('View', 'factory')),
				'view_item' => sprintf(__('View Item', 'bizpanda'), $singularName),
				'search_items' => sprintf(__('Search Items', 'bizpanda'), $pluralName),
				'not_found' => sprintf(__('No Items found', 'bizpanda'), $pluralName),
				'not_found_in_trash' => sprintf(__('No Items found in trash', 'bizpanda'), $pluralName),
				'parent' => sprintf(__('Parent Item', 'bizpanda'), $pluralName)
			);

			//todo: Фильтр opanda_items_lables устарел
			$labels = factory_000_apply_filters_deprecated("opanda_items_lables", array($labels), '1.2.4', "bizpanda_items_lables");
			$this->options['labels'] = apply_filters('bizpanda_items_lables', $labels);

			/**
			 * Menu
			 */

			$this->menu->title = BizPanda::getMenuTitle();
			$this->menu->icon = BizPanda::getMenuIcon();

			/**
			 * View table
			 */

			$this->viewTable = 'OPanda_ItemsViewTable';

			/**
			 * Scripts & styles
			 */

			$this->scripts->request(array('jquery', 'jquery-effects-highlight', 'jquery-effects-slide'));

			$this->scripts->request(array(
				'bootstrap.transition',
				'bootstrap.datepicker',
				'bootstrap.tab',
				'holder.more-link',
				'control.checkbox',
				'control.dropdown',
				'control.list',
				'bootstrap.modal',
			), 'bootstrap');

			$this->styles->request(array(
				'bootstrap.core',
				'bootstrap.datepicker',
				'bootstrap.form-group',
				'bootstrap.form-metabox',
				'bootstrap.tab',
				'bootstrap.wp-editor',
				'bootstrap.separator',
				'control.checkbox',
				'control.dropdown',
				'control.list',
				'holder.more-link'
			), 'bootstrap');

			$this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/filters.js');
			$this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/libs/json2.js');
			$this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/preview.js');
			$this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/item-edit.js')->request('jquery-ui-sortable');
			$this->styles->add(OPANDA_BIZPANDA_URL . '/assets/admin/css/item-edit.css');

			if( onp_build('free') ) {
				$this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/libs/jquery.qtip.min.js');
				$this->styles->add(OPANDA_BIZPANDA_URL . '/assets/admin/css/libs/jquery.qtip.min.css');
			}

			//todo: хук является устаревшим opanda_panda-item_edit_assets
			factory_000_do_action_deprecated("opanda_panda-item_edit_assets", array(
				$this->scripts,
				$this->styles
			), '1.2.4', "bizpanda_panda-item_edit_assets");

			do_action('bizpanda_panda-item_edit_assets', $this->scripts, $this->styles);
		}
	}

	global $bizpanda;
	FactoryTypes000::register('OPanda_PandaItemType', $bizpanda);