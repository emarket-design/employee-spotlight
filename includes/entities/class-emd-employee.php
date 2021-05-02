<?php
/**
 * Entity Class
 *
 * @package EMPLOYEE_SPOTLIGHT
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Employee Class
 * @since WPAS 4.0
 */
class Emd_Employee extends Emd_Entity {
	protected $post_type = 'emd_employee';
	protected $textdomain = 'employee-spotlight';
	protected $sing_label;
	protected $plural_label;
	protected $menu_entity;
	protected $id;
	/**
	 * Initialize entity class
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function __construct() {
		add_action('init', array(
			$this,
			'set_filters'
		) , 1);
		add_action('admin_init', array(
			$this,
			'set_metabox'
		));
		add_filter('post_updated_messages', array(
			$this,
			'updated_messages'
		));
		add_action('admin_menu', array(
			$this,
			'add_menu_link'
		));
		add_action('admin_head-edit.php', array(
			$this,
			'add_opt_button'
		));
		$is_adv_filt_ext = apply_filters('emd_adv_filter_on', 0);
		if ($is_adv_filt_ext === 0) {
			add_action('manage_emd_employee_posts_custom_column', array(
				$this,
				'custom_columns'
			) , 10, 2);
			add_filter('manage_emd_employee_posts_columns', array(
				$this,
				'column_headers'
			));
		}
		add_filter('is_protected_meta', array(
			$this,
			'hide_attrs'
		) , 10, 2);
		add_filter('postmeta_form_keys', array(
			$this,
			'cust_keys'
		) , 10, 2);
		add_filter('emd_get_cust_fields', array(
			$this,
			'get_cust_fields'
		) , 10, 2);
		add_filter('enter_title_here', array(
			$this,
			'change_title_text'
		));
		add_filter('post_row_actions', array(
			$this,
			'duplicate_link'
		) , 10, 2);
		add_action('admin_action_emd_duplicate_entity', array(
			$this,
			'duplicate_entity'
		));
	}
	public function change_title_disable_emd_temp($title, $id) {
		$post = get_post($id);
		if ($this->post_type == $post->post_type && (!empty($this->id) && $this->id == $id)) {
			return '';
		}
		return $title;
	}
	/**
	 * Get custom attribute list
	 * @since WPAS 4.9
	 *
	 * @param array $cust_fields
	 * @param string $post_type
	 *
	 * @return array $new_keys
	 */
	public function get_cust_fields($cust_fields, $post_type) {
		global $wpdb;
		if ($post_type == $this->post_type) {
			$sql = "SELECT DISTINCT meta_key
               FROM $wpdb->postmeta a
               WHERE a.post_id IN (SELECT id FROM $wpdb->posts b WHERE b.post_type='" . $this->post_type . "')";
			$keys = $wpdb->get_col($sql);
			if (!empty($keys)) {
				foreach ($keys as $i => $mkey) {
					if (!preg_match('/^(_|wpas_|emd_)/', $mkey)) {
						$ckey = str_replace('-', '_', sanitize_title($mkey));
						$cust_fields[$ckey] = $mkey;
					}
				}
			}
		}
		return $cust_fields;
	}
	/**
	 * Set new custom attributes dropdown in admin edit entity
	 * @since WPAS 4.9
	 *
	 * @param array $keys
	 * @param object $post
	 *
	 * @return array $keys
	 */
	public function cust_keys($keys, $post) {
		global $post_type, $wpdb;
		if ($post_type == $this->post_type) {
			$sql = "SELECT DISTINCT meta_key
                FROM $wpdb->postmeta a
                WHERE a.post_id IN (SELECT id FROM $wpdb->posts b WHERE b.post_type='" . $this->post_type . "')";
			$keys = $wpdb->get_col($sql);
		}
		return $keys;
	}
	/**
	 * Hide all emd attributes
	 * @since WPAS 4.9
	 *
	 * @param bool $protected
	 * @param string $meta_key
	 *
	 * @return bool $protected
	 */
	public function hide_attrs($protected, $meta_key) {
		if (preg_match('/^(emd_|wpas_)/', $meta_key)) return true;
		if (!empty($this->boxes)) {
			foreach ($this->boxes as $mybox) {
				if (!empty($mybox['fields'])) {
					foreach ($mybox['fields'] as $fkey => $mybox_field) {
						if ($meta_key == $fkey) return true;
					}
				}
			}
		}
		return $protected;
	}
	/**
	 * Get column header list in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param array $columns
	 *
	 * @return array $columns
	 */
	public function column_headers($columns) {
		$ent_list = get_option(str_replace("-", "_", $this->textdomain) . '_ent_list');
		if (!empty($ent_list[$this->post_type]['featured_img'])) {
			$columns['featured_img'] = __('Featured Image', $this->textdomain);
		}
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if (!in_array($fkey, Array(
					'wpas_form_name',
					'wpas_form_submitted_by',
					'wpas_form_submitted_ip'
				)) && !in_array($mybox_field['type'], Array(
					'textarea',
					'wysiwyg'
				)) && $mybox_field['list_visible'] == 1) {
					$columns[$fkey] = $mybox_field['name'];
				}
			}
		}
		$taxonomies = get_object_taxonomies($this->post_type, 'objects');
		if (!empty($taxonomies)) {
			$tax_list = get_option(str_replace("-", "_", $this->textdomain) . '_tax_list');
			foreach ($taxonomies as $taxonomy) {
				if (!empty($tax_list[$this->post_type][$taxonomy->name]) && $tax_list[$this->post_type][$taxonomy->name]['list_visible'] == 1) {
					$columns[$taxonomy->name] = $taxonomy->label;
				}
			}
		}
		$rel_list = get_option(str_replace("-", "_", $this->textdomain) . '_rel_list');
		if (!empty($rel_list)) {
			foreach ($rel_list as $krel => $rel) {
				if ($rel['from'] == $this->post_type && in_array($rel['show'], Array(
					'any',
					'from'
				))) {
					$columns[$krel] = $rel['from_title'];
				} elseif ($rel['to'] == $this->post_type && in_array($rel['show'], Array(
					'any',
					'to'
				))) {
					$columns[$krel] = $rel['to_title'];
				}
			}
		}
		return $columns;
	}
	/**
	 * Get custom column values in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param int $column_id
	 * @param int $post_id
	 *
	 * @return string $value
	 */
	public function custom_columns($column_id, $post_id) {
		if (taxonomy_exists($column_id) == true) {
			$terms = get_the_terms($post_id, $column_id);
			$ret = array();
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$url = add_query_arg(array(
						'post_type' => $this->post_type,
						'term' => $term->slug,
						'taxonomy' => $column_id
					) , admin_url('edit.php'));
					$a_class = preg_replace('/^emd_/', '', $this->post_type);
					$ret[] = sprintf('<a href="%s"  class="' . $a_class . '-tax ' . $term->slug . '">%s</a>', $url, $term->name);
				}
			}
			echo implode(', ', $ret);
			return;
		}
		$rel_list = get_option(str_replace("-", "_", $this->textdomain) . '_rel_list');
		if (!empty($rel_list) && !empty($rel_list[$column_id])) {
			$rel_arr = $rel_list[$column_id];
			if ($rel_arr['from'] == $this->post_type) {
				$other_ptype = $rel_arr['to'];
			} elseif ($rel_arr['to'] == $this->post_type) {
				$other_ptype = $rel_arr['from'];
			}
			$column_id = str_replace('rel_', '', $column_id);
			if (function_exists('p2p_type') && p2p_type($column_id)) {
				$rel_args = apply_filters('emd_ext_p2p_add_query_vars', array(
					'posts_per_page' => - 1
				) , Array(
					$other_ptype
				));
				$connected = p2p_type($column_id)->get_connected($post_id, $rel_args);
				$ptype_obj = get_post_type_object($this->post_type);
				$edit_cap = $ptype_obj->cap->edit_posts;
				$ret = array();
				if (empty($connected->posts)) return '&ndash;';
				foreach ($connected->posts as $myrelpost) {
					$rel_title = get_the_title($myrelpost->ID);
					$rel_title = apply_filters('emd_ext_p2p_connect_title', $rel_title, $myrelpost, '');
					$url = get_permalink($myrelpost->ID);
					$url = apply_filters('emd_ext_connected_ptype_url', $url, $myrelpost, $edit_cap);
					$ret[] = sprintf('<a href="%s" title="%s" target="_blank">%s</a>', $url, $rel_title, $rel_title);
				}
				echo implode(', ', $ret);
				return;
			}
		}
		$value = get_post_meta($post_id, $column_id, true);
		$type = "";
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if ($fkey == $column_id) {
					$type = $mybox_field['type'];
					break;
				}
			}
		}
		if ($column_id == 'featured_img') {
			$type = 'featured_img';
		}
		switch ($type) {
			case 'featured_img':
				$thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id($post_id) , 'thumbnail');
				if (!empty($thumb_url)) {
					$value = "<img style='max-width:100%;height:auto;' src='" . $thumb_url[0] . "' >";
				}
			break;
			case 'plupload_image':
			case 'image':
			case 'thickbox_image':
				$image_list = emd_mb_meta($column_id, 'type=image');
				$value = "";
				if (!empty($image_list)) {
					$myimage = current($image_list);
					$value = "<img style='max-width:100%;height:auto;' src='" . $myimage['url'] . "' >";
				}
			break;
			case 'user':
			case 'user-adv':
				$user_id = emd_mb_meta($column_id);
				if (!empty($user_id)) {
					$user_info = get_userdata($user_id);
					$value = $user_info->display_name;
				}
			break;
			case 'file':
				$file_list = emd_mb_meta($column_id, 'type=file');
				if (!empty($file_list)) {
					$value = "";
					foreach ($file_list as $myfile) {
						$fsrc = wp_mime_type_icon($myfile['ID']);
						$value.= "<a style='margin:5px;' href='" . $myfile['url'] . "' target='_blank'><img src='" . $fsrc . "' title='" . $myfile['name'] . "' width='20' /></a>";
					}
				}
			break;
			case 'radio':
			case 'checkbox_list':
			case 'select':
			case 'select_advanced':
				$value = emd_get_attr_val(str_replace("-", "_", $this->textdomain) , $post_id, $this->post_type, $column_id);
			break;
			case 'checkbox':
				if ($value == 1) {
					$value = '<span class="dashicons dashicons-yes"></span>';
				} elseif ($value == 0) {
					$value = '<span class="dashicons dashicons-no-alt"></span>';
				}
			break;
			case 'rating':
				$value = apply_filters('emd_get_rating_value', $value, Array(
					'meta' => $column_id
				) , $post_id);
			break;
		}
		if (is_array($value)) {
			$value = "<div class='clonelink'>" . implode("</div><div class='clonelink'>", $value) . "</div>";
		}
		echo $value;
	}
	/**
	 * Register post type and taxonomies and set initial values for taxs
	 *
	 * @since WPAS 4.0
	 *
	 */
	public static function register() {
		$labels = array(
			'name' => __('Employees', 'employee-spotlight') ,
			'singular_name' => __('Employee', 'employee-spotlight') ,
			'add_new' => __('Add New', 'employee-spotlight') ,
			'add_new_item' => __('Add New Employee', 'employee-spotlight') ,
			'edit_item' => __('Edit Employee', 'employee-spotlight') ,
			'new_item' => __('New Employee', 'employee-spotlight') ,
			'all_items' => __('All Employees', 'employee-spotlight') ,
			'view_item' => __('View Employee', 'employee-spotlight') ,
			'search_items' => __('Search Employees', 'employee-spotlight') ,
			'not_found' => __('No Employees Found', 'employee-spotlight') ,
			'not_found_in_trash' => __('No Employees Found In Trash', 'employee-spotlight') ,
			'menu_name' => __('Employees', 'employee-spotlight') ,
		);
		$ent_map_list = get_option('employee_spotlight_ent_map_list', Array());
		$myrole = emd_get_curr_usr_role('employee_spotlight');
		if (!empty($ent_map_list['emd_employee']['rewrite'])) {
			$rewrite = $ent_map_list['emd_employee']['rewrite'];
		} else {
			$rewrite = 'employees';
		}
		$supports = Array();
		if (empty($ent_map_list['emd_employee']['attrs']['blt_title']) || $ent_map_list['emd_employee']['attrs']['blt_title'] != 'hide') {
			if (empty($ent_map_list['emd_employee']['edit_attrs'])) {
				$supports[] = 'title';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'title';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_title']) && $ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_title'] == 'edit') {
				$supports[] = 'title';
			}
		}
		if (empty($ent_map_list['emd_employee']['attrs']['blt_content']) || $ent_map_list['emd_employee']['attrs']['blt_content'] != 'hide') {
			if (empty($ent_map_list['emd_employee']['edit_attrs'])) {
				$supports[] = 'editor';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'editor';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_content']) && $ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_content'] == 'edit') {
				$supports[] = 'editor';
			}
		}
		if (empty($ent_map_list['emd_employee']['attrs']['blt_excerpt']) || $ent_map_list['emd_employee']['attrs']['blt_excerpt'] != 'hide') {
			if (empty($ent_map_list['emd_employee']['edit_attrs'])) {
				$supports[] = 'excerpt';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'excerpt';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_excerpt']) && $ent_map_list['emd_employee']['edit_attrs'][$myrole]['blt_excerpt'] == 'edit') {
				$supports[] = 'excerpt';
			}
		}
		register_post_type('emd_employee', array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'description' => __('Employees are human resources that work for your organization. Employees can be identified as staff, team members, founders, or contractors.', 'employee-spotlight') ,
			'show_in_menu' => true,
			'menu_position' => 6,
			'has_archive' => true,
			'exclude_from_search' => false,
			'rewrite' => array(
				'slug' => $rewrite
			) ,
			'can_export' => true,
			'show_in_rest' => false,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-id',
			'map_meta_cap' => 'false',
			'taxonomies' => array() ,
			'capability_type' => 'post',
			'supports' => $supports,
		));
		$tax_settings = get_option('employee_spotlight_tax_settings', Array());
		$myrole = emd_get_curr_usr_role('employee_spotlight');
		$groups_nohr_labels = array(
			'name' => __('Groups', 'employee-spotlight') ,
			'singular_name' => __('Group', 'employee-spotlight') ,
			'search_items' => __('Search Groups', 'employee-spotlight') ,
			'popular_items' => __('Popular Groups', 'employee-spotlight') ,
			'all_items' => __('All', 'employee-spotlight') ,
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit Group', 'employee-spotlight') ,
			'update_item' => __('Update Group', 'employee-spotlight') ,
			'add_new_item' => __('Add New Group', 'employee-spotlight') ,
			'new_item_name' => __('Add New Group Name', 'employee-spotlight') ,
			'separate_items_with_commas' => __('Seperate Groups with commas', 'employee-spotlight') ,
			'add_or_remove_items' => __('Add or Remove Groups', 'employee-spotlight') ,
			'choose_from_most_used' => __('Choose from the most used Groups', 'employee-spotlight') ,
			'menu_name' => __('Groups', 'employee-spotlight') ,
		);
		if (empty($tax_settings['groups']['hide']) || (!empty($tax_settings['groups']['hide']) && $tax_settings['groups']['hide'] != 'hide')) {
			if (!empty($tax_settings['groups']['rewrite'])) {
				$rewrite = $tax_settings['groups']['rewrite'];
			} else {
				$rewrite = 'groups';
			}
			$targs = array(
				'hierarchical' => false,
				'labels' => $groups_nohr_labels,
				'public' => true,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_menu' => true,
				'show_tagcloud' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array(
					'slug' => $rewrite,
				) ,
				'show_in_rest' => false,
				'capabilities' => array(
					'manage_terms' => 'manage_groups',
					'edit_terms' => 'edit_groups',
					'delete_terms' => 'delete_groups',
					'assign_terms' => 'assign_groups'
				) ,
			);
			if ($myrole != 'administrator' && !empty($tax_settings['groups']['edit'][$myrole]) && $tax_settings['groups']['edit'][$myrole] != 'edit') {
				$targs['meta_box_cb'] = false;
			}
			register_taxonomy('groups', array(
				'emd_employee'
			) , $targs);
		}
		$employee_tags_nohr_labels = array(
			'name' => __('Employee Tags', 'employee-spotlight') ,
			'singular_name' => __('Employee Tag', 'employee-spotlight') ,
			'search_items' => __('Search Employee Tags', 'employee-spotlight') ,
			'popular_items' => __('Popular Employee Tags', 'employee-spotlight') ,
			'all_items' => __('All', 'employee-spotlight') ,
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit Employee Tag', 'employee-spotlight') ,
			'update_item' => __('Update Employee Tag', 'employee-spotlight') ,
			'add_new_item' => __('Add New Employee Tag', 'employee-spotlight') ,
			'new_item_name' => __('Add New Employee Tag Name', 'employee-spotlight') ,
			'separate_items_with_commas' => __('Seperate Employee Tags with commas', 'employee-spotlight') ,
			'add_or_remove_items' => __('Add or Remove Employee Tags', 'employee-spotlight') ,
			'choose_from_most_used' => __('Choose from the most used Employee Tags', 'employee-spotlight') ,
			'menu_name' => __('Employee Tags', 'employee-spotlight') ,
		);
		if (empty($tax_settings['employee_tags']['hide']) || (!empty($tax_settings['employee_tags']['hide']) && $tax_settings['employee_tags']['hide'] != 'hide')) {
			if (!empty($tax_settings['employee_tags']['rewrite'])) {
				$rewrite = $tax_settings['employee_tags']['rewrite'];
			} else {
				$rewrite = 'employee_tags';
			}
			$targs = array(
				'hierarchical' => false,
				'labels' => $employee_tags_nohr_labels,
				'public' => true,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_menu' => true,
				'show_tagcloud' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array(
					'slug' => $rewrite,
				) ,
				'show_in_rest' => false,
				'capabilities' => array(
					'manage_terms' => 'manage_employee_tags',
					'edit_terms' => 'edit_employee_tags',
					'delete_terms' => 'delete_employee_tags',
					'assign_terms' => 'assign_employee_tags'
				) ,
			);
			if ($myrole != 'administrator' && !empty($tax_settings['employee_tags']['edit'][$myrole]) && $tax_settings['employee_tags']['edit'][$myrole] != 'edit') {
				$targs['meta_box_cb'] = false;
			}
			register_taxonomy('employee_tags', array(
				'emd_employee'
			) , $targs);
		}
		$office_locations_nohr_labels = array(
			'name' => __('Locations', 'employee-spotlight') ,
			'singular_name' => __('Location', 'employee-spotlight') ,
			'search_items' => __('Search Locations', 'employee-spotlight') ,
			'popular_items' => __('Popular Locations', 'employee-spotlight') ,
			'all_items' => __('All', 'employee-spotlight') ,
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit Location', 'employee-spotlight') ,
			'update_item' => __('Update Location', 'employee-spotlight') ,
			'add_new_item' => __('Add New Location', 'employee-spotlight') ,
			'new_item_name' => __('Add New Location Name', 'employee-spotlight') ,
			'separate_items_with_commas' => __('Seperate Locations with commas', 'employee-spotlight') ,
			'add_or_remove_items' => __('Add or Remove Locations', 'employee-spotlight') ,
			'choose_from_most_used' => __('Choose from the most used Locations', 'employee-spotlight') ,
			'menu_name' => __('Locations', 'employee-spotlight') ,
		);
		if (empty($tax_settings['office_locations']['hide']) || (!empty($tax_settings['office_locations']['hide']) && $tax_settings['office_locations']['hide'] != 'hide')) {
			if (!empty($tax_settings['office_locations']['rewrite'])) {
				$rewrite = $tax_settings['office_locations']['rewrite'];
			} else {
				$rewrite = 'office_locations';
			}
			$targs = array(
				'hierarchical' => false,
				'labels' => $office_locations_nohr_labels,
				'public' => true,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_menu' => true,
				'show_tagcloud' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array(
					'slug' => $rewrite,
				) ,
				'show_in_rest' => false,
				'capabilities' => array(
					'manage_terms' => 'manage_office_locations',
					'edit_terms' => 'edit_office_locations',
					'delete_terms' => 'delete_office_locations',
					'assign_terms' => 'assign_office_locations'
				) ,
			);
			if ($myrole != 'administrator' && !empty($tax_settings['office_locations']['edit'][$myrole]) && $tax_settings['office_locations']['edit'][$myrole] != 'edit') {
				$targs['meta_box_cb'] = false;
			}
			register_taxonomy('office_locations', array(
				'emd_employee'
			) , $targs);
		}
	}
	/**
	 * Set metabox fields,labels,filters, comments, relationships if exists
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function set_filters() {
		do_action('emd_ext_class_init', $this);
		$search_args = Array();
		$filter_args = Array();
		$this->sing_label = __('Employee', 'employee-spotlight');
		$this->plural_label = __('Employees', 'employee-spotlight');
		$this->menu_entity = 'emd_employee';
		$this->boxes['emd_employee_info_emd_employee_0'] = array(
			'id' => 'emd_employee_info_emd_employee_0',
			'title' => __('Employee Info', 'employee-spotlight') ,
			'app_name' => 'employee_spotlight',
			'pages' => array(
				'emd_employee'
			) ,
			'context' => 'normal',
		);
		$this->boxes['emd_cust_field_meta_box'] = array(
			'id' => 'emd_cust_field_meta_box',
			'title' => __('Custom Fields', 'employee-spotlight') ,
			'app_name' => 'employee_spotlight',
			'pages' => array(
				'emd_employee'
			) ,
			'context' => 'normal',
			'priority' => 'low'
		);
		list($search_args, $filter_args) = $this->set_args_boxes();
		if (empty($this->boxes['emd_cust_field_meta_box']['fields'])) {
			unset($this->boxes['emd_cust_field_meta_box']);
		}
		if (!post_type_exists($this->post_type) || in_array($this->post_type, Array(
			'post',
			'page'
		))) {
			self::register();
		}
		do_action('emd_set_adv_filtering', $this->post_type, $search_args, $this->boxes, $filter_args, $this->textdomain, $this->plural_label);
		add_action('admin_notices', array(
			$this,
			'show_lite_filters'
		));
		$ent_map_list = get_option(str_replace('-', '_', $this->textdomain) . '_ent_map_list');
	}
	/**
	 * Initialize metaboxes
	 * @since WPAS 4.5
	 *
	 */
	public function set_metabox() {
		if (class_exists('EMD_Meta_Box') && is_array($this->boxes)) {
			foreach ($this->boxes as $meta_box) {
				new EMD_Meta_Box($meta_box);
			}
		}
	}
	/**
	 * Change content for created frontend views
	 * @since WPAS 4.0
	 * @param string $content
	 *
	 * @return string $content
	 */
	public function change_content($content) {
		global $post;
		$layout = "";
		$this->id = $post->ID;
		$tools = get_option('employee_spotlight_tools');
		if (!empty($tools['disable_emd_templates'])) {
			add_filter('the_title', array(
				$this,
				'change_title_disable_emd_temp'
			) , 10, 2);
		}
		if (get_post_type() == $this->post_type && is_single()) {
			ob_start();
			do_action('emd_single_before_content', $this->textdomain, $this->post_type);
			emd_get_template_part($this->textdomain, 'single', 'emd-employee');
			do_action('emd_single_after_content', $this->textdomain, $this->post_type);
			$layout = ob_get_clean();
		} elseif (is_post_type_archive('emd_employee')) {
			ob_start();
			do_action('emd_single_before_content', $this->textdomain, $this->post_type);
			emd_get_template_part($this->textdomain, 'archive', 'emd-employee');
			do_action('emd_single_after_content', $this->textdomain, $this->post_type);
			$layout = ob_get_clean();
		} elseif (is_tax('groups') && $post->post_type == $this->post_type) {
			ob_start();
			do_action('emd_single_before_content', $this->textdomain, $this->post_type);
			emd_get_template_part($this->textdomain, 'taxonomy', 'groups-emd-employee');
			do_action('emd_single_after_content', $this->textdomain, $this->post_type);
			$layout = ob_get_clean();
		} elseif (is_tax('office_locations') && $post->post_type == $this->post_type) {
			ob_start();
			do_action('emd_single_before_content', $this->textdomain, $this->post_type);
			emd_get_template_part($this->textdomain, 'taxonomy', 'office-locations-emd-employee');
			do_action('emd_single_after_content', $this->textdomain, $this->post_type);
			$layout = ob_get_clean();
		} elseif (is_tax('employee_tags') && $post->post_type == $this->post_type) {
			ob_start();
			do_action('emd_single_before_content', $this->textdomain, $this->post_type);
			emd_get_template_part($this->textdomain, 'taxonomy', 'employee-tags-emd-employee');
			do_action('emd_single_after_content', $this->textdomain, $this->post_type);
			$layout = ob_get_clean();
		}
		if ($layout != "") {
			$content = $layout;
		}
		if (!empty($tools['disable_emd_templates'])) {
			remove_filter('the_title', array(
				$this,
				'change_title_disable_emd_temp'
			) , 10, 2);
		}
		return $content;
	}
	/**
	 * Add operations and add new submenu hook
	 * @since WPAS 4.4
	 */
	public function add_menu_link() {
		add_submenu_page(null, __('CSV Import/Export', 'employee-spotlight') , __('CSV Import/Export', 'employee-spotlight') , 'manage_operations_emd_employees', 'operations_emd_employee', array(
			$this,
			'get_operations'
		));
	}
	/**
	 * Display operations page
	 * @since WPAS 4.0
	 */
	public function get_operations() {
		if (current_user_can('manage_operations_emd_employees')) {
			$myapp = str_replace("-", "_", $this->textdomain);
			if (!function_exists('emd_operations_entity')) {
				emd_lite_get_operations('opr', $this->plural_label, $this->textdomain);
			} else {
				do_action('emd_operations_entity', $this->post_type, $this->plural_label, $this->sing_label, $myapp, $this->menu_entity);
			}
		}
	}
	public function change_title_text($title) {
		$screen = get_current_screen();
		if ($this->post_type == $screen->post_type) {
			$title = __('Enter Full Name here', 'employee-spotlight');
		}
		return $title;
	}
	public function show_lite_filters() {
		if (class_exists('EMD_AFC')) {
			return;
		}
		global $pagenow;
		if (get_post_type() == $this->post_type && $pagenow == 'edit.php') {
			emd_lite_get_filters($this->textdomain);
		}
	}
}
new Emd_Employee;