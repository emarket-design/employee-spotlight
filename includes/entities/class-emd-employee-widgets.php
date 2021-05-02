<?php
/**
 * Entity Widget Classes
 *
 * @package EMPLOYEE_SPOTLIGHT
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class employee_spotlight_recent_employees_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'employee-spotlight';
	public $class_label;
	public $class = 'emd_employee';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'recent-members';
	public $id = 'employee_spotlight_recent_employees_widget';
	public $query_args = array(
		'post_type' => 'emd_employee',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'context' => 'employee_spotlight_recent_employees_widget',
	);
	public $filter = '';
	public $header = '';
	public $footer = '';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	public function __construct() {
		parent::__construct($this->id, __('Recent Members', 'employee-spotlight') , __('Employees', 'employee-spotlight') , __('The most recent employees', 'employee-spotlight'));
	}
	/**
	 * Get header and footer for layout
	 *
	 * @since WPAS 4.6
	 */
	protected function get_header_footer() {
	}
	/**
	 * Enqueue css and js for widget
	 *
	 * @since WPAS 4.5
	 */
	protected function enqueue_scripts() {
		if (is_active_widget(false, false, $this->id_base) && !is_admin()) {
			wp_enqueue_style('empswidcss');
		}
		employee_spotlight_enq_custom_css_js();
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		ob_start();
		emd_get_template_part('employee_spotlight', 'widget', 'recent-employees-content');
		$layout = ob_get_clean();
		return $layout;
	}
}
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class employee_spotlight_featured_employees_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'employee-spotlight';
	public $class_label;
	public $class = 'emd_employee';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'featured-employees';
	public $id = 'employee_spotlight_featured_employees_widget';
	public $query_args = array(
		'post_type' => 'emd_employee',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'context' => 'employee_spotlight_featured_employees_widget',
	);
	public $filter = 'attr::emd_employee_featured::is::1';
	public $header = '';
	public $footer = '';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	public function __construct() {
		parent::__construct($this->id, __('Featured Employees', 'employee-spotlight') , __('Employees', 'employee-spotlight') , __('The most recent employees', 'employee-spotlight'));
	}
	/**
	 * Get header and footer for layout
	 *
	 * @since WPAS 4.6
	 */
	protected function get_header_footer() {
	}
	/**
	 * Enqueue css and js for widget
	 *
	 * @since WPAS 4.5
	 */
	protected function enqueue_scripts() {
		if (is_active_widget(false, false, $this->id_base) && !is_admin()) {
			wp_enqueue_style('empswidcss');
		}
		employee_spotlight_enq_custom_css_js();
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		ob_start();
		emd_get_template_part('employee_spotlight', 'widget', 'featured-employees-content');
		$layout = ob_get_clean();
		return $layout;
	}
}
$access_views = get_option('employee_spotlight_access_views', Array());
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('recent_employees', $access_views['widgets']) && current_user_can('view_recent_employees'))) {
	register_widget('employee_spotlight_recent_employees_widget');
}
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('featured_employees', $access_views['widgets']) && current_user_can('view_featured_employees'))) {
	register_widget('employee_spotlight_featured_employees_widget');
}