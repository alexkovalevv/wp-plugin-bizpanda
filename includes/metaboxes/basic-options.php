<?php
	/**
	 * The file contains a class to configure the metabox Basic Options.
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
	 * The class to configure the metabox Basic Options.
	 *
	 * @since 1.0.0
	 */
	class OPanda_BasicOptionsMetaBox extends FactoryMetaboxes000_FormMetabox {

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

		public $cssClass = 'factory-bootstrap-000 factory-fontawesome-000';

		public function __construct($plugin)
		{
			parent::__construct($plugin);

			$this->title = __('Basic Options', 'bizpanda');
		}

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

			$itemType = OPanda_Items::getCurrentItem();

			$items[] = array(
				'type' => 'hidden',
				'name' => 'item',
				'default' => isset($_GET['opanda_item'])
					? $_GET['opanda_item']
					: null
			);

			$defaultHeader = __('This content is locked!', 'bizpanda');
			$defaultMessage = __('Please support us, use one of the buttons below to unlock the content.', 'bizpanda');

			switch( $itemType['name'] ) {

				case 'email-locker':
					$defaultHeader = __('This Content Is Only For Subscribers', 'bizpanda');
					$defaultMessage = __('Please subscribe to unlock this content. Just enter your email below.', 'bizpanda');
					break;

				case 'connect-locker':
					$defaultHeader = __('Sing In To Unlock This Content', 'bizpanda');
					$defaultMessage = __('Please sign in. It\'s free. Just click one of the buttons below to get instant access.', 'bizpanda');
					break;
			}

			$items[] = array(
				'type' => 'textbox',
				'name' => 'header',
				'title' => __('Locker header', 'bizpanda'),
				'hint' => __('Type a header which attracts attention or calls to action. You can leave this field empty.', 'bizpanda'),
				'default' => $defaultHeader
			);
			$items[] = array(
				'type' => 'wp-editor',
				'name' => 'message',
				'title' => __('Locker message', 'bizpanda'),
				'hint' => __('Type a message which will appear under the header.', 'bizpanda') . '<br /><br />' . __('Shortcodes: [post_title], [post_url].', 'bizpanda'),
				'default' => $defaultMessage,
				'tinymce' => array(
					'setup' => 'function(ed){ window.bizpanda.lockerEditor.bindWpEditorChange( ed ); }',
					'height' => 100
				),
				'layout' => array(
					'hint-position' => 'left'
				)
			);

			if( 'email-locker' === $itemType['name'] ) {
				$items[] = array(
					'type' => 'columns',
					'items' => array(
						array(
							'type' => 'textbox',
							'name' => 'button_text',
							'title' => __('Buttton Text', 'bizpanda'),
							'hint' => __('The text on the button. Call to action!', 'bizpanda'),
							'default' => __('subscribe to unlock', 'bizpanda'),
							'column' => 1
						),
						array(
							'type' => 'textbox',
							'name' => 'after_button',
							'title' => __('After Buttton', 'bizpanda'),
							'hint' => __('The text below the button. Guarantee something.', 'bizpanda'),
							'default' => sprintf(__('Your email address is %s safe from spam!', 'bizpanda'), '100%'),
							'column' => 2
						)
					)
				);
			}

			//todo: Фильтр opanda_basic_options_text_section устарел
			$items = factory_000_apply_filters_deprecated('opanda_basic_options_text_section', array($items), '1.2.4', 'bizpanda_basic_options_text_section');
			$items = apply_filters('bizpanda_basic_options_text_section', $items);

			/*@mix:place*/

			if( in_array($itemType['name'], array('email-locker', 'signin-locker')) ) {
				$items[] = array(
					'type' => 'html',
					'html' => array($this, 'showOtherFrontendTextNote')
				);

				$items[] = array(
					'type' => 'separator'
				);
			}

			require_once OPANDA_BIZPANDA_DIR . '/includes/themes.php';

			$items[] = array(
				'type' => 'dropdown',
				'hasHints' => true,
				'name' => 'style',
				'data' => OPanda_ThemeManager::getThemes(OPanda_Items::getCurrentItemName(), 'dropdown'),
				'title' => __('Theme', 'bizpanda'),
				'hint' => __('Select the most suitable theme.', 'bizpanda'),
				'default' => 'secrets'
			);

			//todo: Фильтр opanda_basic_options_style_section устарел
			$items = factory_000_apply_filters_deprecated('opanda_basic_options_style_section', array($items), '1.2.4', 'bizpanda_basic_options_style_section');
			$items = apply_filters('bizpanda_basic_options_style_section', $items);

			if( OPanda_Items::isCurrentPremium() ) {
				$items[] = array(
					'type' => 'dropdown',
					'way' => 'buttons',
					'name' => 'overlap',
					'data' => array(
						array('full', '<i class="fa fa-lock"></i>' . __('Full (classic)', 'bizpanda')),
						array('transparence', '<i class="fa fa-adjust"></i>' . __('Transparency', 'bizpanda')),
						array(
							'blurring',
							'<i class="fa fa-bullseye"></i>' . __('Blurring', 'bizpanda'),
							__('Works in all browsers except IE 10-11 (In IE 10-11, the transparency mode will be applied)', 'bizpanda')
						)
					),
					'title' => __('Overlap Mode', 'bizpanda'),
					'hint' => __('Choose the way how your locker should lock the content.', 'bizpanda'),
					'default' => 'full'
				);
			} else {

				$items[] = array(
					'type' => 'dropdown',
					'way' => 'buttons',
					'name' => 'overlap',
					'data' => array(
						array('full', __('<i class="fa fa-lock"></i>Full (classic)', 'bizpanda')),
						array('transparence', __('<i class="fa fa-adjust"></i>Transparency', 'bizpanda')),
						array(
							'blurring',
							__('<i class="fa fa-bullseye"></i>Blurring', 'bizpanda'),
							sprintf(__('This option is available only in the <a href="%s" target="_blank">premium version</a> of the plugin (the transparency mode will be used in the free version)', 'bizpanda'), opanda_get_premium_url(null, 'blurring'))
						)
					),
					'title' => __('Overlap Mode', 'bizpanda'),
					'hint' => __('Choose the way how your locker should lock the content.', 'bizpanda'),
					'default' => 'full'
				);
			}

			$items[] = array(
				'type' => 'dropdown',
				'name' => 'overlap_position',
				'data' => array(
					array('top', __('Top Position', 'bizpanda')),
					array('middle', __('Middle Position', 'bizpanda')),
					array('scroll', __('Scrolling (N/A in Preview)', 'bizpanda'))
				),
				'title' => '',
				'hint' => '',
				'default' => 'middle'
			);

			$items = apply_filters('bizpanda_basic_options', $items);

			$form->add($items);
		}

		/**
		 * Replaces the 'blurring' overlap with 'transparence' in the free version.
		 *
		 * @since 1.0.0
		 * @param type $postId
		 */
		public function onSavingForm($postId)
		{
			if( !OPanda_Items::isCurrentFree() ) {
				return;
			}

			$overlap = isset ($_POST['opanda_overlap'])
				? $_POST['opanda_overlap']
				: null;
			if( $overlap == 'blurring' ) {
				$_POST['opanda_overlap'] = 'transparence';
			}
		}

		public function showOtherFrontendTextNote()
		{

			?>
			<div class="form-group opanda-edit-common-text">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<?php printf(__('<a href="%s" target="_blank">Click here</a> to edit the front-end text shared for all lockers.', 'bizpanda'), opanda_get_settings_url('text')) ?>
				</div>
			</div>
		<?php
		}
	}

	global $bizpanda;
	FactoryMetaboxes000::register('OPanda_BasicOptionsMetaBox', $bizpanda);
/*@mix:place*/