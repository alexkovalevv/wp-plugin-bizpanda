<?php
	add_action("wp_ajax_onp_sl_preview", 'onp_lock_preview');
	function onp_lock_preview()
	{
		global $bizpanda;

		$resOptions = array(
			'confirm_screen_title',
			'confirm_screen_instructiont',
			'confirm_screen_note1',
			'confirm_screen_note2',
			'confirm_screen_cancel',
			'confirm_screen_open',
			'misc_data_processing',
			'misc_or_enter_email',
			'misc_enter_your_email',
			'misc_enter_your_name',
			'misc_your_agree_with',
			'misc_terms_of_use',
			'misc_privacy_policy',
			'misc_or_wait',
			'misc_close',
			'misc_or',
			'errors_empty_email',
			'errors_inorrect_email',
			'errors_empty_name',
			'errors_subscription_canceled',
			'misc_close',
			'misc_or',
			'onestep_screen_title',
			'onestep_screen_instructiont',
			'onestep_screen_button',
			'errors_not_signed_in',
			'errors_not_granted',
			'signin_long',
			'signin_short',
			'signin_facebook_name',
			'signin_twitter_name',
			'signin_google_name',
			'signin_linkedin_name'
		);

		$resources = array();
		foreach($resOptions as $resName) {
			$resValue = get_option('opanda_res_' . $resName, false);
			if( empty($resValue) ) {
				continue;
			}
			$resources[$resName] = $resValue;
		}

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8"/>
			<style>
				body {
					padding: 0px;
					margin: 0px;
					font: normal normal 400 14px/170% Arial;
					color: #333333;
					text-align: justify;
				}

				* {
					padding: 0px;
					margin: 0px;
				}

				#wrap {
					overflow: hidden;
				}

				p {
					margin: 0px;
				}

				p + p {
					margin-top: 8px;
				}

				.content-to-lock a {
					color: #3185AB;
				}

				.content-to-lock {
					text-shadow: 1px 1px 1px #fff;
					padding: 20px 40px;
				}

				.content-to-lock .header {
					margin-bottom: 20px;
				}

				.content-to-lock .header strong {
					font-size: 16px;
					text-transform: capitalize;
				}

				.content-to-lock .image {
					text-align: center;
					background-color: #f9f9f9;
					border-bottom: 3px solid #f1f1f1;
					margin: auto;
					padding: 30px 20px 20px 20px;
				}

				.content-to-lock .image img {
					display: block;
					margin: auto;
					margin-bottom: 15px;
					max-width: 460px;
					max-height: 276px;
					height: 100%;
					width: 100%;
				}

				.content-to-lock .footer {
					margin-top: 20px;
				}
			</style>

			<?php if( !empty($resources) ) { ?>
				<script>
					window.__pandalockers = {};
					window.__pandalockers.lang = <?php echo json_encode( $resources ) ?>;
				</script>
			<?php } ?>

			<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/jquery.js"></script>

			<?php if( file_exists(includes_url() . 'js/jquery/ui/jquery.ui.core.min.js') ) { ?>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js"></script>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/jquery.ui.effect.min.js"></script>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/jquery.ui.effect-highlight.min.js"></script>
			<?php } else { ?>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/core.min.js"></script>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/effect.min.js"></script>
				<script type="text/javascript" src="<?php echo get_site_url() ?>/wp-includes/js/jquery/ui/effect-highlight.min.js"></script>
			<?php } ?>

			<script type="text/javascript" src="<?php echo OPANDA_BIZPANDA_URL ?>/assets/admin/js/libs/json2.js"></script>
			<script type="text/javascript" src="<?php echo OPANDA_BIZPANDA_URL ?>/assets/js/lockers.min.js?ver=<?= $bizpanda->version ?>"></script>
			<?php if( get_locale() == 'ru_RU' ) {
				echo '<script type="text/javascript" src="' . OPANDA_BIZPANDA_URL . '/assets/js/lockers.localization.ru_RU.min.js?ver=' . $bizpanda->version . '"></script>';
			} ?>
			<link rel="stylesheet" type="text/css" href="<?php echo OPANDA_BIZPANDA_URL ?>/assets/css/lockers.min.css?ver=<?= $bizpanda->version ?>">

			<?php
				//todo: хук является устаревшим onp_sxal_preview_head
				factory_000_do_action_deprecated('onp_sl_preview_head', array(), '1.2.4', 'bizpanda_print_scripts_to_preview_head');
			?>
			<?php do_action('bizpanda_print_scripts_to_preview_head') ?>
		</head>
		<body class="onp-sl-demo factory-fontawesome-000">
		<div id="wrap" style="text-align: center; margin: 0 auto;">
			<div class="content-to-lock" style="text-align: center; margin: 0 auto;">
				<div class="header">
					<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing</strong></p>

					<p>
						Maecenas sed consectetur tortor. Morbi non vestibulum eros, at posuere nisi praesent consequat.
					</p>
				</div>
				<div class="image">
					<img src="<?php echo OPANDA_BIZPANDA_URL ?>/assets/admin/img/preview-image.jpg" alt="Preview image"/>
					<i>Aenean vel sodales sem. Morbi et felis eget felis vulputate placerat.</i>
				</div>
				<div class="footer">
					<p>Curabitur a rutrum enim, sit amet ultrices quam.
						Morbi dui leo, euismod a diam vitae, hendrerit ultricies arcu.
						Suspendisse tempor ultrices urna ut auctor.</p>
				</div>
				<?php if( !isset($_GET['from']) ): ?>
					<div class="header">
						<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing</strong></p>

						<p>
							Maecenas sed consectetur tortor. Morbi non vestibulum eros, at posuere nisi praesent
							consequat.
						</p>
					</div>
					<div class="image">
						<img src="<?php echo OPANDA_BIZPANDA_URL ?>/assets/admin/img/preview-image.jpg" alt="Preview image"/>
						<i>Aenean vel sodales sem. Morbi et felis eget felis vulputate placerat.</i>
					</div>
					<div class="footer">
						<p>Curabitur a rutrum enim, sit amet ultrices quam.
							Morbi dui leo, euismod a diam vitae, hendrerit ultricies arcu.
							Suspendisse tempor ultrices urna ut auctor.</p>
					</div>

					<div class="header">
						<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing</strong></p>

						<p>
							Maecenas sed consectetur tortor. Morbi non vestibulum eros, at posuere nisi praesent
							consequat.
						</p>
					</div>
					<div class="image">
						<img src="<?php echo OPANDA_BIZPANDA_URL ?>/assets/admin/img/preview-image.jpg" alt="Preview image"/>
						<i>Aenean vel sodales sem. Morbi et felis eget felis vulputate placerat.</i>
					</div>
					<div class="footer">
						<p>Curabitur a rutrum enim, sit amet ultrices quam.
							Morbi dui leo, euismod a diam vitae, hendrerit ultricies arcu.
							Suspendisse tempor ultrices urna ut auctor.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div style="clear: both;"></div>
		</body>

		<?php
			$getData = !empty($_GET)
				? $_GET
				: null;

			//todo: хук является устаревшим opanda_preview_print_scripts
			factory_000_do_action_deprecated('opanda_preview_print_scripts', array($getData), '1.2.4', 'bizpanda_print_scripts_to_preview_footer');

			do_action('bizpanda_print_scripts_to_preview_footer', $getData);
		?>

		<script>
			(function($) {
				var callback = '<?php echo ( isset( $_POST['callback'] ) ? $_POST['callback'] : '' ) ?>';
				var $originalContent = $("#wrap").clone();

				/**
				 * Предопределенные события
				 * @param options
				 */
				$(window).resize(function() {
					window.alertFrameSize();
				});

				$.pandalocker.hooks.add('opanda-unlock', function() {
					window.alertFrameSize();
				});

				$.pandalocker.hooks.add('opanda-lock', function() {
					window.alertFrameSize();
				});

				$.pandalocker.hooks.add('opanda-next-step', function() {
					window.alertFrameSize();
				});

				window.setOptions = function(options) {
					$("#wrap").remove();
					$("body").prepend($originalContent.clone());

					options.demo = true;
					options.overlap.position = 'middle';

					if( options.overlap && options.overlap.mode && options.overlap.mode == 'full' ) {
						$("#wrap").css('padding', '20px');
					} else {
						$("#wrap").css('padding', '0');
					}

					$(".content-to-lock").pandalocker(options);

					window.alertFrameSize();
				};

				window.alertFrameSizeIter = 0;

				window.alertFrameSize = function() {
					if( !parent || !callback ) {
						return;
					}

					if( void 0 != window.alertFrameSizeTimer && window.alertFrameSizeTimer ) {
						window.alertFrameSizeIter = 0;
						clearInterval(window.alertFrameSizeTimer);
					}

					window.alertFrameSizeTimer = setInterval(function() {
						if( window.alertFrameSizeIter > 20 ) {
							window.alertFrameSizeIter = 0;
							clearInterval(window.alertFrameSizeTimer);
							return;
						}
						var height,
							lockerHeight = jQuery(".onp-sl").outerHeight();

						jQuery(".content-to-lock").css({margin: 0}).height(lockerHeight + 60);
						height = jQuery("#wrap").outerHeight();

						if( parent[callback] ) {
							parent[callback](height);
						}
						window.alertFrameSizeIter++;
					}, 100);

				};

				window.dencodeOptions = function(options) {
					for( var optionName in options ) {
						if( !$.isPlainObject(options[optionName]) ) {
							continue;
						}

						if( typeof options[optionName] === 'object' ) {
							options[optionName] = dencodeOptions(options[optionName]);
						} else {
							if( options[optionName] ) {
								options[optionName] = decodeURI(options[optionName]);
							}
						}
					}
					return options;
				};

				window.defaultOptions = {
					demo: true,
					text: {},

					locker: {},
					overlap: {},

					groups: {},

					socialButtons: {
						buttons: {},
						effects: {}
					},

					connectButtons: {
						facebook: {},
						twitter: {},
						google: {},
						linkedin: {}
					},

					subscrioption: {},

					events: {
						ready: function() {
							alertFrameSize();
						},
						unlock: function() {
							alertFrameSize();
						},
						unlockByTimer: function() {
							alertFrameSize();
						},
						unlockByClose: function() {
							alertFrameSize();
						}
					}
				};

				$(document).trigger('onp-sl-filter-preview-options-php');

				var postOptions = dencodeOptions(JSON.parse('<?php echo $_POST['options'] ?>'));
				var options = $.extend(window.defaultOptions, postOptions);

				$(document).ready(function() {
					if( options.overlap && options.overlap.mode && options.overlap.mode == 'full' ) {
						$("#wrap").css('padding', '20px');
					} else {
						$("#wrap").css('padding', '0');
					}

					$(".content-to-lock").pandalocker(options);
				});

				$(document).click(function() {
					if( parent && window.removeProfilerSelector ) {
						window.removeProfilerSelector();
					}
				});
			})(jQuery);
		</script>
		</html>

		<?php
		exit;
	}
/*@mix:place*/