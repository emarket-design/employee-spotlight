<?php $real_post = $post;
$ent_attrs = get_option('employee_spotlight_attr_list');
?>
<div class="widget-thumb">
<a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
<div style="border-color:<?php echo esc_html(emd_global_val('employee-spotlight', 'glb_imgborder_color')); ?>" class="person-img circle"><?php if (get_post_meta($post->ID, 'emd_employee_photo')) {
	$sval = get_post_meta($post->ID, 'emd_employee_photo');
	$thumb = wp_get_attachment_image_src($sval[0], 'thumbnail');
	echo '<img class="emd-img thumb" src="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" alt="' . get_post_meta($sval[0], '_wp_attachment_image_alt', true) . '"/>';
} ?></div>
</a>
</div>
<div class="widget-thumb-text">
<a href="<?php echo get_permalink(); ?>" class="person-name" title="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></a>
</div>