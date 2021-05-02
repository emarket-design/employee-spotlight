<?php
/**
 * Settings Functions Advanced
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 5.0
 */
if (!defined('ABSPATH')) exit;

add_action('emd_ext_register','emd_global_register_settings');
add_filter('emd_add_settings_tab','emd_glob_settings_tab',10,2);
add_action('emd_show_settings_tab','emd_show_glob_settings_tab',10,2);

function emd_glob_settings_tab($tabs,$app){
	$init_variables = get_option($app . '_glob_init_list',Array());
	$init_variables = apply_filters('emd_ext_glob_var_init', $init_variables);
	if(!empty($init_variables)){
		$tabs['global'] = __('Global', 'emd_plugins');
		echo '<p>' . settings_errors($app . '_glob_list') . '</p>';
	}
	return $tabs;
}
function emd_show_glob_settings_tab($app,$active_tab){
	$variables = get_option($app . '_glob_list',Array());
	$init_variables = get_option($app . '_glob_init_list',Array());
	$init_variables = apply_filters('emd_ext_glob_var_init', $init_variables);
	if(!empty($init_variables)){
		emd_global_view_tab($app,$active_tab,$init_variables,$variables);
	}
}

function emd_global_register_settings($app){
	register_setting($app . '_glob_list', $app . '_glob_list', 'emd_global_sanitize');
	$variables = get_option($app . '_glob_list');
	if(!empty($variables)){
		foreach($variables as $id => $myvar){
			$args['key'] = $id;
			$args['val'] = "";
			add_settings_field($app . '_glob_list[' . $id . ']', $myvar['label'], 'emd_global_' . $myvar['type'] . '_callback',$app . '_settings','',$args);
		}
	}
}
function emd_global_sanitize($input){
	$variables = get_option($input['app'] . '_glob_init_list');
	$variables = apply_filters('emd_ext_glob_var_init', $variables);
	if(!empty($variables)){
		foreach($variables as $kv => $val){
			if(isset($input[$kv])){
				$variables[$kv]['val'] = $input[$kv];
			}
			elseif($val['type'] == 'checkbox') {
				$variables[$kv]['val'] = 0;
			}
			if($val['required'] == 1 && empty($input[$kv])){
				$error_message = sprintf(__( "%s is required.", 'emd-plugins'),$val['label']);
				add_settings_error($input['app'] . '_glob_list','required-' . $kv,$error_message,'error');
			}
			if($val['type'] == 'map'){
				$variables[$kv]['map'] = $input[$kv.'_map'];
				$variables[$kv]['width'] = $input[$kv.'_width'];
				$variables[$kv]['height'] = $input[$kv.'_height'];
				$variables[$kv]['zoom'] = $input[$kv.'_zoom'];
				$variables[$kv]['map_type'] = $input[$kv.'_map_type'];
				$variables[$kv]['marker'] = $input[$kv.'_marker'];
				$variables[$kv]['load_info'] = $input[$kv.'_load_info'];
				$variables[$kv]['marker_title'] = $input[$kv.'_marker_title'];
				$variables[$kv]['info_window'] = $input[$kv.'_info_window'];
				if(empty($input[$kv])){
					$variables[$kv]['map'] = "";
				}
			}
		}
	}
	return $variables;
}
function emd_global_text_callback($args){
	$html = '<input type="text" class="' . $size . '-text" id="' . esc_attr($args['key']) . '" name="' . esc_attr($args['key']) . '" value="' . esc_attr(stripslashes($args['val'])) . '"/>';
	echo $html;
}
function emd_global_view_tab($app,$active_tab,$init_variables,$variables){
?>
	<div class='tab-content' id='tab-global' <?php if ( 'global' != $active_tab ) { echo 'style="display:none;"'; } ?>>
<?php	echo '<form method="post" action="options.php">';
	settings_fields($app .'_glob_list'); ?>
	<?php if(!empty($init_variables)){
	echo '<input type="hidden" name="' . esc_attr($app) . '_glob_list[app]" id="' . esc_attr($app) . '_glob_list_app" value="' . $app . '">';
	echo '<table class="form-table">
		<tbody>';
	foreach($init_variables as $id => $myvar){
		if(!empty($variables)){
			if(!empty($variables[$id]['val'])){
				$myvar['val'] = $variables[$id]['val'];
			}
			if(!empty($variables[$id]['values'])){
				$myvar['values'] = $variables[$id]['values'];
			}       
		}
		echo '<tr>
			<th scope="row">
			<label for="' . $id . '">';
		echo $myvar['label']; 
		if($myvar['required'] == 1){
			echo '<span class="dashicons dashicons-star-filled" style="font-size:10px;color:red;"></span>';
		}
		echo '</label>
			</th>
			<td>';
		$val = "";
		if(isset($myvar['val'])){
			$val = $myvar['val'];
			if($myvar['type'] == 'checkbox' && $val == 1){
				$val = 'checked';
			}
		}
		elseif(!empty($myvar['dflt'])){
			if(($myvar['type'] == 'checkbox_list' || $myvar['type'] == 'multi_select') && !is_array($myvar['dflt'])){
				$dflt = $myvar['dflt'];
				$val= Array("$dflt");
			}
			else {
				$val = $myvar['dflt'];
			}
		}
		switch($myvar['type']){
			case 'text':
				echo "<input class='regular-text' id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='text' value='" . $val ."'></input>";
				break;
			case 'map':
				$myvar = $variables[$id];
				$width = isset($myvar['width']) ? $myvar['width'] : '';
				$height = isset($myvar['height']) ? $myvar['height'] : '';
				$zoom = isset($myvar['zoom']) ? $myvar['zoom'] : '14';
				$map_coord = isset($myvar['map']) ? $myvar['map'] : '';
				$marker = isset($myvar['marker']) ? 'checked' : '';
				$load_info = isset($myvar['load_info']) ? 'checked' : '';
				$map_type = isset($myvar['map_type']) ? $myvar['map_type'] : '';
				$marker_title = isset($myvar['marker_title']) ? $myvar['marker_title'] : '';
				$info_window = isset($myvar['info_window']) ? $myvar['info_window'] : '';
				echo "<input id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='text' size='50' value='" . $val ."'></input>";
				 if(!empty($myvar['desc'])){
                        		echo "<p class='description'>" . $myvar['desc'] . "</p>";
                		}
				echo "<tr><th scope='row'></th><td><table><th scope='row'><label>" . __('Frontend Map Settings','emd-plugins') . "</th><td></td></tr>
				<th scope='row'><label for='" . $id . "_width'>" . __('Width','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_width' name='" . esc_attr($app) . "_glob_list[" . $id . "_width]' type='text' value='" . $width . "'></input><p class='description'>" . __('Sets the map width.You can use \'%\' or \'px\'. Default is 100%.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_height'>" . __('Height','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_height' name='" . esc_attr($app) . "_glob_list[" . $id . "_height]' type='text' value='" . $height ."'></input><p class='description'>" . __('Sets the map height. You can use \'px\'. Default is 480px.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_zoom'>" . __('Zoom','emd-plugins') . "</th><td><select id='" . esc_attr($app) . "_glob_list_" . $id . "_zoom' name='" . esc_attr($app) . "_glob_list[" . $id . "_zoom]'>";
				for($i=20;$i >=1;$i--){
					echo "<option value='" . $i . "'";
					if($zoom == $i){
						echo " selected";
					}
					echo ">" . $i . "</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_map_type'>" . __('Type','emd-plugins') . "</th><td><select id='" . esc_attr($app) . "_glob_list_" . $id . "_map_type' name='" . esc_attr($app) . "_glob_list[" . $id . "_map_type]'>";
				$map_types = Array("ROADMAP","SATELLITE","HYBRID","TERRAIN");
				foreach($map_types as $mtype){
					echo "<option value='" . $mtype . "'";
					if($map_type == $mtype){
						echo " selected";
					}
					echo ">" . $mtype . "</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_marker'>" . __('Marker','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_marker' name='" . esc_attr($app) . "_glob_list[" . $id . "_marker]' type='checkbox' value=1 $marker></input></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_marker_title'>" . __('Marker Title','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_marker_title' name='" . esc_attr($app) . "_glob_list[" . $id . "_marker_title]' type='text' value='" . $marker_title ."'></input><p class='description'>" . __('Sets the marker title when hover.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_info_window'>" . __('Info Window','emd-plugins') . "</th><td><textarea id='" . esc_attr($app) . "_glob_list_" . $id . "_info_window' name='" . esc_attr($app) . "_glob_list[" . $id . "_info_window]'>" . $info_window . "</textarea><p class='description'>" . __('Sets the content of the info box. You can use html tags.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_load_info'>" . __('Display Info Window on Page Load','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_load_info' name='" . esc_attr($app) . "_glob_list[" . $id . "_load_info]' type='checkbox' value=1 $load_info></input></td></tr>";
				echo "<tr><th><p class='description'>" . __('You can drag and drop the marker to specify the exact location.','emd-plugins') . "</th><td><div class='emd-mb-map-field'><div class='emd-mb-map-canvas' data-default-loc=''></div>
					<input type='hidden' name='" . esc_attr($app) . "_glob_list[" . $id . "_map]' class='emd-mb-map-coordinate' value='" . $map_coord ."'>";
                                echo "<button style='display:none;' class='button emd-mb-map-goto-address-button' value='".  esc_attr($app) . "_glob_list_" . $id . "'>Find Address</button>";
				echo "</div></td></tr></table></td></tr>";
				break;
			case 'textarea':
				echo "<textarea id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $val ."</textarea>";
				break;
			case 'wysiwyg':
				echo wp_editor($val, esc_attr($app) . "_glob_list_" . $id, array(
							'tinymce' => false,
							'textarea_rows' => 10,
							'media_buttons' => true,
							'textarea_name' => esc_attr($app) . "_glob_list[" . $id . "]",
							'quicktags' => Array(
								'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,spell'
								)
							));
				break;
			case 'checkbox':
				echo "<input id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='checkbox' value='1'";
				if($val === 'checked'){
					echo " checked";
				}
				echo "></input>";
				break;
			case 'checkbox_list':
				if (!empty($myvar['values'])) {
					foreach($myvar['values'] as $kvalue => $mvalue){
						if (in_array($kvalue,$val)) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
						echo "<input name='" . esc_attr($app) . "_glob_list[" . $id . "][] id='" . esc_attr($app) . "_glob_list[" . $id . "]" . "' type='checkbox' value='" . $kvalue . "' " . $checked . "/>&nbsp;";
						echo "<label for='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $mvalue . "</label><br/>";
					}
				}
				break;
			case 'radio':
				if (!empty($myvar['values'])) {
					foreach($myvar['values'] as $kvalue => $mvalue){
						if ($val == $kvalue) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
						echo "<input name='" . esc_attr($app) . "_glob_list[" . $id . "] id='" . esc_attr($app) . "_glob_list_" . $id .  "' type='radio' value='" . $kvalue . "' " . $checked . "/>&nbsp;";
						echo "<label for='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $mvalue . "</label><br/>";
					}
				}
				break;
			case 'select':
				echo "<select id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]'>";
				foreach($myvar['values'] as $kvalue => $mvalue){
					if($val == $kvalue){
						$selected = "selected";
					}
					else {
						$selected = "";
					}
					echo "<option value='" . $kvalue . "' " . $selected . "/>";
					echo  $mvalue . "</option>";
				}
				echo "</select>";
				break;
			case 'multi_select':
				echo "<select id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "][]' multiple>";
				foreach($myvar['values'] as $kvalue => $mvalue){
					if(in_array($kvalue,$val)){
						$selected = "selected";
					}
					else {
						$selected = "";
					}
					echo "<option value='" . $kvalue . "' " . $selected . "/>";
					echo  $mvalue . "</option>";
				}
				echo "</select>";
				break;
		}
		if(!empty($myvar['desc'])){
			echo "<p class='description'>" . $myvar['desc'] . "</p>";
		}
		
		echo '</td>
			</tr>';
	}
	echo '</tbody></table>';
}
?>
	</tbody>
	</table>
<?php
	submit_button(); 
	echo '</form></div>';
}
if(!function_exists('emd_get_global_map')){
	function emd_get_global_map($app,$key){
		$glob_list = get_option(str_replace("-","_",$app) . '_glob_list');
		$inp_args = Array();
		$width = '100%'; // Map width, default is 640px. You can use '%' or 'px'
		$height = '480px'; // Map height, default is 480px. You can use '%' or 'px'
		if(!empty($glob_list[$key]['map'])){
			$value_map = $glob_list[$key]['map'];
			$map_arr = explode(",",$value_map);
			$latitude = $map_arr[0];
			$longitude = $map_arr[1];
			$marker = ($glob_list[$key]['marker']) ? true : false;
			$load_info = ($glob_list[$key]['load_info']) ? true : false;
			$zoom = (int) $glob_list[$key]['zoom'];
			if(!empty($glob_list[$key]['width'])){
				$width = $glob_list[$key]['width'];
			}
			if(!empty($glob_list[$key]['height'])){
				$height = $glob_list[$key]['height'];
			}
			$inp_args = array(
				'latitude'     => $latitude,
				'longitude'    => $longitude,
				'zoom'         => $zoom,  // Map zoom, default is the value set in admin, and if it's omitted - 14
				'width'        => $width,
				'height'       => $height,
				// Map type, see https://developers.google.com/maps/documentation/javascript/reference#MapTypeId
				'mapTypeId'    => $glob_list[$key]['map_type'],
				'marker'       => $marker, // Display marker? Default is 'true',
				'load_info'    => $load_info,
				'marker_title' => $glob_list[$key]['marker_title'], // Marker title when hover
				'info_window'  => $glob_list[$key]['info_window'], // Info window content, can be anything. HTML allowed.
			);
		}
		$args = wp_parse_args( $inp_args, array(
				'latitude'     => '25.7616798',
				'longitude'    => '-80.19179020000001',
				'zoom'         => 14,
				'mapTypeId'    => 'ROADMAP',
				'marker'       => false,
				'load_info'    => false,
				'width'        => $width,
				'height'       => $height,
				'marker_title' => '',
				'info_window'  => '',
				'js_options'   => array(),
			) );
		$args['js_options'] = wp_parse_args( $args['js_options'], array(
					'zoom'      => $args['zoom'],
					'marker_title' => $args['marker_title'],
					'mapTypeId' => $args['mapTypeId'],
				) );
		$js_options =  esc_attr( wp_json_encode( $args ) );
		return ' <style type="text/css" media="screen">
			/*<![CDATA[*/
			.gm-style img{ 
			max-width:none !important; 
			/*]]>*/} 
			</style>
			<div class="emd-mb-map-canvas" data-map_options="' . $js_options . '" style="width:' . $args['width'] . ';height:' . $args['height'] . ';"></div>';
	}
}
