<?php
namespace com\cminds\registration\helper;

use com\cminds\registration\model\Settings;

class SettingsListKeyValue {
	
	static function render($name, $option) {
		$value = Settings::getOption($name);
		$output = self::renderOption(0, 'template', $option, '', '', '', '');
		if(!empty($value) && is_array($value)) {
			foreach ($value as $i => $opt) {
				$fieldName = $name . '['. ($i+1) .']';
				$output .= self::renderOption($i+1, $fieldName, $option, $opt['key'], $opt['value'], $opt['color'], $opt['textcolor']);
			}
		}
		$output .= '<input type="button" value="Add" class="cmreg-list-key-value-add-btn" />';
		return '<div class="cmreg-list-key-value" data-name="'. esc_attr($name) .'">' . $output . '</div>';
	}
	
	protected static function renderOption($num, $name, $option, $key, $val, $color, $textcolor) {

		$products_html = '';
		$products = $option['products'];
		$products_html .= '<select name="'.esc_attr($name).'[key]">';
		if(count($products) > 0) {
			foreach($products as $product_key=>$product_val) {
				if($product_key == $key) {
					$products_html .= '<option value="'.$product_key.'" selected="selected">'.$product_val.'</option>';
				} else {
					$products_html .= '<option value="'.$product_key.'">'.$product_val.'</option>';
				}
			}
		}
		$products_html .= '</select>';
		
		$wp_roles_html = '';
		$wp_roles = $option['wp_roles'];
		$wp_roles_html .= '<select name="'.esc_attr($name).'[value]">';
		if(count($wp_roles) > 0) {
			foreach($wp_roles as $wp_role_key=>$wp_role_val) {
				if($wp_role_key == $val) {
					$wp_roles_html .= '<option value="'.$wp_role_key.'" selected="selected">'.$wp_role_val.'</option>';
				} else {
					$wp_roles_html .= '<option value="'.$wp_role_key.'">'.$wp_role_val.'</option>';
				}
			}
		}
		$wp_roles_html .= '</select>';
		
		$html = '';
		$html .= '<div class="cmreg-list-key-value-row" data-num="%d">';
		$html .= '<div><label>Product</label>%s</div>';
		$html .= '<div><label>User Role</label>%s</div>';
		$html .= '<div><label>Box BG Color</label><input type="color" name="'.esc_attr($name).'[color]" value="'.$color.'" /></div>';
		$html .= '<div><label>Box Text Color</label><input type="color" name="'.esc_attr($name).'[textcolor]" value="'.$textcolor.'" /></div>';
		$html .= '<div><input type="button" value="Remove" title="Remove" style="float:right;" /></div>';
		$html .= '</div>';

		return sprintf($html,
			$num,
			$products_html,
			$wp_roles_html
		);

		/*
		return sprintf('<div class="cmreg-list-key-value-row" data-num="%d"><input type="text" name="%s[key]" value="%s" placeholder="%s" />'
				. '<input type="text" name="%s[value]" value="%s" placeholder="%s" /><input type="button" value="x" title="Remove" /></div>',
			$num,
			esc_attr($name),
			esc_attr($key),
			esc_attr($option['keyPlaceholder']),
			esc_attr($name),
			esc_attr($val),
			esc_attr($option['valuePlaceholder'])
		);
		*/
	}
	
}