<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
}
$show = apply_filters('emd_get_login_register_option_for_views','none','employee_spotlight');
do_action('emd_show_login_register_forms','employee_spotlight','',$show);
