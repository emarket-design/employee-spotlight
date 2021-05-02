<?php $real_post = $post;
$ent_attrs = get_option('employee_spotlight_attr_list');
?>
<div id="single-emd-employee-<?php echo get_the_ID(); ?>" class="emd-container emd-employee-wrap single-wrap">
<?php $is_editable = 0; ?>
<div>
<h3><?php echo get_the_title(); ?></h3>
</div>
<div class="mainDetails row">
<?php if (emd_is_item_visible('ent_employee_photo', 'employee_spotlight', 'attribute')) { ?>
  <div class="person-photo col-md-4">
<div style="border-color:<?php echo esc_html(emd_global_val('employee-spotlight', 'glb_imgborder_color')); ?>
" class="person-img circle"><?php if (get_post_meta($post->ID, 'emd_employee_photo')) {
		$sval = get_post_meta($post->ID, 'emd_employee_photo');
		$thumb = wp_get_attachment_image_src($sval[0], 'thumbnail');
		echo '<img class="emd-img thumb" src="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" alt="' . get_post_meta($sval[0], '_wp_attachment_image_alt', true) . '"/>';
	} ?></div>
</div>
<?php
} ?>
  <div class="person-detail col-md-8">
<?php if (emd_is_item_visible('ent_employee_jobtitle', 'employee_spotlight', 'attribute')) { ?>
    <div class="empjobtitle"><strong class="emptitle"><?php echo esc_html(emd_mb_meta('emd_employee_jobtitle')); ?>
</strong></div>
<?php
} ?>
<div class="person-address">
<?php if (emd_is_item_visible('ent_employee_phone', 'employee_spotlight', 'attribute')) { ?>
<div class="person-phone"><i class="fa fa-phone fa-fw"></i><?php echo esc_html(emd_mb_meta('emd_employee_phone')); ?>
</div>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_mobile', 'employee_spotlight', 'attribute')) { ?>
<div class="person-mobile"><i class="fa fa-mobile fa-fw"></i> <?php echo esc_html(emd_mb_meta('emd_employee_mobile')); ?>
</div>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_primary_address', 'employee_spotlight', 'attribute')) { ?>
<div class="person-priaddress"><i class="fa fa-map-marker fa-fw"></i><?php echo esc_html(emd_mb_meta('emd_employee_primary_address')); ?>
</div>
<?php
} ?>
</div>
    <div class="person-link">
<?php if (emd_is_item_visible('ent_employee_email', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon email animate fa fa-envelope fa-fw" href="mailto:<?php echo esc_html(emd_mb_meta('emd_employee_email')); ?>
"></a>
<?php
} ?>
<span class="social">
<?php if (emd_is_item_visible('ent_employee_facebook', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon facebook animate fa fa-facebook fa-fw" href="<?php echo esc_html(emd_mb_meta('emd_employee_facebook')); ?>
"></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_twitter', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon twitter animate fa fa-twitter fa-fw" href="<?php echo esc_html(emd_mb_meta('emd_employee_twitter')); ?>
"></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_github', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon github animate fa fa-github-alt fa-fw" href="<?php echo esc_html(emd_mb_meta('emd_employee_github')); ?>
"></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_google', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon google-plus animate fa fa-google-plus fa-fw" href="<?php echo esc_html(emd_mb_meta('emd_employee_google')); ?>
"></a>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_linkedin', 'employee_spotlight', 'attribute')) { ?>
<a class="social-icon linkedin animate fa fa-linkedin fa-fw" href="<?php echo esc_html(emd_mb_meta('emd_employee_linkedin')); ?>
"></a>
<?php
} ?>
<?php do_action('emd_vcard', 'empslight_com', 'emd_employee', $post->ID); ?>
</span>
    </div>
  </div>
  </div>
<div class="emp-content">
  <?php echo $post->post_content; ?>
</div>
<div class="tax-block" style="padding:40px 0">
    <div class="fcontent">
<?php if (emd_is_item_visible('tax_office_locations', 'employee_spotlight', 'taxonomy')) { ?>
     <span class="footer-object-title badge alert-info"><?php _e('Location', 'employee-spotlight'); ?></span><span class="footer-object-value"><?php echo emd_get_tax_vals(get_the_ID() , 'office_locations'); ?></span>
<?php
} ?>
<?php if (emd_is_item_visible('tax_groups', 'employee_spotlight', 'taxonomy')) { ?>
      <span class="footer-object-title badge alert-info"><?php _e('Groups', 'employee-spotlight'); ?></span><span class="footer-object-value"><?php echo emd_get_tax_vals(get_the_ID() , 'groups'); ?></span>
<?php
} ?>
<?php if (emd_is_item_visible('tax_employee_tags', 'employee_spotlight', 'taxonomy')) { ?>
      <span class="footer-object-title badge alert-info"><?php _e('Tags', 'employee-spotlight'); ?></span><span class="footer-object-value"><?php echo emd_get_tax_vals(get_the_ID() , 'employee_tags'); ?></span>
<?php
} ?>
    </div>
</div>
</div><!--container-end-->