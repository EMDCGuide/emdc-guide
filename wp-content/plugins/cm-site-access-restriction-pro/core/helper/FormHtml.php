<?php
namespace com\cminds\siteaccessrestriction\helper;

use com\cminds\siteaccessrestriction\App;

class FormHtml {
	
	static function checkboxTree($fieldName, array $current, array $values, $parentId = 0) {
		$output = '';
		if (!empty($values[$parentId])) {
			$output .= '<ul class="'. App::prefix('-form-checkbox-tree') .'">';
			foreach ($values[$parentId] as $id => $label) {
				$output .= sprintf('<li><label><input type="checkbox" name="%s" value="%s"%s /><span>%s</span></label>%s</li>',
					$fieldName,
					esc_attr($id),
					checked(in_array($id, $current), true, false),
					esc_html($label),
					static::checkboxTree($fieldName, $current, $values, $id)
				);
			}
			$output .= '</ul>';
		}
		return $output;
	}
	
	static function selectBox($fieldName, array $options, $currentValue) {
		$content = '';
		foreach ($options as $value => $label) {
			$content .= sprintf('<option value="%s"%s>%s</option>',
				esc_attr($value),
				selected($value, $currentValue, false),
				esc_html($label)
			);
		}
		return sprintf('<select name="%s">%s</select>',
			esc_attr($fieldName),
			$content
		);
	}
	
	static function checkboxList($fieldName, array $options, array $currentValue) {
		$content = '';
		foreach ($options as $value => $label) {
			$content .= sprintf('<div><label><input type="checkbox" name="%s" value="%s"%s> %s</label></div>',
				esc_attr($fieldName),
				esc_attr($value),
				checked(in_array($value, $currentValue), true, false),
				esc_html($label)
			);
		}
		return '<div>'. $content .'</div>';
	}
	
	static function radioWithLabel($fieldName, $value, $label, $currentValue) {
		return sprintf('<label><input type="radio" name="%s" value="%s"%s /> %s</label>',
			esc_attr($fieldName),
			esc_attr($value),
			checked($value, $currentValue, false),
			$label
		);
	}

    static function inputWithLabel($fieldName,$type, $value, $label) {
        $additionalAttr = '';
	    if ($type === 'number')
	        $additionalAttr = ' min="0" step="1"';
        return sprintf('<label>%s<br><input type="%s" name="%s" value="%s" %s/></label>',
            $label,
            $type,
            esc_attr($fieldName),
            esc_attr($value),
            $additionalAttr
        );
    }

    static function userInputWithLabel($label, $field_name){
        return sprintf('<label>%s<input class="cmacc-user-search" data-field="%s" type="text" /><button style="vertical-align:middle; height:30px; margin:5px;" data-field="%s" class="cmacc-user-search-button">Search</button></label>',
            $label,
            $field_name,
            $field_name
        );
    }

}