<?php
/**
 * Plugin Functions
 * @package     EMD
 * @since       5.3
 */
if (!defined('ABSPATH')) exit;
add_action('emd_ext_set_conf', 'empslight_com_set_vcard_list');
add_action('emd_ext_reset_conf', 'empslight_com_reset_vcard_list');
function empslight_com_reset_vcard_list($app) {
	delete_option('empslight_com_vcard_field_list');
}
function empslight_com_set_vcard_list($app) {
	$vcard_list['fname'] = Array(
		'type' => 'blt',
		'key' => 'blt_title'
	);
	$vcard_list['n'] = Array(
		'type' => 'blt',
		'key' => 'blt_title'
	);
	$vcard_list['fn'] = Array(
		'type' => 'blt',
		'key' => 'blt_title'
	);
	$vcard_list['title'] = Array(
		'type' => 'attr',
		'key' => 'employee_jobtitle'
	);
	$vcard_list['adr'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_primary_address'
	);
	$vcard_list['email'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_email'
	);
	$vcard_list['photo'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_photo'
	);
	$vcard_list['wphone'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_phone'
	);
	$vcard_list['mphone'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_mobile'
	);
	$vcard_list['bday'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_birthday'
	);
	$vcard_list['facebook'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_facebook'
	);
	$vcard_list['google'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_google'
	);
	$vcard_list['twitter'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_twitter'
	);
	$vcard_list['linkedin'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_linkedin'
	);
	$vcard_list['github'] = Array(
		'type' => 'attr',
		'key' => 'emd_employee_github'
	);
	$vcard_list['org'] = Array(
		'type' => 'glob',
		'key' => 'glb_vcard_org_name'
	);
	update_option("empslight_com_vcard_field_list", $vcard_list);
}
?>