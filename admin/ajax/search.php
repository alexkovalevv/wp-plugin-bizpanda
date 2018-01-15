<?php
	/**
	 * Фильтр поиск только по заголовкам статей
	 * @param $search
	 * @param $wp_query
	 * @return array|string
	 */
	function opanda_wpse_11826_search_by_title($search, $wp_query)
	{
		if( !empty($search) && !empty($wp_query->query_vars['search_terms']) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = !empty($q['exact'])
				? ''
				: '%';

			$search = array();

			foreach($q['search_terms'] as $term)
				$search[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like($term) . $n);

			if( !is_user_logged_in() ) {
				$search[] = "$wpdb->posts.post_password = ''";
			}

			$search = ' AND ' . implode(' AND ', $search);
		}

		return $search;
	}

	/**
	 * Возвращает список найденных записей в формате JSON
	 */
	function opanda_ajax_search_post()
	{
		$search_query = isset($_POST['search_query'])
			? $_POST['search_query']
			: null;

		$post_types = isset($_POST['post_types'])
			? $_POST['post_types']
			: array('page', 'post');

		if( !is_numeric($search_query) ) {
			add_filter('posts_search', 'opanda_wpse_11826_search_by_title', 500, 2);

			$query = new WP_Query(array(
				's' => $search_query,
				'post_type' => $post_types,
				'orders' => 'DESC',
				'showposts' => 20
			));

			remove_filter('posts_search', 'opanda_wpse_11826_search_by_title', 500);
		} else {
			$query = new WP_Query(array(
				'p' => $search_query,
				'post_type' => 'any'
			));
		}

		$result = array();
		if( $query->have_posts() ) :
			while( $query->have_posts() ) :
				$query->the_post();
				$result[] = array(
					'id' => get_the_ID(),
					'text' => get_the_title()
				);
			endwhile;

			wp_reset_postdata();
		endif;

		echo @json_encode($result);
		die();
	}

	add_action('wp_ajax_opanda_search_post', 'opanda_ajax_search_post');

	/**
	 * Возвращает список найденных категорий в формате JSON
	 */
	function opanda_ajax_search_cats()
	{
		global $wpdb;

		$search_query = isset($_POST['search_query'])
			? $_POST['search_query']
			: null;

		$search_term = explode(' ', $search_query);

		$select = "
			SELECT DISTINCT t.*, tt.*
			FROM wp_terms AS t
			INNER JOIN wp_term_taxonomy AS tt
			ON t.term_id = tt.term_id
			WHERE tt.taxonomy IN ('category')";

		$first = true;
		foreach($search_term as $s) {
			if( $first ) {
				$select .= " AND (t.name LIKE '%s')";
				$string_replace[] = '%' . $wpdb->esc_like($s) . '%';
				$first = false;
			} else {
				$select .= " OR (t.name LIKE '%s')";
				$string_replace[] = '%' . $wpdb->esc_like($s) . '%';
			}
		}
		$select .= " ORDER BY t.name ASC";

		$terms = $wpdb->get_results($wpdb->prepare($select, $string_replace, ARRAY_A));

		$result = array();
		foreach($terms as $term) {
			$result[] = array(
				'id' => $term->term_id,
				'text' => $term->name
			);
		}

		echo @json_encode($result);
		die();
	}

	add_action('wp_ajax_opanda_search_cats', 'opanda_ajax_search_cats');

	/*@mix:place*/