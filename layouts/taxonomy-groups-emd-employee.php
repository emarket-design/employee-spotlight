<?php $real_post = $post;
$ent_attrs = get_option('employee_spotlight_attr_list');
?>
<div id="tax-emd-employee-<?php echo get_the_ID(); ?>" class="emd-container emd-employee-wrap tax-wrap">
<?php $is_editable = 0; ?>
<div class="panel panel-info circle" style="background-color:#FFFFFF">
<div class="panel-heading">
        <div class="panel-title">
            <a class="archive permalink font-bold" href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>"><span class="emd_employee-title"><?php echo get_the_title(); ?></span>
<?php echo apply_filters('emd_get_parent_title', '', $post->ID, 'tax'); ?></a>
        </div>
    </div>
  <div class="panel-body">
    <div class="row">
<?php if (emd_is_item_visible('ent_employee_photo', 'employee_spotlight', 'attribute')) { ?>
      <div class="col-md-4">
        <div class="slcontent">
          <div class="img-responsive">
           <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>"><div style="border-color:<?php echo esc_html(emd_global_val('employee-spotlight', 'glb_imgborder_color')); ?>
" class="person-img"><?php if (get_post_meta($post->ID, 'emd_employee_photo')) {
		$sval = get_post_meta($post->ID, 'emd_employee_photo');
		$thumb = wp_get_attachment_image_src($sval[0], 'thumbnail');
		echo '<img class="emd-img thumb" src="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" alt="' . get_post_meta($sval[0], '_wp_attachment_image_alt', true) . '"/>';
	} ?></div></a>
          </div>
        </div>
      </div>
<?php
} ?>
      <div class="col-md-8">
        <div class="srcontent">
<?php if (emd_is_item_visible('ent_employee_jobtitle', 'employee_spotlight', 'attribute')) { ?>
          <div class="segment-block">
            <div class="row" data-has-attrib="false">
              <div class="col-md-6">
                <span class="segtitle"><?php _e('Job Title', 'employee-spotlight'); ?></span>
              </div>
              <div class="col-md-6">
                <span class="taxlabel"><?php echo esc_html(emd_mb_meta('emd_employee_jobtitle')); ?>
</span>
              </div>
            </div>
          </div>
<?php
} ?>
<?php if (emd_is_item_visible('ent_employee_email', 'employee_spotlight', 'attribute')) { ?>
            <div class="segment-block">
              <div class="row" data-has-attrib="false">
                <div class="col-md-6">
                  <span class="segtitle"><?php _e('Email', 'employee-spotlight'); ?></span>
                </div>
                <div class="col-md-6">
                  <span class="segvalue"><a class="text-undernone" href="mailto:<?php echo esc_html(emd_mb_meta('emd_employee_email')); ?>
"><?php echo esc_html(emd_mb_meta('emd_employee_email')); ?>
</a></span>
                </div>
              </div>
            </div>
<?php
} ?>
<?php if (emd_is_item_visible('tax_office_locations', 'employee_spotlight', 'taxonomy')) { ?>
          <div class="segment-block">
            <div class="row" data-has-attrib="false">
              <div class="col-md-6">
                <span class="segtitle"><?php _e('Location', 'employee-spotlight'); ?></span>
              </div>
              <div class="col-md-6">
                <span class="taxlabel"><?php echo emd_get_tax_vals(get_the_ID() , 'office_locations'); ?></span>
              </div>
            </div>
          </div>
<?php
} ?>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <div class="fcontent">
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
</div>
</div><!--container-end-->