<?php
	/**
	 * The file contains a class to configure the metabox "More Features?".
	 *
	 * Created via the Factory Metaboxes.
	 *
	 * @author Paul Kashtanoff <paul@byonepress.com>
	 * @copyright (c) 2013, OnePress Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	/**
	 * The class to configure the metabox "More Features?".
	 *
	 * @since 1.0.0
	 */
	class OPanda_TermsPrivacyMetaBox extends FactoryMetaboxes000_Metabox {

		/**
		 * A visible title of the metabox.
		 *
		 * Inherited from the class FactoryMetabox.
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $title;

		/**
		 * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 * Inherited from the class FactoryMetabox.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $priority = 'core';

		/**
		 * The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side').
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 * Inherited from the class FactoryMetabox.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $context = 'side';

		public function __construct($plugin)
		{
			parent::__construct($plugin);

			$this->title = __('Terms & Policies', 'bizpanda');
		}

		public $cssClass = 'factory-bootstrap-000';


		public function html()
		{
			?>
			<?php printf(__('You can change the Terms & Policies options on <a href="%s" target="_blank">this page</a>.', 'bizpanda'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index')) ?>
		<?php
		}
	}

	global $bizpanda;
	FactoryMetaboxes000::register('OPanda_TermsPrivacyMetaBox', $bizpanda);
	/*@mix:place*/