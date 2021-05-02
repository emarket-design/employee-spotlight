<?php
/**
 * Entity Related Shortcode Functions
 *
 * @package EMPLOYEE_SPOTLIGHT
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function employee_spotlight_employee_circle_grid_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1, $shc_page_count = 0) {
	global $shc_count;
	if ($shc_page_count != 0) {
		$shc_count = $shc_page_count;
	} else {
		if (empty($shc_count)) {
			$shc_count = 1;
		} else {
			$shc_count++;
		}
	}
	$fields = Array(
		'app' => 'employee_spotlight',
		'class' => 'emd_employee',
		'shc' => 'employee_circle_grid',
		'shc_count' => $shc_count,
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => '',
		'theme' => 'na',
		'hier' => 0,
		'hier_type' => 'ul',
		'hier_depth' => - 1,
		'hier_class' => '',
		'has_json' => 0,
	);
	$args_default = array(
		'posts_per_page' => '12',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'filter' => ''
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('employee_circle_grid', 'employee_circle_grid_list');
function employee_circle_grid_list($atts) {
	$show_shc = 1;
	$show_shc = apply_filters('emd_show_shc', $show_shc, 'employee_circle_grid');
	if ($show_shc == 1) {
		wp_enqueue_script('jquery');
		wp_enqueue_style('font-awesome');
		wp_enqueue_style('pagination');
		wp_enqueue_script('matchboxlib');
		wp_enqueue_script('matchbox-js');
		wp_enqueue_style('emd-pagination');
		add_action('wp_footer', 'employee_spotlight_enq_allview');
		employee_spotlight_enq_custom_css_js();
		$list = employee_spotlight_employee_circle_grid_set_shc($atts);
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
		$list = apply_filters('emd_no_access_msg_shc', $list, 'employee_circle_grid');
	}
	return $list;
}
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function employee_spotlight_employee_circle_panel_grid_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1, $shc_page_count = 0) {
	global $shc_count;
	if ($shc_page_count != 0) {
		$shc_count = $shc_page_count;
	} else {
		if (empty($shc_count)) {
			$shc_count = 1;
		} else {
			$shc_count++;
		}
	}
	$fields = Array(
		'app' => 'employee_spotlight',
		'class' => 'emd_employee',
		'shc' => 'employee_circle_panel_grid',
		'shc_count' => $shc_count,
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => '',
		'theme' => 'na',
		'hier' => 0,
		'hier_type' => 'ul',
		'hier_depth' => - 1,
		'hier_class' => '',
		'has_json' => 0,
	);
	$args_default = array(
		'posts_per_page' => '12',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'filter' => ''
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('employee_circle_panel_grid', 'employee_circle_panel_grid_list');
function employee_circle_panel_grid_list($atts) {
	$show_shc = 1;
	$show_shc = apply_filters('emd_show_shc', $show_shc, 'employee_circle_panel_grid');
	if ($show_shc == 1) {
		wp_enqueue_script('jquery');
		wp_enqueue_style('font-awesome');
		wp_enqueue_style('pagination');
		wp_enqueue_script('matchboxlib');
		wp_enqueue_script('matchbox-js');
		wp_enqueue_style('emd-pagination');
		add_action('wp_footer', 'employee_spotlight_enq_allview');
		employee_spotlight_enq_custom_css_js();
		$list = employee_spotlight_employee_circle_panel_grid_set_shc($atts);
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
		$list = apply_filters('emd_no_access_msg_shc', $list, 'employee_circle_panel_grid');
	}
	return $list;
}
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode', 11);