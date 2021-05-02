<?php global $employee_circle_panel_grid_count, $employee_circle_panel_grid_filter, $employee_circle_panel_grid_set_list;
$real_post = $post;
$ent_attrs = get_option('employee_spotlight_attr_list');
?>
<article style="height:350px;margin-bottom:5px" class="col-md-3 col-sm-4 col-xs-6 person">
  <div class="person-thumb in">
     <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
<div style="border-color:<?php echo esc_html(emd_global_val('employee-spotlight', 'glb_imgborder_color', $employee_circle_panel_grid_set_list)); ?>
" class="person-img circle"><?php if (get_post_meta($post->ID, 'emd_employee_photo')) {
	$sval = get_post_meta($post->ID, 'emd_employee_photo');
	$thumb = wp_get_attachment_image_src($sval[0], 'thumbnail');
	echo '<img class="emd-img thumb" src="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" alt="' . get_post_meta($sval[0], '_wp_attachment_image_alt', true) . '"/>';
} ?></div>
</a>
<div class="person-tag text-center">
<a href="<?php echo get_permalink(); ?>" class="person-name"><?php echo get_the_title(); ?></a>
<?php if (emd_is_item_visible('ent_employee_jobtitle', 'employee_spotlight', 'attribute')) { ?>
<div class="person-jobtitle"><small><?php echo esc_html(emd_mb_meta('emd_employee_jobtitle')); ?>
</small></div>
</div>
<?php
} ?>
  <div class="person-link">
<?php if (emd_is_item_visible('ent_employee_email', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon email animate" href="mailto:<?php echo esc_html(emd_mb_meta('emd_employee_email')); ?>
"><i class="fa fa-envelope fa-fw"></i></a>
<?php
} ?>
<span class="social">
<?php if (emd_is_item_visible('ent_employee_facebook', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon facebook animate" href="<?php echo esc_html(emd_mb_meta('emd_employee_facebook')); ?>
"><i class="fa fa-facebook fa-fw"></i></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_twitter', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon twitter animate" href="<?php echo esc_html(emd_mb_meta('emd_employee_twitter')); ?>
"><i class="fa fa-twitter fa-fw"></i></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_github', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon github animate" href="<?php echo esc_html(emd_mb_meta('emd_employee_github')); ?>
"><i class="fa fa-github-alt fa-fw"></i></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_google', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon google-plus animate" href="<?php echo esc_html(emd_mb_meta('emd_employee_google')); ?>
"><i class="fa fa-google-plus fa-fw"></i></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_linkedin', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon linkedin animate" href="<?php echo esc_html(emd_mb_meta('emd_employee_linkedin')); ?>
"><i class="fa fa-linkedin fa-fw"></i></a>
<?php
} ?>
</span>
<a class="person-page animate" title="<?php _e('Go to the personal page of', 'employee-spotlight'); ?> <?php echo get_the_title(); ?>" href="<?php echo get_permalink(); ?>"><i class="fa fa-user fa-fw text-danger"></i></a>
  </div>
<div id="empexcerpt" class="panel-body"><?php echo $post->post_excerpt; ?></div>
</article>