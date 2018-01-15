<?php
	/**
	 * The file contains a class to configure the metabox Advanced Options.
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
	 * The class to configure the metabox Advanced Options.
	 *
	 * @since 1.0.0
	 */
	class OPanda_AdvancedOptionsMetaBox extends FactoryMetaboxes000_FormMetabox {

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
		 * A prefix that will be used for names of input fields in the form.
		 *
		 * Inherited from the class FactoryFormMetabox.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $scope = 'opanda';

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

			$this->title = __('Advanced Options', 'bizpanda');
		}

		public $cssClass = 'factory-bootstrap-000';

		/**
		 * Configures a form that will be inside the metabox.
		 *
		 * @see FactoryMetaboxes000_FormMetabox
		 * @since 1.0.0
		 *
		 * @param FactoryForms000_Form $form A form object to configure.
		 * @return void
		 */
		public function form($form)
		{
			/*@mix:place*/

			$options = array(

				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'close',
					'title' => __('Close Icon', 'bizpanda'),
					'hint' => __('Shows the Close Icon at the corner.', 'bizpanda'),
					'icon' => OPANDA_BIZPANDA_URL . '/assets/admin/img/close-icon.png',
					'default' => false
				),
				array(
					'type' => 'textbox',
					'name' => 'timer',
					'title' => __('Timer Interval', 'bizpanda'),
					'hint' => __('Sets a countdown interval for the locker.', 'bizpanda'),
					'icon' => OPANDA_BIZPANDA_URL . '/assets/admin/img/timer-icon.png',
					'default' => false
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'ajax',
					'title' => __('AJAX', 'bizpanda'),
					'hint' => __('If On, locked content will be cut from a page source code.', 'bizpanda'),
					'icon' => OPANDA_BIZPANDA_URL . '/assets/admin/img/ajax-icon.png',
					'default' => false
				),
				array(
					'type' => 'html',
					'html' => '<div id="opanda-ajax-disabled" class="alert alert-warning">' . __('The option AJAX is not applied when the "transparence" or "blurring" overlap modes selected.', 'bizpanda') . '</div>'
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'highlight',
					'title' => __('Highlight', 'bizpanda'),
					'hint' => __('Defines whether the locker must use the Highlight effect.', 'bizpanda'),
					'icon' => OPANDA_BIZPANDA_URL . '/assets/admin/img/highlight-icon.png',
					'default' => true
				)
			);

			if( OPanda_Items::isCurrentFree() ) {

				$options[] = array(
					'type' => 'html',
					'html' => '<div style="display: none;" class="factory-fontawesome-000 opanda-overlay-note opanda-premium-note">' . __('<i class="fa fa-star-o"></i> Go Premium <i class="fa fa-star-o"></i><br />To Unlock These Features <a href="#" class="opnada-button">Learn More</a>', 'bizpanda') . '</div>'
				);
			}

			//todo: Фильтр opanda_advanced_options устарел
			$options = factory_000_apply_filters_deprecated('opanda_advanced_options', array($options), '1.2.4', 'bizpanda_advanced_options');
			$options = apply_filters('bizpanda_advanced_options', $options);

			$form->add($options);
		}
	}

	global $bizpanda;
	FactoryMetaboxes000::register('OPanda_AdvancedOptionsMetaBox', $bizpanda);
	/*@mix:place*/