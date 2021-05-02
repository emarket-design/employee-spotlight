<?php global $employee_circle_grid_count, $employee_circle_grid_filter, $employee_circle_grid_set_list;
$real_post = $post;
$ent_attrs = get_option('employee_spotlight_attr_list');
?>
<article class="col-md-3 col-sm-4 col-xs-6 person">
  <div class="person-thumb in">
 <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
<div style="border-color:<?php echo esc_html(emd_global_val('employee-spotlight', 'glb_imgborder_color', $employee_circle_grid_set_list)); ?>
" class="person-img circle"><?php if (get_post_meta($post->ID, 'emd_employee_photo')) {
	$sval = get_post_meta($post->ID, 'emd_employee_photo');
	$thumb = wp_get_attachment_image_src($sval[0], 'thumbnail');
	echo '<img class="emd-img thumb" src="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" alt="' . get_post_meta($sval[0], '_wp_attachment_image_alt', true) . '"/>';
} ?></div></a>
<div class="person-tag text-center">
<a href="<?php echo get_permalink(); ?>" class="person-name" title="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></a>
<?php if (emd_is_item_visible('ent_employee_jobtitle', 'employee_spotlight', 'attribute')) { ?>
<div class="person-jobtitle"><small><?php echo esc_html(emd_mb_meta('emd_employee_jobtitle')); ?>
</small></div>
<?php
} ?>
</div>
</article>