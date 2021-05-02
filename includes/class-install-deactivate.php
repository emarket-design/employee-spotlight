<?php
/**
 * Install and Deactivate Plugin Functions
 * @package EMPLOYEE_SPOTLIGHT
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
if (!class_exists('Employee_Spotlight_Install_Deactivate')):
	/**
	 * Employee_Spotlight_Install_Deactivate Class
	 * @since WPAS 4.0
	 */
	class Employee_Spotlight_Install_Deactivate {
		private $option_name;
		/**
		 * Hooks for install and deactivation and create options
		 * @since WPAS 4.0
		 */
		public function __construct() {
			$this->option_name = 'employee_spotlight';
			add_action('admin_init', array(
				$this,
				'check_update'
			));
			register_activation_hook(EMPLOYEE_SPOTLIGHT_PLUGIN_FILE, array(
				$this,
				'install'
			));
			register_deactivation_hook(EMPLOYEE_SPOTLIGHT_PLUGIN_FILE, array(
				$this,
				'deactivate'
			));
			add_action('wp_head', array(
				$this,
				'version_in_header'
			));
			add_action('admin_init', array(
				$this,
				'setup_pages'
			));
			add_action('admin_notices', array(
				$this,
				'install_notice'
			));
			add_action('generate_rewrite_rules', 'emd_create_rewrite_rules');
			add_filter('query_vars', 'emd_query_vars');
			add_action('admin_init', array(
				$this,
				'register_settings'
			) , 0);
			add_action('before_delete_post', array(
				$this,
				'delete_post_file_att'
			));
			add_filter('get_media_item_args', 'emd_media_item_args');
			add_action('init', array(
				$this,
				'init_extensions'
			) , 99);
			do_action('emd_ext_actions', $this->option_name);
			add_filter('tiny_mce_before_init', array(
				$this,
				'tinymce_fix'
			));
		}
		public function check_update() {
			$curr_version = get_option($this->option_name . '_version', 1);
			$new_version = constant(strtoupper($this->option_name) . '_VERSION');
			if (version_compare($curr_version, $new_version, '<')) {
				$this->set_options();
				$this->set_roles_caps();
				if (!get_option($this->option_name . '_activation_date')) {
					$triggerdate = mktime(0, 0, 0, date('m') , date('d') + 7, date('Y'));
					add_option($this->option_name . '_activation_date', $triggerdate);
				}
				set_transient($this->option_name . '_activate_redirect', true, 30);
				do_action($this->option_name . '_upgrade', $new_version);
				update_option($this->option_name . '_version', $new_version);
			}
		}
		public function version_in_header() {
			$version = constant(strtoupper($this->option_name) . '_VERSION');
			$name = constant(strtoupper($this->option_name) . '_NAME');
			echo '<meta name="generator" content="' . $name . ' v' . $version . ' - https://emdplugins.com" />' . "\n";
		}
		public function init_extensions() {
			do_action('emd_ext_init', $this->option_name);
		}
		/**
		 * Runs on plugin install to setup custom post types and taxonomies
		 * flushing rewrite rules, populates settings and options
		 * creates roles and assign capabilities
		 * @since WPAS 4.0
		 *
		 */
		public function install() {
			$this->set_options();
			Emd_Employee::register();
			flush_rewrite_rules();
			$this->set_roles_caps();
			set_transient($this->option_name . '_activate_redirect', true, 30);
			do_action('emd_ext_install_hook', $this->option_name);
		}
		/**
		 * Runs on plugin deactivate to remove options, caps and roles
		 * flushing rewrite rules
		 * @since WPAS 4.0
		 *
		 */
		public function deactivate() {
			flush_rewrite_rules();
			$this->remove_caps_roles();
			$this->reset_options();
			do_action('emd_ext_deactivate', $this->option_name);
		}
		/**
		 * Register notification and/or license settings
		 * @since WPAS 4.0
		 *
		 */
		public function register_settings() {
			do_action('emd_ext_register', $this->option_name);
			if (!get_transient($this->option_name . '_activate_redirect')) {
				return;
			}
			// Delete the redirect transient.
			delete_transient($this->option_name . '_activate_redirect');
			$query_args = array(
				'page' => $this->option_name
			);
			wp_safe_redirect(add_query_arg($query_args, admin_url('admin.php')));
		}
		/**
		 * Sets caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function set_roles_caps() {
			global $wp_roles;
			$cust_roles = Array();
			update_option($this->option_name . '_cust_roles', $cust_roles);
			$add_caps = Array(
				'edit_groups' => Array(
					'administrator'
				) ,
				'manage_groups' => Array(
					'administrator'
				) ,
				'assign_groups' => Array(
					'administrator'
				) ,
				'manage_employee_tags' => Array(
					'administrator'
				) ,
				'manage_operations_emd_employees' => Array(
					'administrator'
				) ,
				'view_employee_spotlight_dashboard' => Array(
					'administrator'
				) ,
				'edit_employee_tags' => Array(
					'administrator'
				) ,
				'manage_office_locations' => Array(
					'administrator'
				) ,
				'delete_office_locations' => Array(
					'administrator'
				) ,
				'edit_emd_employees' => Array(
					'administrator'
				) ,
				'delete_employee_tags' => Array(
					'administrator'
				) ,
				'assign_employee_tags' => Array(
					'administrator'
				) ,
				'edit_office_locations' => Array(
					'administrator'
				) ,
				'delete_groups' => Array(
					'administrator'
				) ,
				'assign_office_locations' => Array(
					'administrator'
				) ,
			);
			update_option($this->option_name . '_add_caps', $add_caps);
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				if (!empty($cust_roles)) {
					foreach ($cust_roles as $krole => $vrole) {
						$myrole = get_role($krole);
						if (empty($myrole)) {
							$myrole = add_role($krole, $vrole);
						}
					}
				}
				$this->set_reset_caps($wp_roles, 'add');
			}
		}
		/**
		 * Removes caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function remove_caps_roles() {
			global $wp_roles;
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				$this->set_reset_caps($wp_roles, 'remove');
			}
		}
		/**
		 * Set  capabilities
		 *
		 * @since WPAS 4.0
		 * @param object $wp_roles
		 * @param string $type
		 *
		 */
		public function set_reset_caps($wp_roles, $type) {
			$caps['enable'] = get_option($this->option_name . '_add_caps', Array());
			$caps['enable'] = apply_filters('emd_ext_get_caps', $caps['enable'], $this->option_name);
			foreach ($caps as $stat => $role_caps) {
				foreach ($role_caps as $mycap => $roles) {
					foreach ($roles as $myrole) {
						if (($type == 'add' && $stat == 'enable') || ($stat == 'disable' && $type == 'remove')) {
							$wp_roles->add_cap($myrole, $mycap);
						} else if (($type == 'remove' && $stat == 'enable') || ($type == 'add' && $stat == 'disable')) {
							$wp_roles->remove_cap($myrole, $mycap);
						}
					}
				}
			}
		}
		/**
		 * Set app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function set_options() {
			$access_views = Array();
			if (get_option($this->option_name . '_setup_pages', 0) == 0) {
				update_option($this->option_name . '_setup_pages', 1);
			}
			$ent_list = Array(
				'emd_employee' => Array(
					'label' => __('Employees', 'employee-spotlight') ,
					'rewrite' => 'employees',
					'archive_view' => 1,
					'rest_api' => 0,
					'sortable' => 0,
					'searchable' => 1,
					'class_title' => Array(
						'blt_title'
					) ,
					'unique_keys' => Array(
						'blt_title'
					) ,
					'blt_list' => Array(
						'blt_content' => __('Bio', 'employee-spotlight') ,
						'blt_excerpt' => __('Excerpt', 'employee-spotlight') ,
					) ,
					'req_blt' => Array(
						'blt_title' => Array(
							'msg' => __('Full Name', 'employee-spotlight')
						) ,
					) ,
				) ,
			);
			update_option($this->option_name . '_ent_list', $ent_list);
			$shc_list['app'] = 'Employee Spotlight';
			$shc_list['has_gmap'] = 0;
			$shc_list['has_form_lite'] = 1;
			$shc_list['has_lite'] = 1;
			$shc_list['has_bs'] = 0;
			$shc_list['has_autocomplete'] = 0;
			$shc_list['remove_vis'] = 0;
			$shc_list['shcs']['employee_circle_grid'] = Array(
				"class_name" => "emd_employee",
				"type" => "std",
				'page_title' => __('Employee Circle Grid', 'employee-spotlight') ,
			);
			$shc_list['shcs']['employee_circle_panel_grid'] = Array(
				"class_name" => "emd_employee",
				"type" => "std",
				'page_title' => __('Employee Circle Panel Grid', 'employee-spotlight') ,
			);
			if (!empty($shc_list)) {
				update_option($this->option_name . '_shc_list', $shc_list);
			}
			$attr_list['emd_employee']['emd_employee_featured'] = Array(
				'label' => __('Featured', 'employee-spotlight') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'mid' => 'emd_employee_info_emd_employee_0',
				'desc' => __('Sets employee as featured which can be used to select employees in available views using Visual Shortcode Builder and Featured employee widget.', 'employee-spotlight') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_employee']['emd_employee_photo'] = Array(
				'label' => __('Photo', 'employee-spotlight') ,
				'display_type' => 'thickbox_image',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'mid' => 'emd_employee_info_emd_employee_0',
				'desc' => __('Photo of the employee. 250x250 is the preferred size.', 'employee-spotlight') ,
				'type' => 'char',
				'max_file_uploads' => 1,
				'file_ext' => 'jpg,jpeg,png,gif',
			);
			$attr_list['emd_employee']['emd_employee_jobtitle'] = Array(
				'label' => __('Job Title', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_primary_address'] = Array(
				'label' => __('Primary Address', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_phone'] = Array(
				'label' => __('Phone', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_mobile'] = Array(
				'label' => __('Mobile', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_email'] = Array(
				'label' => __('Email', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
				'email' => true,
			);
			$attr_list['emd_employee']['emd_employee_facebook'] = Array(
				'label' => __('Facebook', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_google'] = Array(
				'label' => __('Google+', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_twitter'] = Array(
				'label' => __('Twitter', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_linkedin'] = Array(
				'label' => __('Linkedin', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_github'] = Array(
				'label' => __('Github', 'employee-spotlight') ,
				'display_type' => 'text',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'char',
			);
			$attr_list['emd_employee']['emd_employee_birthday'] = Array(
				'label' => __('Birthday', 'employee-spotlight') ,
				'display_type' => 'date',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'mid' => 'emd_employee_info_emd_employee_0',
				'type' => 'date',
				'dformat' => array(
					'dateFormat' => 'mm-dd-yy'
				) ,
				'date_format' => 'm-d-Y',
			);
			$attr_list = apply_filters('emd_ext_attr_list', $attr_list, $this->option_name);
			if (!empty($attr_list)) {
				update_option($this->option_name . '_attr_list', $attr_list);
			}
			$glob_list['glb_imgborder_color'] = Array(
				'label' => __('Image Border Color', 'employee-spotlight') ,
				'type' => 'text',
				'desc' => 'Sets the color of image border. If you put in "rgba(0, 0, 0, 0)" (without quotes), it will not display border.',
				'in_form' => 0,
				'values' => '',
				'dflt' => '#4abaae',
				'required' => 0,
				'shc_list' => Array(
					'employee_circle_grid',
					'single_employee',
					'employee_circle_panel_grid',
					'archive_employee',
					'employee_groups',
					'employee_locations',
					'employee_tags'
				) ,
				'val' => '#4abaae'
			);
			if (!empty($glob_list)) {
				update_option($this->option_name . '_glob_init_list', $glob_list);
				if (get_option($this->option_name . '_glob_list') === false) {
					update_option($this->option_name . '_glob_list', $glob_list);
				}
			}
			if (!empty($glob_forms_list)) {
				update_option($this->option_name . '_glob_forms_init_list', $glob_forms_list);
				if (get_option($this->option_name . '_glob_forms_list') === false) {
					update_option($this->option_name . '_glob_forms_list', $glob_forms_list);
				}
			}
			$tax_list['emd_employee']['groups'] = Array(
				'archive_view' => 1,
				'label' => __('Groups', 'employee-spotlight') ,
				'single_label' => __('Group', 'employee-spotlight') ,
				'default' => '',
				'type' => 'multi',
				'hier' => 0,
				'sortable' => 0,
				'list_visible' => 0,
				'required' => 0,
				'srequired' => 0,
				'rewrite' => 'groups'
			);
			$tax_list['emd_employee']['office_locations'] = Array(
				'archive_view' => 1,
				'label' => __('Locations', 'employee-spotlight') ,
				'single_label' => __('Location', 'employee-spotlight') ,
				'default' => '',
				'type' => 'multi',
				'hier' => 0,
				'sortable' => 0,
				'list_visible' => 0,
				'required' => 0,
				'srequired' => 0,
				'rewrite' => 'office_locations'
			);
			$tax_list['emd_employee']['employee_tags'] = Array(
				'archive_view' => 1,
				'label' => __('Employee Tags', 'employee-spotlight') ,
				'single_label' => __('Employee Tag', 'employee-spotlight') ,
				'default' => '',
				'type' => 'multi',
				'hier' => 0,
				'sortable' => 0,
				'list_visible' => 0,
				'required' => 0,
				'srequired' => 0,
				'rewrite' => 'employee_tags'
			);
			$tax_list = apply_filters('emd_ext_tax_list', $tax_list, $this->option_name);
			if (!empty($tax_list)) {
				update_option($this->option_name . '_tax_list', $tax_list);
			}
			$emd_activated_plugins = get_option('emd_activated_plugins');
			if (!$emd_activated_plugins) {
				update_option('emd_activated_plugins', Array(
					'employee-spotlight'
				));
			} elseif (!in_array('employee-spotlight', $emd_activated_plugins)) {
				array_push($emd_activated_plugins, 'employee-spotlight');
				update_option('emd_activated_plugins', $emd_activated_plugins);
			}
			//conf parameters for incoming email
			//conf parameters for inline entity
			//conf parameters for calendar
			//conf parameters for ldap
			$has_ldap = Array(
				'employee_ldap' => 'emd_employee'
			);
			update_option($this->option_name . '_has_ldap', $has_ldap);
			//action to configure different extension conf parameters for this plugin
			do_action('emd_ext_set_conf', 'employee-spotlight');
		}
		/**
		 * Reset app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function reset_options() {
			delete_option($this->option_name . '_shc_list');
			delete_option($this->option_name . '_has_ldap');
			do_action('emd_ext_reset_conf', 'employee-spotlight');
		}
		/**
		 * Show admin notices
		 *
		 * @since WPAS 4.0
		 *
		 * @return html
		 */
		public function install_notice() {
			if (isset($_GET[$this->option_name . '_adm_notice1'])) {
				update_option($this->option_name . '_adm_notice1', true);
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_adm_notice1') != 1) {
?>
<div class="updated">
<?php
				printf('<p><a href="%1s" target="_blank"> %2$s </a>%3$s<a style="float:right;" href="%4$s"><span class="dashicons dashicons-dismiss" style="font-size:15px;"></span>%5$s</a></p>', 'https://docs.emdplugins.com/docs/employee-spotlight-community-documentation/?pk_campaign=empslight-com&pk_source=plugin&pk_medium=link&pk_content=notice', __('New To Employee Spotlight? Review the documentation!', 'wpas') , __('&#187;', 'wpas') , esc_url(add_query_arg($this->option_name . '_adm_notice1', true)) , __('Dismiss', 'wpas'));
?>
</div>
<?php
			}
			if (isset($_GET[$this->option_name . '_adm_notice2'])) {
				update_option($this->option_name . '_adm_notice2', true);
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_adm_notice2') != 1) {
?>
<div class="updated">
<?php
				printf('<p><a href="%1s" target="_blank"> %2$s </a>%3$s<a style="float:right;" href="%4$s"><span class="dashicons dashicons-dismiss" style="font-size:15px;"></span>%5$s</a></p>', 'https://emdplugins.com/plugins/employee-spotlight-wordpress-plugin?pk_campaign=empslight-com&pk_source=plugin&pk_medium=link&pk_content=notice', __('Showcase your team\'s talent with more features!', 'wpas') , __('&#187;', 'wpas') , esc_url(add_query_arg($this->option_name . '_adm_notice2', true)) , __('Dismiss', 'wpas'));
?>
</div>
<?php
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_setup_pages') == 1) {
				echo "<div id=\"message\" class=\"updated\"><p><strong>" . __('Welcome to Employee Spotlight', 'employee-spotlight') . "</strong></p>
           <p class=\"submit\"><a href=\"" . add_query_arg('setup_employee_spotlight_pages', 'true', admin_url('index.php')) . "\" class=\"button-primary\">" . __('Setup Employee Spotlight Pages', 'employee-spotlight') . "</a> <a class=\"skip button-primary\" href=\"" . add_query_arg('skip_setup_employee_spotlight_pages', 'true', admin_url('index.php')) . "\">" . __('Skip setup', 'employee-spotlight') . "</a></p>
         </div>";
			}
		}
		/**
		 * Setup pages for components and redirect to dashboard
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function setup_pages() {
			if (!is_admin()) {
				return;
			}
			if (!empty($_GET['setup_' . $this->option_name . '_pages'])) {
				$shc_list = get_option($this->option_name . '_shc_list');
				emd_create_install_pages($this->option_name, $shc_list);
				update_option($this->option_name . '_setup_pages', 2);
				wp_redirect(admin_url('admin.php?page=' . $this->option_name . '_settings&employee-spotlight-installed=true'));
				exit;
			}
			if (!empty($_GET['skip_setup_' . $this->option_name . '_pages'])) {
				update_option($this->option_name . '_setup_pages', 2);
				wp_redirect(admin_url('admin.php?page=' . $this->option_name . '_settings'));
				exit;
			}
		}
		/**
		 * Delete file attachments when a post is deleted
		 *
		 * @since WPAS 4.0
		 * @param $pid
		 *
		 * @return bool
		 */
		public function delete_post_file_att($pid) {
			$entity_fields = get_option($this->option_name . '_attr_list');
			$post_type = get_post_type($pid);
			if (!empty($entity_fields[$post_type])) {
				//Delete fields
				foreach (array_keys($entity_fields[$post_type]) as $myfield) {
					if (in_array($entity_fields[$post_type][$myfield]['display_type'], Array(
						'file',
						'image',
						'plupload_image',
						'thickbox_image'
					))) {
						$pmeta = get_post_meta($pid, $myfield);
						if (!empty($pmeta)) {
							foreach ($pmeta as $file_id) {
								//check if this file is used for another post
								$fargs = array(
									'meta_query' => array(
										array(
											'key' => $myfield,
											'value' => $file_id,
											'compare' => '=',
										)
									) ,
									'fields' => 'ids',
									'post_type' => $post_type,
									'posts_per_page' => - 1,
								);
								$fquery = new WP_Query($fargs);
								if (empty($fquery->posts)) {
									wp_delete_attachment($file_id);
								}
							}
						}
					}
				}
			}
			return true;
		}
		public function tinymce_fix($init) {
			global $post;
			$ent_list = get_option($this->option_name . '_ent_list', Array());
			if (!empty($post) && in_array($post->post_type, array_keys($ent_list))) {
				$init['wpautop'] = false;
				$init['indent'] = true;
			}
			return $init;
		}
	}
endif;
return new Employee_Spotlight_Install_Deactivate();