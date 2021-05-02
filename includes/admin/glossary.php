<?php
/**
 * Settings Glossary Functions
 *
 * @package EMPLOYEE_SPOTLIGHT
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('employee_spotlight_settings_glossary', 'employee_spotlight_settings_glossary');
/**
 * Display glossary information
 * @since WPAS 4.0
 *
 * @return html
 */
function employee_spotlight_settings_glossary() {
	global $title;
?>
<div class="wrap">
<h2><?php echo $title; ?></h2>
<p><?php _e('Employee Spotlight displays photo, bio, and contact information of your employees, founders, team or just yourself. Each employee has its own page.', 'employee-spotlight'); ?></p>
<p><?php _e('The below are the definitions of entities, attributes, and terms included in Employee Spotlight.', 'employee-spotlight'); ?></p>
<div id="glossary" class="accordion-container">
<ul class="outer-border">
<li id="emd_employee" class="control-section accordion-section open">
<h3 class="accordion-section-title hndle" tabindex="1"><?php _e('Employees', 'employee-spotlight'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php _e('Employees are human resources that work for your organization. Employees can be identified as staff, team members, founders, or contractors.', 'employee-spotlight'); ?></p><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php _e('Attributes', 'employee-spotlight'); ?></div></th></tr>
<tr>
<th><?php _e('Full Name', 'employee-spotlight'); ?></th>
<td><?php _e(' Full Name is a required field. Being a unique identifier, it uniquely distinguishes each instance of Employee entity. Full Name is filterable in the admin area. Full Name does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Bio', 'employee-spotlight'); ?></th>
<td><?php _e(' Bio does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Excerpt', 'employee-spotlight'); ?></th>
<td><?php _e(' Excerpt does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Featured', 'employee-spotlight'); ?></th>
<td><?php _e('Sets employee as featured which can be used to select employees in available views using Visual Shortcode Builder and Featured employee widget. Featured does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Photo', 'employee-spotlight'); ?></th>
<td><?php _e('Photo of the employee. 250x250 is the preferred size. Photo does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Job Title', 'employee-spotlight'); ?></th>
<td><?php _e(' Job Title does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Primary Address', 'employee-spotlight'); ?></th>
<td><?php _e(' Primary Address does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Phone', 'employee-spotlight'); ?></th>
<td><?php _e(' Phone is filterable in the admin area. Phone does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Mobile', 'employee-spotlight'); ?></th>
<td><?php _e(' Mobile is filterable in the admin area. Mobile does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Email', 'employee-spotlight'); ?></th>
<td><?php _e(' Email does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Facebook', 'employee-spotlight'); ?></th>
<td><?php _e(' Facebook does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Google+', 'employee-spotlight'); ?></th>
<td><?php _e(' Google+ does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Twitter', 'employee-spotlight'); ?></th>
<td><?php _e(' Twitter does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Linkedin', 'employee-spotlight'); ?></th>
<td><?php _e(' Linkedin does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Github', 'employee-spotlight'); ?></th>
<td><?php _e(' Github is filterable in the admin area. Github does not have a default value. ', 'employee-spotlight'); ?></td>
</tr>
<tr>
<th><?php _e('Birthday', 'employee-spotlight'); ?></th>
<td><?php _e(' Birthday is filterable in the admin area. Birthday does not have a default value. ', 'employee-spotlight'); ?></td>
</tr><tr><th style='font-size:1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php _e('Taxonomies', 'employee-spotlight'); ?></div></th></tr>
<tr>
<th><?php _e('Group', 'employee-spotlight'); ?></th>

<td><?php _e(' Group accepts multiple values like tags', 'employee-spotlight'); ?>. <?php _e('Group does not have a default value', 'employee-spotlight'); ?>.<div class="taxdef-block"><p><?php _e('There are no preset values for <b>Group.</b>', 'employee-spotlight'); ?></p></div></td>
</tr>

<tr>
<th><?php _e('Location', 'employee-spotlight'); ?></th>

<td><?php _e(' Location accepts multiple values like tags', 'employee-spotlight'); ?>. <?php _e('Location does not have a default value', 'employee-spotlight'); ?>.<div class="taxdef-block"><p><?php _e('There are no preset values for <b>Location.</b>', 'employee-spotlight'); ?></p></div></td>
</tr>

<tr>
<th><?php _e('Employee Tag', 'employee-spotlight'); ?></th>

<td><?php _e(' Employee Tag accepts multiple values like tags', 'employee-spotlight'); ?>. <?php _e('Employee Tag does not have a default value', 'employee-spotlight'); ?>.<div class="taxdef-block"><p><?php _e('There are no preset values for <b>Employee Tag.</b>', 'employee-spotlight'); ?></p></div></td>
</tr>
</table>
</div>
</div>
</li>
</ul>
</div>
</div>
<?php
}