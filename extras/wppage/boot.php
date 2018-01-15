<?php
	add_filter('tiny_mce_before_init', 'onp_wppage_tinymce_config', 9999);
	function onp_wppage_tinymce_config($init)
	{

		global $typenow;
		global $current_screen;

		if( $current_screen->post_type != 'page_selling' || $typenow != 'page_selling' ) {
			return $init;
		}

		$init['toolbar1'] = 'bold italic underline strikethrough | bullist numlist  | blockquote hr | alignleft aligncenter alignright | outdent indent | anchor link unlink anchor fullscreen wp_adv | optinpanda';

		// Pass $init back to WordPress
		return $init;
	}

	add_action('wppage_head', 'onp_do_wp_head', 0);
	function onp_do_wp_head()
	{
		do_action('wp_head');
		?>
		<style>
			.onp-sl iframe {
				padding: 0 !important;
				border: 0 !important;
				background: none !important;
			}
		</style>
	<?php
	}

	add_action('wppage_footer', 'onp_do_wp_footer');
	function onp_do_wp_footer()
	{
		do_action('wp_footer');
	}

	function opanda_remove_all_scripts_for_wp_page()
	{
		global $wp_scripts, $post;

		if( empty($post) ) {
			return;
		}

		if( !is_admin() && $post->post_type == 'page_selling' ) {
			$wp_scripts->queue = array(
				'jquery',
				'admin-bar',
				'jquery-ui-widget',
				'opanda-lockers'
			);
		}
	}

	add_action('wp_print_scripts', 'opanda_remove_all_scripts_for_wp_page', 100);

	function opanda_remove_all_styles_for_wp_page()
	{
		global $wp_styles, $post;

		if( empty($post) ) {
			return;
		}

		if( !is_admin() && $post->post_type == 'page_selling' ) {
			$wp_styles->queue = array(
				'admin-bar',
				'opanda-lockers',
				'genericons'
			);
		}
	}

	add_action('wp_print_styles', 'opanda_remove_all_styles_for_wp_page', 100);