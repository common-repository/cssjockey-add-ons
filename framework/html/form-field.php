<?php
global $wp_roles, $wp_registered_sidebars, $wpdb;
$field_html = [];
$text_fields = $this->inputTextFieldTypes();
$select_fields = $this->selectFields();
$option_name = $option['id'];

$cjwpbldr_helpers = cjwpbldr_helpers::getInstance();

if (!isset($option['options'])) {
	$option['options'] = [];
}
$form_field_id = '';
if (isset($option_name)) {
	$form_field_id = $this->cleanString($option_name);
	if (strstr($option_name, '~')) {
		$option_name = explode('~', $option_name)[1];
	}
}

if (gettype($option['default']) == 'object') {
	$option['default'] = (array) $option['default'];
}

$option_value = (isset($option['default']) && $option['default'] != '') ? (is_string($option['default'])) ? stripslashes($option['default']) : $option['default'] : '';
if (isset($option['aos'])) {
	$option_name .= '[value]';
	$option_value = (isset($option['default']['value']) && $option['default']['value'] != '') ? @stripslashes($option['default']['value']) : '';
}

// params
if (!isset($option['params'])) {
	$option['params'] = [];
}
if (isset($option['params']) && !is_array($option['params'])) {
	$option['params'] = [];
}
if (!isset($option['params']['class'])) {
	$option['params']['class'] = '';
}
if (isset($option['params']['class']) && is_array($option['params']['class'])) {
	$option['params']['class'] = implode(' ', $option['params']['class']);
}

if (in_array($option['type'], $text_fields)) {
	$option['params']['class'] = (isset($option['params']['class']) && $option['params']['class'] !== '') ? $option['params']['class'] . ' cj-input' : 'cj-input';
}
if (in_array($option['type'], $select_fields)) {
	$option['params']['class'] = (isset($option['params']['class']) && $option['params']['class'] !== '') ? $option['params']['class'] . ' selectize' : 'selectize';
}
if ($option['type'] == 'textarea') {
	$option['params']['class'] = $option['params']['class'] . ' cj-textarea';
}

$params = '';
if (is_array($option['params']) && !empty($option['params'])) {
	foreach ($option['params'] as $attribute => $attr_value) {
		if ($attr_value !== '') {
			$class_value = esc_textarea(strip_tags($attr_value));
			$class_value = str_replace(' ', '-space-', $class_value);
			$class_value = str_replace(['space-', '-space', '-space-'], ' ', $class_value);
			$class_value = str_replace(['- ', ' -'], ' ', $class_value);
			$params .= ' ' . $attribute . '="' . trim($class_value) . '"';
		}
	}
}

if (in_array($option['type'], $text_fields)) {
	$option_value = (!is_array($option_value)) ? esc_html($option_value) : $option_value;
}

if ($option['type'] == 'admin-heading') {
	$heading_text = ($option_value != '') ? $option_value : $option['label'];
	$field_html[] = '<div class="cj-main-heading">';
	if (isset($option['search_form']) && $option['search_form'] == 1) {
		$field_html[] = '<div class="cj-is-pulled-right cj-field" style="margin-bottom: 0 !important; margin-top: -4px;"><div class="cj-control cj-mb-0 cj-has-icon cj-has-icon-right inline-block"><input type="search" class="cjwpbldr-quick-search cj-input" style="padding: 2px 15px; width: 200px;" placeholder="' . __('Search..', 'wp-builder-locale') . '"></div></div>';
	}

	$field_html[] = '<h2>' . html_entity_decode($heading_text) . '</h2>';
	$field_html[] = '<div class="cj-help">' . $option['info'] . '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'admin-info-full') {
	$heading_text = ($option_value != '') ? $option_value : $option['label'];
	$field_html[] = '<div class="cj-field cj-p-15">';
	if (isset($option['label']) && $option['label'] != '') {
		$field_html[] = '<span class="cj-label">' . $option['label'] . '</span>';
	}
	$field_html[] = '<div class="cj-help">' . html_entity_decode($option_value) . '</div>';
	if ($option['info'] != '') {
		$field_html[] = '<div class="cj-help">' . html_entity_decode($option['info']) . '</div>';
	}
	$field_html[] = '</div>';
}
if ($option['type'] == 'heading') {
	$field_html[] = '<h2>' . $option['label'] . '</h2>';
}
if ($option['type'] == 'sub-heading') {
	$field_html[] = '<h2>' . $option['label'] . '</h2>';
}
if ($option['type'] == 'info') {
	$field_html[] = '<div class="cj-info">' . html_entity_decode($option_value) . '</div>';
}
if ($option['type'] == 'html') {
	$field_html[] = html_entity_decode(html_entity_decode($option_value));
}

if ($option['type'] == 'text') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'text-readonly') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' readonly />';
}
if ($option['type'] == 'text-disabled') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' disabled />';
}
if ($option['type'] == 'number') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="number" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'email') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="email" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'url') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="url" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'password') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="password" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'date') {
	$field_html[] = '<input data-input-type="date" id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'date-time' || $option['type'] == 'date_time') {
	$field_html[] = '<input data-input-type="date-time" id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'time') {
	$field_html[] = '<input data-input-type="time" id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'date-range') {
	$from = (isset($option_value['from'])) ? $option_value['from'] : '';
	$to = (isset($option_value['to'])) ? $option_value['to'] : '';
	$field_html[] = '<div class="cj-columns cj-is-multiline cj-date-range" style="border-top: 0;">';
	$field_html[] = '<div class="cj-column cj-is-6"><input data-input-type="date-range-from" id="' . $form_field_id . '-from" name="' . $option_name . '[from]" type="text" value="' . $from . '" class="cj-input" placeholder="' . __('Select Start Date', 'wp-builder-locale') . '" /></div>';
	$field_html[] = '<div class="cj-column cj-is-6"><input data-input-type="date-range-to" id="' . $form_field_id . '-to" name="' . $option_name . '[to]" type="text" value="' . $to . '" class="cj-input" placeholder="' . __('Select End Date', 'wp-builder-locale') . '" /></div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'time-range') {
	$from = (isset($option_value['from'])) ? $option_value['from'] : '';
	$to = (isset($option_value['to'])) ? $option_value['to'] : '';
	$field_html[] = '<div class="cj-columns cj-is-multiline cj-date-range" style="border-top: 0;">';
	$field_html[] = '<div class="cj-column cj-is-4 cj-pb-0"><input data-input-type="time" id="' . $form_field_id . '-from" name="' . $option_name . '[from]" type="text" value="' . $from . '" class="cj-input" placeholder="' . __('Select Start Time', 'wp-builder-locale') . '" /></div>';
	$field_html[] = '<div class="cj-column cj-is-4 cj-pb-0"><input data-input-type="time" id="' . $form_field_id . '-to" name="' . $option_name . '[to]" type="text" value="' . $to . '" class="cj-input" placeholder="' . __('Select End Time', 'wp-builder-locale') . '" /></div>';
	$field_html[] = '<div class="cj-column cj-is-4 cj-pb-0">';
	$intervals = [
		'15' => __('15 minutes', 'wp-builder-locale'),
		'30' => __('30 minutes', 'wp-builder-locale'),
		'60' => __('1 hour', 'wp-builder-locale'),
		'120' => __('2 hours', 'wp-builder-locale'),
	];
	foreach ($intervals as $opt => $val) {
		if ($option_value['interval'] != '' && $opt == $option_value['interval']) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$field_html[] = '<select class="selectize" id="' . $form_field_id . '-interval" name="' . $option_name . '[interval]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
	$field_html[] = '</div>';

	/*$field_html[] = '<div class="cj-column cj-is-12 cj-pb-0">';
	$time_slots = $this->createTimeRange( '00:00', '24:00', '15 mins', '12', false );
	$tile_slot_opts = '';
	foreach( $time_slots as $opt => $val ) {
		if( array_key_exists( 'exclude', $option_value ) ) {
			if( in_array( $opt, $option_value['exclude'] ) ) {
				$tile_slot_opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$tile_slot_opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
	}
	$field_html[] = '<select class="selectize" multiple id="' . $form_field_id . '-interval" name="' . $option_name . '[exclude][]" data-tags="true" ' . $params . '>';
	$field_html[] = $tile_slot_opts;
	$field_html[] = '</select>';
	$field_html[] = '</div>';*/

	$field_html[] = '</div>';
}
if ($option['type'] == 'weekdays') {
	$weekdays = [
		'Sunday' => 'Sunday',
		'Monday' => 'Monday',
		'Tuesday' => 'Tuesday',
		'Wednesday' => 'Wednesday',
		'Thursday' => 'Thursday',
		'Friday' => 'Friday',
		'Saturday' => 'Saturday',
	];
	$opts = '<option value="">None</option>';
	if (!empty($weekdays)) {
		foreach ($weekdays as $opt => $val) {
			if (is_array($opt) && in_array($opt, $option_value)) {
				$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
	}
	$field_html[] = '<select class="selectize" multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'color') {
	$default_value = ($option_value != '') ? $option_value : __('inherit', 'wp-builder-locale');
	// $field_html[] = '<input data-input-type="color" id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" ' . $params . ' /><span style="position: relative; top: 0px; height:30px;" class="cj-button cj-hex-color-container cj-br-0"><span class="cj-hex-color" data-clipboard-text="' . $default_value . '" data-clipboard-confirmation="' . __( 'Copied', 'wp-builder-locale' ) . '">' . $default_value . '</span></span>';
	$field_html[] = '<input autocomplete="off" data-input-type="cj-color-picker" data-default="' . $option_value . '" id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" class="cj-input" />';
	$field_html[] = '<span class="cj-color-hex" style="background-color: ' . $option_value . '"></span>';
}
if ($option['type'] == 'hidden') {
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="hidden" value="' . $option_value . '" ' . $params . ' />';
}
if ($option['type'] == 'textarea') {
	$field_html[] = '<textarea spellcheck="false" autocapitalize="off" id="' . $form_field_id . '" name="' . $option_name . '" ' . $params . '>' . $option_value . '</textarea>';
}
if ($option['type'] == 'select' && isset($option['options'])) {
	$option['options'] = (isset($option['select-options-from-raw'])) ? $option['select-options-from-raw'] : $option['options'];
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$opts = '<option value="">' . __('None', 'wp-builder-locale') . '</option>';
		if (!empty($option['options'])) {
			foreach ($option['options'] as $opt => $val) {
				if ($option_value != '' && $opt == $option_value) {
					$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
				} else {
					$opts .= '<option value="' . $opt . '">' . $val . '</option>';
				}
			}
		}
		$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
		$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" style="width: 100%;" ' . $params . '>';
		$field_html[] = $opts;
		$field_html[] = '</select>';
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified, please review field settings.', 'wp-builder-locale'), '', false);
		}
	}
}

if ($option['type'] == 'font-family') {
	$default_fonts['Arial'] = 'Arial';
	$default_fonts['Helvetica'] = 'Helvetica';
	$default_fonts['Times New Roman'] = 'Times New Roman';
	$default_fonts['Times'] = 'Times';
	$default_fonts['Courier New'] = 'Courier New';
	$default_fonts['Courier'] = 'Courier';
	$default_fonts['Verdana'] = 'Verdana';
	$default_fonts['Georgia'] = 'Georgia';
	$default_fonts['Palatino'] = 'Palatino';
	$default_fonts['Garamond'] = 'Garamond';
	$default_fonts['Bookman'] = 'Bookman';
	$default_fonts['Comic Sans MS'] = 'Comic Sans MS';
	$default_fonts['Trebuchet MS'] = 'Trebuchet MS';
	$default_fonts['Arial Black'] = 'Arial Black';
	$default_fonts['Impact'] = 'Impact';

	$google_fonts = $this->getGoogleFonts();

	if (is_array($default_fonts) && is_array($google_fonts)) {
		$option['options'] = array_merge($default_fonts, $google_fonts);
	}

	$opts = '<option value="inherit">inherit</option>';
	foreach ($option['options'] as $opt => $val) {
		if ($option_value != '' && $opt == $option_value) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" class="selectize">';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'select-raw' && isset($option['options'])) {
	if (isset($option['options']) && is_array($option['options'])) {
		$opts = '';
		$opts = '<option value="">None</option>';
		foreach ($option['options'] as $opt => $val) {
			if ($option_value != '' && $opt == $option_value) {
				$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
		$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
		$field_html[] = '<select class="cj-input" id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" ' . $params . '>';
		$field_html[] = $opts;
		$field_html[] = '</select>';
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified, please review field settings.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'dropdown' || $option['type'] == 'form-options-dropdown' || $option['type'] == 'form_options_dropdown') {
	$option['options'] = (isset($option['select-options-from-raw'])) ? $option['select-options-from-raw'] : $option['options'];
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$opts = '<option value="">None</option>';
		if (!empty($option['options'])) {
			foreach ($option['options'] as $opt => $val) {
				if ($option_value != '' && $opt == $option_value) {
					$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
				} else {
					$opts .= '<option value="' . $opt . '">' . $val . '</option>';
				}
			}
		}
		$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
		$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" ' . $params . '>';
		$field_html[] = $opts;
		$field_html[] = '</select>';
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified, please review field settings.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'multi-dropdown' || $option['type'] == 'multi_dropdown') {
	$option['options'] = (isset($option['select-options-from-raw'])) ? $option['select-options-from-raw'] : $option['options'];
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$opts = '<option value="">None</option>';
		if (!empty($option['options'])) {
			foreach ($option['options'] as $opt => $val) {
				if (is_array($option_value) && $option_value != '' && in_array($opt, $option_value)) {
					$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
				} else {
					$opts .= '<option value="' . $opt . '">' . $val . '</option>';
				}
			}
		}
		$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
		$field_html[] = '<select class="selectize" multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" ' . $params . '>';
		$field_html[] = $opts;
		$field_html[] = '</select>';
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified, please review field settings.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'checkbox' || $option['type'] == 'checkbox-inline' || $option['type'] == 'checkbox_inline') {
	$option['options'] = (isset($option['select-options-from-raw'])) ? $option['select-options-from-raw'] : $option['options'];
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$inline_class = '';
		if ($option['type'] == 'checkbox-inline' || $option['type'] == 'checkbox_inline') {
			$inline_class = 'cj-inline-block cj-mr-10';
		}
		foreach ($option['options'] as $opt => $val) {
			$unique_string_id = $option['id'] . '_' . $opt;
			if ($option_value !== '' && in_array($opt, $option_value)) {
				$opts .= '<div class="cj-mb-10 ' . $inline_class . '"><input id="' . $unique_string_id . '" checked name="' . $option_name . '[]" type="checkbox" value="' . $opt . '" ' . $params . '><label for="' . $unique_string_id . '" class="">' . $val . '</label></div>';
			} else {
				$opts .= '<div class="cj-mb-10 ' . $inline_class . '"><input id="' . $unique_string_id . '" name="' . $option_name . '[]" type="checkbox" value="' . $opt . '" ' . $params . '><label for="' . $unique_string_id . '" class="">' . $val . '</label></div>';
			}
		}
		$field_html[] = $opts;
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'radio' || $option['type'] == 'radio-inline' || $option['type'] == 'radio_inline') {
	$option['options'] = (isset($option['select-options-from-raw'])) ? $option['select-options-from-raw'] : $option['options'];
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$inline_class = '';
		if ($option['type'] == 'radio-inline') {
			$inline_class = 'cj-inline-block cj-mr-10';
		}
		foreach ($option['options'] as $opt => $val) {
			$unique_string_id = $option['id'] . '_' . $opt;
			if ($option_value !== '' && $option_value == $opt) {
				$opts .= '<div class="cj-mb-10 ' . $inline_class . '"><input id="' . sanitize_title($unique_string_id) . '" checked name="' . $option_name . '" type="radio" value="' . $opt . '" ' . $params . '><label for="' . $unique_string_id . '" class="">' . $val . '</label></div>';
			} else {
				$opts .= '<div class="cj-mb-10 ' . $inline_class . '"><input id="' . sanitize_title($unique_string_id) . '" name="' . $option_name . '" type="radio" value="' . $opt . '" ' . $params . '><label for="' . $unique_string_id . '" class="">' . $val . '</label></div>';
			}
		}
		$field_html[] = $opts;
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'user') {
	$opts = '';
	$opts = '<option value="">None</option>';

	$query_vars = ['orderby' => 'display_name'];
	if (isset($option['query_vars']) && is_array($option['query_vars'])) {
		foreach ($option['query_vars'] as $query_var => $query_value) {
			$query_vars[$query_var] = $query_value;
		}
	}

	$options = get_users($query_vars);
	foreach ($options as $opt => $val) {
		if ($val->ID == $option_value) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->display_name . ' (' . $val->user_email . ')</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->display_name . ' (' . $val->user_email . ')</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'users') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_users(['orderby' => 'display_name']);
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($val->ID, $option_value)) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->display_name . ' (' . $val->user_email . ')</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->display_name . ' (' . $val->user_email . ')</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'role') {
	$opts = '<option value="default">' . esc_attr__('None | Default', 'wp-builder-locale') . '</option>';
	$options = $this->getRoles();
	foreach ($options as $opt => $val) {
		if ($opt == $option_value) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$visitor_selected = ($option_value == 'visitor') ? 'selected' : '';
	$opts .= '<option ' . $visitor_selected . ' value="visitor">' . esc_attr__('Visitor', 'wp-builder-locale') . '</option>';
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'roles') {
	$options = $this->getRoles();
	$opts = '';
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($opt, $option_value)) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'page') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC', 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => '10000']);
	foreach ($options as $opt => $val) {
		if ($val->ID == $option_value) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->post_title . '</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->post_title . '</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'pages') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC', 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => '10000']);
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($val->ID, $option_value)) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->post_title . '</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->post_title . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'post') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_posts(['sort_column' => 'post_title', 'sort_order' => 'ASC', 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => '10000']);
	foreach ($options as $opt => $val) {
		if ($val->ID == $option_value) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->post_title . '</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->post_title . '</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'posts') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_posts(['sort_column' => 'post_title', 'sort_order' => 'ASC', 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => '10000']);
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($val->ID, $option_value)) {
			$opts .= '<option selected value="' . $val->ID . '">' . $val->post_title . '</option>';
		} else {
			$opts .= '<option value="' . $val->ID . '">' . $val->post_title . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'post-type') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_post_types();
	if (isset($option['exclude']) && is_array($option['exclude']) && !empty($option['exclude'])) {
		foreach ($option['exclude'] as $exclude_key => $exclude_value) {
			unset($options[$exclude_value]);
		}
	}
	foreach ($options as $opt => $val) {
		if ($opt == $option_value) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'post-types') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_post_types();
	if (isset($option['exclude']) && is_array($option['exclude']) && !empty($option['exclude'])) {
		foreach ($option['exclude'] as $exclude_key => $exclude_value) {
			unset($options[$exclude_value]);
		}
	}
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($opt, $option_value)) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'category') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_categories(['type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category', 'number' => 10000]);
	foreach ($options as $opt => $val) {
		if ($val->slug == $option_value) {
			$opts .= '<option selected value="' . $val->slug . '">' . $val->name . '</option>';
		} else {
			$opts .= '<option value="' . $val->slug . '">' . $val->name . '</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'categories') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_categories(['type' => 'post', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'taxonomy' => 'category', 'number' => 10000]);
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($val->slug, $option_value)) {
			$opts .= '<option selected value="' . $val->slug . '">' . $val->name . '</option>';
		} else {
			$opts .= '<option value="' . $val->slug . '">' . $val->name . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'tag') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_tags();
	foreach ($options as $opt => $val) {
		if ($val->slug == $option_value) {
			$opts .= '<option selected value="' . $val->slug . '">' . $val->name . '</option>';
		} else {
			$opts .= '<option value="' . $val->slug . '">' . $val->name . '</option>';
		}
	}

	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'tags') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$options = get_tags();
	foreach ($options as $opt => $val) {
		if (is_array($option_value) && in_array($val->slug, $option_value)) {
			$opts .= '<option selected value="' . $val->slug . '">' . $val->name . '</option>';
		} else {
			$opts .= '<option value="' . $val->slug . '">' . $val->name . '</option>';
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'taxonomy') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$taxonomies = get_taxonomies();
	$exclude_taxonomies = ['category', 'post_tag', 'nav_menu', 'link_category', 'post_format'];
	$terms = '';
	$taxonomy_array = [];
	foreach ($taxonomies as $key => $taxonomy) {
		if (!in_array($key, $exclude_taxonomies)) {
			$taxonomy_array[] = $key;
		}
	}
	$options = get_terms($taxonomy_array, ['orderby' => 'name', 'hide_empty' => false]);

	if (is_array($options)) {
		foreach ($options as $opt => $val) {
			$t_val = $val->slug . '~~~~' . $val->taxonomy;
			if ($t_val == $option_value) {
				$opts .= '<option selected value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			} else {
				$opts .= '<option value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			}
		}
	}

	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'taxonomies') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$taxonomies = get_taxonomies();
	$exclude_taxonomies = ['category', 'post_tag', 'nav_menu', 'link_category', 'post_format'];
	if (isset($option['exclude']) && is_array($option['exclude'])) {
		$exclude_taxonomies = $option['exclude'];
	}
	$terms = '';
	$taxonomy_array = [];
	foreach ($taxonomies as $key => $taxonomy) {
		if (!in_array($key, $exclude_taxonomies)) {
			$taxonomy_array[] = $key;
		}
	}
	$options = get_terms($taxonomy_array, ['orderby' => 'name', 'hide_empty' => false]);

	if (is_array($options)) {
		foreach ($options as $opt => $val) {
			$t_val = $val->slug . '~~~~' . $val->taxonomy;
			if (is_array($option_value) && !empty($option_value) && in_array($t_val, $option_value)) {
				$opts .= '<option selected value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			} else {
				$opts .= '<option value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			}
		}
	}
	$field_html[] = '<select multiple id="' . $form_field_id . '" name="' . $option_name . '[]" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'taxonomy-terms' || $option['type'] == 'taxonomy_terms') {
	$opts = '';
	$opts = '<option value="">None</option>';
	$taxonomies = get_taxonomies();
	$exclude_taxonomies = ['nav_menu', 'link_category', 'post_format'];
	$get_terms_params = ['orderby' => 'name', 'hide_empty' => false];
	if (isset($option['get_terms_params']) && is_array($option['get_terms_params']) && !empty($option['get_terms_params'])) {
		foreach ($option['get_terms_params'] as $p_key => $p_value) {
			$get_terms_params[$p_key] = $p_value;
		}
	}
	$terms = '';
	$taxonomy_array = [];
	foreach ($taxonomies as $tax_key => $taxonomy) {
		if (!in_array($tax_key, $exclude_taxonomies)) {
			$taxonomy_array[] = $tax_key;
		}
	}
	$term_opts = get_terms($taxonomy_array, $get_terms_params);

	if (is_array($term_opts)) {
		foreach ($term_opts as $opt => $val) {
			$t_val = $val->slug . '@' . $val->taxonomy;
			if ($t_val == $option_value) {
				$opts .= '<option selected value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			} else {
				$opts .= '<option value="' . $t_val . '">' . $val->name . ' (' . $val->taxonomy . ')</option>';
			}
		}
	}

	$field_html[] = '<div class="form-taxonomy">';
	$field_html[] = '<div class="cj-form-field">';
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'code-css') {
	$field_html[] = '<textarea data-code="code-css" id="' . $form_field_id . '" name="' . $option_name . '" ' . $params . '>' . $option_value . '</textarea>';
}
if ($option['type'] == 'code-html') {
	$field_html[] = '<textarea data-code="code-html" id="' . $form_field_id . '" name="' . $option_name . '" ' . $params . '>' . html_entity_decode($option_value) . '</textarea>';
}
if ($option['type'] == 'code-js') {
	$field_html[] = '<textarea data-code="code-js" id="' . $form_field_id . '" name="' . $option_name . '" ' . $params . '>' . $option_value . '</textarea>';
}
if ($option['type'] == 'wysiwyg') {
	$editor_id = $form_field_id . '-wp-builder';
	$editor_settings = [
		'wpautop' => false,
		'media_buttons' => true,
		'textarea_name' => $option_name,
		'textarea_rows' => 7,
		'teeny' => false,
	];
	if (isset($option['editor_settings']) && is_array($option['editor_settings'])) {
		foreach ($option['editor_settings'] as $e_key => $e_value) {
			$editor_settings[$e_key] = $e_value;
		}
	}
	ob_start();
	$content = html_entity_decode($option_value);
	wp_editor($content, $editor_id, $editor_settings);
	$editor_panel = ob_get_clean();
	$field_html[] = '<div id="wysiwyg-' . $form_field_id . '" class="cj-wysiwyg">' . $editor_panel . '</div>';
}
if ($option['type'] == 'wysiwyg-tiny') {
	$editor_id = str_replace('-', '', str_replace('_', '', strtolower($option_name)));
	$editor_settings = [
		'wpautop' => false,
		'media_buttons' => true,
		'textarea_name' => $option_name,
		'textarea_rows' => 7,
		'teeny' => true,
	];
	if (isset($option['editor_settings']) && is_array($option['editor_settings'])) {
		foreach ($option['editor_settings'] as $e_key => $e_value) {
			$editor_settings[$e_key] = $e_value;
		}
	}
	ob_start();
	$content = html_entity_decode($option_value);
	wp_editor($content, $editor_id, $editor_settings);
	$editor_panel = ob_get_clean();
	$field_html[] = '<div class="cj-wysiwyg">' . $editor_panel . '</div>';
}
if ($option['type'] == 'file' || $option['type'] == 'files') {

	$multiple_files = (isset($option['type']) && $option['type'] == 'files') ? true : false;
	$multiple = ($multiple_files) ? true : false;
	$button_text = (isset($option['type']) && $option['type'] == 'files') ? __('Select Files', 'wp-builder-locale') : __('Select File', 'wp-builder-locale');
	$field_html[] = '<div class="cj-form-file">';
	$field_html[] = '<div class="cj-form-field ">';
	$field_html[] = '<label data-multiple="' . $multiple_files . '" for="' . $option_name . '" data-selector-id="' . sanitize_title($option_name) . '" class="cj-upload-files">';
	if ($option['type'] == 'files') {
		$field_html[] = '<span class="cj-button"><span class="cj-icon cj-is-small cj-mr-10"><i class="fa fa-file"></i></span>' . __('Select Files', 'wp-builder-locale') . '</span>';
	} else {
		$field_html[] = '<span class="cj-button"><span class="cj-icon cj-is-small cj-mr-10"><i class="fa fa-file"></i></span>' . __('Select File', 'wp-builder-locale') . '</span>';
	}
	//$field_html[] = '<input type="file" name="' . $option_name . '" value="" ' . $multiple . ' class="opacity-0" >';
	$field_html[] = '</label>';
	$field_html[] = '</div>';

	$field_html[] = '<div class="uploaded-file">';
	$field_html[] = '<ul id="filelist-' . sanitize_title($option_name) . '" data-id="' . $form_field_id . '" class="cj-file-list">';
	if ($option['type'] == 'files' && is_array($option_value)) {
		foreach ($option_value as $i_key => $img_url) {
			if ($img_url != '') {
				$field_html[] = '<li>
						<div class="cj-image-object cj-cover" style="background-image: url(' . $img_url . ')"></div>
                        <input type="hidden" name="' . $option_name . '[]" value="' . $img_url . '">
                        <a class="cj-remove-file cj-delete cj-is-small" href="#"></a>
                        <div class="cj-overlay cj-opacity-90"></div>
                        </li>';
			}
		}
	}
	if ($option['type'] == 'file') {
		$img_url = $option_value;
		if ($img_url != '' && !is_array($img_url)) {
			$field_html[] = '<li>
						<div class="cj-image-object cj-cover" style="background-image: url(' . $img_url . ')"></div>
                        <input type="hidden" name="' . $option_name . '" value="' . $img_url . '">
                        <a class="cj-remove-file cj-delete cj-is-small" href="#"></a><div class="cj-overlay cj-opacity-90"></div>
                        </li>';
		}
	}
	$field_html[] = '</ul>';

	$field_html[] = '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'user_avatar' || $option['type'] == 'user-avatar') {
	$option_value = ($option_value == '') ? $this->default_avatar_url : $option_value;
	$option['default_avatar_url'] = $this->default_avatar_url;
	$option['upload_button_text'] = __('Upload image', 'wp-builder-locale');
	$unique_string = $this->uniqueString();
	$field_html[] = '<div id="cj-user-avatar-form-field-' . $unique_string . '" v-cloak class="cj-user-avatar-form-field"><vue-user-avatar is_user_logged_in="' . is_user_logged_in() . '" option_data=\'[' . json_encode($option) . ']\'></vue-user-avatar></div>';
}
if ($option['type'] == 'vue-file-upload' || $option['type'] == 'vue_file_upload') {
	$option['upload_button_text'] = __('Select File(s)', 'wp-builder-locale');
	$vue_class = 'vue-' . sanitize_title($option_name);
	$vue_class = str_replace('_', '-', $vue_class);
	$post_route = rest_url('cjwpbldr') . '/core/file/upload';
	$post_max_size = $this->getBytes($this->postMaxSize());
	$upload_max_file_size = $this->getBytes($this->uploadMaxFileSize());
	$field_html[] = '<div data-vue-app="' . $vue_class . '">';
	$field_html[] = '<vue-file-upload post-max-size="' . $post_max_size . '" upload-max-filesize="' . $upload_max_file_size . '" input-id="' . $option['id'] . '" post-route="' . $post_route . '" welcome-message="' . __('Drag or Click to select a file.', 'wp-builder-locale') . '"></vue-file-upload>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'file-raw') {

	$field_html[] = '<div class="cj-form-file-raw">';
	$field_html[] = '<div class="cj-file cj-has-name">';
	$field_html[] = '<label class="cj-file-label">';
	$field_html[] = '<input id="' . $form_field_id . '" class="cj-file-input" type="file" name="' . $option_name . '" value="' . $option_value . '" ' . $params . '>';
	$field_html[] = '<span class="cj-file-cta">';
	$field_html[] = '<span class="cj-file-icon">';
	$field_html[] = '<i class="fas fa-upload"></i>';
	$field_html[] = '</span>';
	$field_html[] = '<span class="cj-file-label">';
	$field_html[] = __('Choose a file', 'wp-builder-locale');
	$field_html[] = '</span>';
	$field_html[] = '</span>';
	$field_html[] = '<span class="cj-file-name">';
	$field_html[] = '<span>' . __('No file selected.', 'wp-builder-locale') . '</span>';
	$field_html[] = '</span>';
	$field_html[] = '</label>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'sortable') {
	$sortables = '';
	if (is_array($option_value)) {
		foreach ($option_value as $key => $value) {
			$sortables .= '<li>';
			$sortables .= '<span class="text-bold">' . $value . '</span>';
			$sortables .= '<i class="fa fa-sort cj-is-pulled-right opacity-50"></i>';
			$sortables .= '<input id="' . $form_field_id . '-' . $key . '" name="' . $option_name . '[' . $key . ']" type="hidden" value="' . $option_value[$key] . '" />';
			$sortables .= '</li>';
		}
	}
	$field_html[] = '<ul class="cj-sortable">';
	$field_html[] = $sortables;
	$field_html[] = '</ul>';
}
if ($option['type'] == 'repeatable') {
	$field_html[] = '<div class="cj-form-repeatable">';
	$field_html[] = '<div class="cj-form-field cj-repeatable">';
	$default_options = (is_array($option_value)) ? $option_value : [__('Some value', 'wp-builder-locale')];
	if (is_array($default_options)) {
		$field_html[] = '<div id="' . $form_field_id . '-options" class="cj-sortable">';
		foreach ($default_options as $o_key => $o_value) {
			$o_value = esc_textarea($o_value);
			$default_placeholder = (isset($option['default_placeholders']) && isset($option['default_placeholders'][$o_key])) ? $option['default_placeholders'][$o_key] : '';
			$field_html[] = '<div class="cj-repeatable-field">';
			$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '[' . $o_key . ']" type="text" value="' . $o_value . '" placeholder="' . $default_placeholder . '" class="" />';
			$field_html[] = '<span class="cj-input-group-addon cj-pointer" data-option-id="' . $form_field_id . '">';
			if (!isset($option['disable_new'])) {
				$field_html[] = '<span class="cj-remove-repeatable-option" data-msg-not-allowed="' . __('Not Allowed!', 'wp-builder-locale') . '"><i class="fa fa-minus cj-color-danger"></i></span>';
			}
			$field_html[] = '</span>';
			$field_html[] = '</div>';
		}
		$field_html[] = '</div>';
	}
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	if (!isset($option['disable_new'])) {
		$field_html[] = '<a class="cj-button cj-is-success cj-mt-10 cj-mb-10 cj-add-repeatable" data-id="' . $option_name . '"><i class="fa fa-plus"></i></a>';
	}
}
if ($option['type'] == 'google_recaptcha' || $option['type'] == 'google-recaptcha') {
	$recaptcha = $this->reCaptcha();
	if ($recaptcha) {
		$field_html[] = '<div id="' . $form_field_id . '">';
		$field_html[] = $recaptcha->getHtml();
		$field_html[] = '</div>';
	}
}
if ($option['type'] == 'min-max' || $option['type'] == 'min_max') {
	$min_value = (isset($option_value['min'])) ? stripcslashes(htmlentities($option_value['min'])) : '';
	$max_value = (isset($option_value['max'])) ? stripcslashes(htmlentities($option_value['max'])) : '';
	// params
	$min_params = '';
	$max_params = '';
	if (!isset($option['min_params']) || empty($option['min_params'])) {
		$option['min_params']['placeholder'] = __('Min', 'wp-builder-locale');
	}
	if (!isset($option['max_params']) || empty($option['max_params'])) {
		$option['max_params']['placeholder'] = __('Max', 'wp-builder-locale');
	}
	if (isset($option['min_params']) && is_array($option['min_params']) && !empty($option['min_params'])) {
		foreach ($option['min_params'] as $attribute => $param_value) {
			$min_params .= $attribute . '="' . $param_value . '" ';
		}
	}
	if (isset($option['max_params']) && is_array($option['max_params']) && !empty($option['max_params'])) {
		foreach ($option['max_params'] as $attribute => $param_value) {
			$max_params .= $attribute . '="' . $param_value . '" ';
		}
	}

	$min_container_class = 'cj-one_half';
	$max_container_class = 'cj-one_half cj-last';
	$required = (isset($option['validation_rules']) && isset($option['validation_rules']['required']) && $option['validation_rules']['required'] == 'yes') ? ' required' : '';

	// options
	$opts = '';
	$options['options'] = (is_array($option['options'])) ? array_filter($option['options']) : [];
	if (is_array($option['options']) && !empty($options['options'])) {
		$min_container_class = 'cj-one-third';
		$max_container_class = 'cj-one-third';
		$opts .= '<p class="cj-control cj-mb-0">';
		$opts .= '<select name="' . $option_name . '[type]" class="selectize" ' . $required . ' >';
		foreach ($option['options'] as $o_key => $o_value) {
			if (isset($option_value['type']) && $option_value['type'] == $o_key) {
				$opts .= '<option selected value="' . $o_key . '">' . $o_value . '</option>';
			} else {
				$opts .= '<option value="' . $o_key . '">' . $o_value . '</option>';
			}
		}
		$opts .= '</select>';
		$opts .= '</p>';
	}

	$field_html[] = '<div class="cj-control"><input id="' . $form_field_id . '-min" name="' . $option_name . '[min]" type="number" value="' . $min_value . '" ' . $min_params . ' class="cj-input" ' . $required . ' /></div>';
	$field_html[] = '<div class="cj-control"><input id="' . $form_field_id . '-max" name="' . $option_name . '[max]" type="number" value="' . $max_value . '" ' . $max_params . ' class="cj-input" ' . $required . ' /></div>';
	$field_html[] = '<div class="cj-control">' . $opts . '</div>';
}

if ($option['type'] == 'google-address' || $option['type'] == 'google_address') {
	$filter = (isset($option['filter'])) ? $option['filter'] : '';
	$field_html[] = '<div class="cj-google-addresses-autocomplete" data-filter="' . $filter . '">';
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" ' . $params . ' type="text" value="' . $option_value . '" />';
	$field_html[] = '</div>';
	$maps_key = $this->getOption('core_google_maps_key');
	if ($maps_key == '' && current_user_can('manage_options')) {
		$field_html[] = '<div class="cj-notification cj-is-warning cj-mt-10 cj-mb-0">';
		$field_html[] = __('Google maps API key is not specified in Global Settings page.', 'wp-builder-locale');
		$field_html[] = '</div>';
	}
}
if ($option['type'] == 'google-location' || $option['type'] == 'google_location') {
	$filter = (isset($option['filter'])) ? $option['filter'] : '';
	$field_html[] = '<div class="cj-google-location-autocomplete" data-filter="' . $filter . '">';
	$field_html[] = '<input ' . $params . ' type="search" style="border-radius: 0px;" />';

	$field_html[] = '<div class="cj-clearfix cj-mt-10">';
	$field_html[] = '<input readonly id="' . $form_field_id . '" name="' . $option_name . '" class="cj-input" placeholder="' . __('Latitude|Longitude', 'wp-builder-locale') . '" type="text" value="' . $option_value . '" />';
	$field_html[] = '</div><!--clearfix-->';

	$field_html[] = '</div>';
	$maps_key = $this->getOption('core_google_maps_key');
	if ($maps_key == '' && current_user_can('manage_options')) {
		$field_html[] = '<div class="cj-notification cj-is-warning cj-mt-10 cj-mb-0">';
		$field_html[] = __('Google maps API key is not specified in Global Settings page.', 'wp-builder-locale');
		$field_html[] = '</div>';
	}
}
if ($option['type'] == 'button-with-icon') {

	if (!is_array($option_value)) {
		$option_value = [];
	}
	$option_value['text'] = (isset($option_value['text'])) ? $option_value['text'] : '';
	$option_value['icon'] = (isset($option_value['icon'])) ? $option_value['icon'] : '';
	$option_value['align'] = (isset($option_value['align'])) ? $option_value['align'] : '';
	$option['params']['placeholder'] = (isset($option['params']['placeholder'])) ? $option['params']['placeholder'] : '';

	$custom_url = (isset($option_value['custom_url'])) ? $option_value['custom_url'] : '';

	$fa_array = $this->fontAwesomeIconsArray();
	$pe_icons_array = $this->arrays('social-icons');
	$icons_array = array_merge($pe_icons_array, $fa_array);

	$icons = '<option value="">' . __('Select Icon', 'wp-builder-locale') . '</option>';
	foreach ($icons_array as $o_key => $o_value) {
		if ($o_key == $option_value['icon']) {
			$icons .= '<option selected value="' . $o_key . '">' . $o_value . '</option>';
		} else {
			$icons .= '<option value="' . $o_key . '">' . $o_value . '</option>';
		}
	}

	$align_array = [
		'left' => __('Left', 'wp-builder-locale'),
		'right' => __('Right', 'wp-builder-locale'),
	];
	$icon_alignment = '<option value="">' . __('Align', 'wp-builder-locale') . '</option>';
	foreach ($align_array as $a_key => $a_value) {
		if ($a_key == $option_value['align']) {
			$icon_alignment .= '<option selected value="' . $a_key . '">' . $a_value . '</option>';
		} else {
			$icon_alignment .= '<option value="' . $a_key . '">' . $a_value . '</option>';
		}
	}

	$button_class_array = [
		'cj-is-small' => __('Small', 'wp-builder-locale'),
		'cj-is-medium' => __('Medium', 'wp-builder-locale'),
		'cj-is-large' => __('Large', 'wp-builder-locale'),
	];
	$button_class = '<option value="">' . __('Button size', 'wp-builder-locale') . '</option>';
	foreach ($button_class_array as $c_key => $c_value) {
		if (isset($option_value['size']) && $c_key == $option_value['size']) {
			$button_class .= '<option selected value="' . $c_key . '">' . $c_value . '</option>';
		} else {
			$button_class .= '<option value="' . $c_key . '">' . $c_value . '</option>';
		}
	}

	$button_style_array = [
		'cj-is-primary' => __('primary', 'wp-builder-locale'),
		'cj-is-success' => __('success', 'wp-builder-locale'),
		'cj-is-danger' => __('danger', 'wp-builder-locale'),
		'cj-is-warning' => __('warning', 'wp-builder-locale'),
		'cj-is-info' => __('info', 'wp-builder-locale'),
		'cj-is-dark' => __('dark', 'wp-builder-locale'),
		'cj-is-light' => __('light', 'wp-builder-locale'),
	];
	$button_style = '<option value="">' . __('Button color', 'wp-builder-locale') . '</option>';
	foreach ($button_style_array as $c_key => $c_value) {
		if (isset($option_value['style']) && $c_key == $option_value['style']) {
			$button_style .= '<option selected value="' . $c_key . '">' . $c_value . '</option>';
		} else {
			$button_style .= '<option value="' . $c_key . '">' . $c_value . '</option>';
		}
	}

	$button_outlined_array = [
		'yes' => __('Yes', 'wp-builder-locale'),
		'no' => __('No', 'wp-builder-locale'),
	];
	$button_outlined = '<option value="">' . __('Outlined', 'wp-builder-locale') . '</option>';
	foreach ($button_outlined_array as $c_key => $c_value) {
		if (isset($option_value['outlined']) && $c_key == $option_value['outlined']) {
			$button_outlined .= '<option selected value="' . $c_key . '">' . $c_value . '</option>';
		} else {
			$button_outlined .= '<option value="' . $c_key . '">' . $c_value . '</option>';
		}
	}

	$button_rounded_array = [
		'yes' => __('Yes', 'wp-builder-locale'),
		'no' => __('No', 'wp-builder-locale'),
	];
	$button_rounded = '<option value="">' . __('Rounded', 'wp-builder-locale') . '</option>';
	foreach ($button_rounded_array as $c_key => $c_value) {
		if (isset($option_value['rounded']) && $c_key == $option_value['rounded']) {
			$button_rounded .= '<option selected value="' . $c_key . '">' . $c_value . '</option>';
		} else {
			$button_rounded .= '<option value="' . $c_key . '">' . $c_value . '</option>';
		}
	}

	$button_target_array = [
		'_blank' => __('New Window', 'wp-builder-locale'),
		'_self' => __('Same Window', 'wp-builder-locale'),
	];
	$button_target = '<option value="">' . __('Open link in', 'wp-builder-locale') . '</option>';
	foreach ($button_target_array as $c_key => $c_value) {
		if (isset($option_value['target']) && $c_key == $option_value['target']) {
			$button_target .= '<option selected value="' . $c_key . '">' . $c_value . '</option>';
		} else {
			$button_target .= '<option value="' . $c_key . '">' . $c_value . '</option>';
		}
	}

	$link_url = (isset($option_value['link_url'])) ? $option_value['link_url'] : '';

	$field_html[] = <<<EOD
	<div class="cj-mb-10">
	    <div class="cj-field">
            <label class="cj-label">Link this button to:</label>
	        <input name="{$option_name}[link_url]" value="{$link_url}" type="text" placeholder="Link Url" class="cj-input" />
        </div>
    </div>
	<div class="cj-clearfix">
	<div class="cj-one_fourth cj-mb-0 cj-field">
	<label class="cj-label">Button Text</label>
	<p class="cj-control cj-mb-0 cj-is-expanded">
    	<input name="{$option_name}[text]" class="cj-input" type="text" value="{$option_value['text']}" placeholder="{$option['params']['placeholder']}">
  	</p>
	</div>
  	<div class="cj-one_fourth cj-mb-0 cj-field">
  	<label class="cj-label">Select an icon</label>
  	<p class="cj-control cj-mb-0">
    	<select class="selectize" name="{$option_name}[icon]" style="width: 100%">
            {$icons}
        </select>
  	</p>
  	</div>
  	<div class="cj-one_fourth cj-mb-0 cj-field">
  	<label class="cj-label">Align icon to</label>
  	<p class="cj-control cj-mb-0">
    	<select class="selectize" name="{$option_name}[align]">
            {$icon_alignment}
        </select>
  	</p>
	</div>
	<div class="cj-one_fourth cj-mb-0 cj-last cj-field">
  	<label class="cj-label">Button size</label>
  	<p class="cj-control cj-mb-0">
    	<select class="selectize" name="{$option_name}[size]">
            {$button_class}
        </select>
  	</p>
	</div>
	<div class="cj-one_fourth cj-mb-0 cj-field">
	    <p class="cj-control">
	    <label class="cj-label">Button color</label>
            <select class="selectize" name="{$option_name}[style]">
                {$button_style}
            </select>
        </p>
    </div>
    <div class="cj-one_fourth cj-mb-0 cj-field">
	    <p class="cj-control">
	    <label class="cj-label">Outlined?</label>
            <select class="selectize" name="{$option_name}[outlined]">
                {$button_outlined}
            </select>
        </p>
    </div>
    <div class="cj-one_fourth cj-mb-0 cj-field">
	    <p class="cj-control">
	    <label class="cj-label">Rounded?</label>
            <select class="selectize" name="{$option_name}[rounded]">
                {$button_rounded}
            </select>
        </p>
    </div>
    <div class="cj-one_fourth cj-last cj-mb-0 cj-field">
	    <p class="cj-control">
	        <label class="cj-label">Link target?</label>
            <select class="selectize" name="{$option_name}[target]">
                {$button_target}
            </select>
        </p>
    </div>
</div>
<div class="cj-mt-10">
    <div class="cj-field">
        <label class="cj-label">Use Custom Image?</label>
        <input name="{$option_name}[custom_url]" value="{$custom_url}" type="text" placeholder="or specify custom button image url." class="cj-input" />
	</div>
</div>
EOD;
}
if ($option['type'] == 'screenshot') {
	$screenshots = $option['options'];
	if (is_array($screenshots) && !empty($screenshots)) {
		$field_html[] = '<div class="cj-columns cj-is-multiline cj-is-mobile" style="border:none">';
		foreach ($screenshots as $s_key => $screenshot_url) {
			$checked = ($option_value == $s_key) ? 'checked' : '';
			$active_class = ($option_value == $s_key) ? 'cj-is-active' : '';
			$field_html[] = '<div class="cj-column cj-exclude-full cj-is-4-widescreen cj-is-4-desktop cj-is-6-tablet cj-is-6-mobile cj-pt-15 cj-pb-0">';
			$field_html[] = '<label class="' . $active_class . '">';
			$field_html[] = '<input type="radio" name="' . $option_name . '" value="' . $s_key . '" ' . $checked . '>';
			$field_html[] = '<img src="' . $screenshot_url . '" />';
			$field_html[] = '</label>';
			$field_html[] = '</div>';
		}
		$field_html[] = '</div><!-- cj-columns -->';
	}
}
if ($option['type'] == 'font-styles') {
	ob_start();
	include 'admin-form-font-styles.php';
	$field_html[] = ob_get_clean();
}
if ($option['type'] == 'gradient') {
	$color_1 = (isset($option_value['color-1'])) ? $option_value['color-1'] : '#139cec';
	$color_2 = (isset($option_value['color-2'])) ? $option_value['color-2'] : '#c3325f';
	$angle = (isset($option_value['angle'])) ? $option_value['angle'] : '45deg';
	$field_html[] = '<div class="cj-clearfix">';
	$field_html[] = '<div class="cj-field cj-is-grouped">';
	$field_html[] = '<div class="cj-control"><input class="cj-input" data-input-type="color" id="' . $form_field_id . '-1" name="' . $option_name . '[color-1]" type="text" value="' . $color_1 . '"  /> </div>';
	$field_html[] = '<div class="cj-control"><input class="cj-input" data-input-type="color" id="' . $form_field_id . '-2" name="' . $option_name . '[color-2]" type="text" value="' . $color_2 . '"  /> </div>';
	$field_html[] = '<div class="cj-control"><input id="' . $form_field_id . '-deg" name="' . $option_name . '[angle]" type="text" value="' . $angle . '" placeholder="' . __('Angle e.g. 90deg or -45deg', 'wp-builder-locale') . '" class="cj-input cj-mb-0" style="width: 100px;" /> </div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'nav_menu' || $option['type'] == 'nav-menu') {

	$nav_menus = get_terms([
		'taxonomy' => 'nav_menu',
		'hide_empty' => false,
	]);
	$option['options'] = [];
	if (!empty($nav_menus)) {
		foreach ($nav_menus as $menu => $menu_term) {
			$option['options'][$menu_term->slug] = $menu_term->name;
		}
	}
	$opts = '';
	$opts = '<option value="">None</option>';
	if (!empty($option['options'])) {
		foreach ($option['options'] as $opt => $val) {
			if ($option_value != '' && $opt == $option_value) {
				$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
	}
	$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'sidebar') {
	$sidebars = $wp_registered_sidebars;
	$option['options'] = [];
	if (!empty($sidebars)) {
		foreach ($sidebars as $sidebar_id => $sidebar) {
			$option['options'][$sidebar_id] = $sidebar['name'];
		}
	}
	$opts = '';
	$opts = '<option value="">None</option>';
	if (!empty($option['options'])) {
		foreach ($option['options'] as $opt => $val) {
			if ($option_value != '' && $opt == $option_value) {
				$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
	}
	$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'user-meta-key-value') {
	$user_meta_keys_array = $wpdb->get_results("SELECT distinct meta_key FROM $wpdb->usermeta");
	$meta_keys_array = [];
	foreach ($user_meta_keys_array as $key => $value) {
		$meta_keys_array[$value->meta_key] = $value->meta_key;
	}

	$meta_keys_field = [
		'id' => $option_name . '[meta_key]',
		'type' => 'dropdown',
		'label' => '',
		'params' => [
			'placeholder' => __('Select meta key', 'wp-builder-locale'),
			'class' => 'selectize',
		],
		'default' => (isset($option_value['meta_key'])) ? $option_value['meta_key'] : '',
		'options' => $meta_keys_array,
	];
	$meta_value_field = [
		'id' => $option_name . '[meta_value]',
		'type' => 'text',
		'label' => '',
		'params' => [
			'placeholder' => __('Specify meta value', 'wp-builder-locale')
		],
		'default' => (isset($option_value['meta_value'])) ? $option_value['meta_value'] : '',
		'options' => '',
	];

	$field_html[] = '<div class="meta-key-dropdown cj-mb-10">';
	$field_html[] = $this->formField($meta_keys_field);
	$field_html[] = '</div>';
	$field_html[] = '<div class="meta-value-textbox">';
	$field_html[] = $this->formField($meta_value_field);
	$field_html[] = '</div>';
}
if ($option['type'] == 'post-meta-key-value') {
	$post_meta_keys_array = $wpdb->get_results("SELECT distinct meta_key FROM $wpdb->postmeta");
	$meta_keys_array = [];
	foreach ($post_meta_keys_array as $key => $value) {
		$meta_keys_array[$value->meta_key] = $value->meta_key;
	}
	$meta_keys_field = [
		'id' => $option_name . '[meta_key]',
		'type' => 'dropdown',
		'label' => '',
		'params' => [
			'placeholder' => __('Select meta key', 'wp-builder-locale'),
			'class' => 'selectize',
		],
		'default' => (isset($option_value['meta_key'])) ? $option_value['meta_key'] : '',
		'options' => $meta_keys_array,
	];
	$meta_value_field = [
		'id' => $option_name . '[meta_value]',
		'type' => 'text',
		'label' => '',
		'params' => [
			'placeholder' => __('Specify meta value', 'wp-builder-locale')
		],
		'default' => (isset($option_value['meta_value'])) ? $option_value['meta_value'] : '',
		'options' => '',
	];

	$field_html[] = '<div class="meta-key-dropdown cj-mb-10">';
	$field_html[] = $this->formField($meta_keys_field);
	$field_html[] = '</div>';
	$field_html[] = '<div class="meta-value-textbox">';
	$field_html[] = $this->formField($meta_value_field);
	$field_html[] = '</div>';
}
if ($option['type'] == 'spacing') {
	$top = (isset($option_value['top'])) ? $option_value['top'] : '';
	$right = (isset($option_value['right'])) ? $option_value['right'] : '';
	$bottom = (isset($option_value['bottom'])) ? $option_value['bottom'] : '';
	$left = (isset($option_value['left'])) ? $option_value['left'] : '';

	$field_html[] = '<div class="cj-field cj-is-grouped">';
	$field_html[] = '<p class="cj-control"><input name="' . $option_name . '[top]" value="' . $top . '" type="text" class="cj-input" style="width: 150px;" placeholder="' . __('Top e.g. 20px', 'wp-builder-locale') . '" /></p>';
	$field_html[] = '<p class="cj-control"><input name="' . $option_name . '[right]" value="' . $right . '" type="text" class="cj-input" style="width: 150px;" placeholder="' . __('Right e.g. 20px', 'wp-builder-locale') . '" /></p>';
	$field_html[] = '<p class="cj-control"><input name="' . $option_name . '[bottom]" value="' . $bottom . '" type="text" class="cj-input" style="width: 150px;" placeholder="' . __('Bottom e.g. 20px', 'wp-builder-locale') . '" /></p>';
	$field_html[] = '<p class="cj-control"><input name="' . $option_name . '[left]" value="' . $left . '" type="text" class="cj-input" style="width: 150px;" placeholder="' . __('Left e.g. 20px', 'wp-builder-locale') . '" /></p>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'icon') {
	$option['options'] = $this->fontAwesomeIconsArray();
	if (isset($option['options']) && is_array($option['options']) && !empty($option['options'])) {
		$opts = '';
		$opts = '<option value="">None</option>';
		if (!empty($option['options'])) {
			foreach ($option['options'] as $opt => $val) {
				if ($option_value != '' && $opt == $option_value) {
					$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
				} else {
					$opts .= '<option value="' . $opt . '">' . $val . '</option>';
				}
			}
		}
		$use_form_options = (isset($option['select_options_from'])) ? 1 : 0;
		$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '" data-load-child-dropdown="' . $use_form_options . '" data-tags="true" ' . $params . '>';
		$field_html[] = $opts;
		$field_html[] = '</select>';
	} else {
		if (current_user_can('manage_options')) {
			$field_html[] = $this->alert('warning', __('Options for this field are not specified, please review field settings.', 'wp-builder-locale'), '', false);
		}
	}
}
if ($option['type'] == 'meta') {
	if (!isset($option['table']) || $option['table'] == '') {
		$field_html[] = $this->alert('warning', __('Table not defined in the options array.', 'wp-builder-locale'), '', false);
	} else {
		ob_start();
		include 'parts/admin-form-meta-field.php';
		$field_html[] = ob_get_clean();
	}
}
if ($option['type'] == 'responsive-columns') {

	$column_numbers = [
		'12' => '1',
		'6' => '2',
		'4' => '3',
		'3' => '4',
		'2' => '6',
	];

	$default_values = [
		'widescreen' => (isset($option_value['widescreen'])) ? $option_value['widescreen'] : '1',
		'desktop' => (isset($option_value['desktop'])) ? $option_value['desktop'] : '1',
		'tablet' => (isset($option_value['tablet'])) ? $option_value['tablet'] : '1',
		'mobile' => (isset($option_value['mobile'])) ? $option_value['mobile'] : '1',
	];

	$field_html[] = '<div class="cj-clearfix">';
	$field_html[] = '<div class="cj-one_fourth cj-mb-0">';
	$field_html[] = '<select name="' . $option_name . '[widescreen]" class="selectize" placeholder="' . __('Widescreen Columns', 'wp-builder-locale') . '">';
	foreach ($column_numbers as $n_key => $n_value) {
		$selected = ($default_values['widescreen'] == $n_key) ? ' selected' : '';
		$field_html[] = '<option ' . $selected . ' value="cj-is-' . $n_key . '-widescreen">' . sprintf(__('Widescreen: %s', 'wp-builder-locale'), $n_value) . '</option>';
	}
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '<div class="cj-one_fourth cj-mb-0">';
	$field_html[] = '<select name="' . $option_name . '[desktop]" class="selectize" placeholder="' . __('Desktop Columns', 'wp-builder-locale') . '">';
	foreach ($column_numbers as $n_key => $n_value) {
		$selected = ($default_values['desktop'] == $n_key) ? 'selected' : '';
		$field_html[] = '<option ' . $selected . ' value="cj-is-' . $n_key . '-desktop">' . sprintf(__('Desktop: %s', 'wp-builder-locale'), $n_value) . '</option>';
	}
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '<div class="cj-one_fourth cj-mb-0">';
	$field_html[] = '<select name="' . $option_name . '[tablet]" class="selectize" placeholder="' . __('Tablet Columns', 'wp-builder-locale') . '">';
	foreach ($column_numbers as $n_key => $n_value) {
		$selected = ($default_values['tablet'] == $n_key) ? 'selected' : '';
		$field_html[] = '<option ' . $selected . ' value="cj-is-' . $n_key . '-tablet">' . sprintf(__('Tablet: %s', 'wp-builder-locale'), $n_value) . '</option>';
	}
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '<div class="cj-one_fourth cj-mb-0 cj-last">';
	$field_html[] = '<select name="' . $option_name . '[mobile]" class="selectize" placeholder="' . __('Mobile Columns', 'wp-builder-locale') . '">';
	foreach ($column_numbers as $n_key => $n_value) {
		$selected = ($default_values['mobile'] == $n_key) ? 'selected' : '';
		$field_html[] = '<option ' . $selected . ' value="cj-is-' . $n_key . '-mobile">' . sprintf(__('Mobile: %s', 'wp-builder-locale'), $n_value) . '</option>';
	}
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
}
if ($option['type'] == 'country') {
	$opts = '';
	$countries_array = $this->arrays('countries');
	$opts = '<option value="">None</option>';
	foreach ($countries_array as $opt => $val) {
		if ($option_value != '' && $opt == $option_value) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '"  data-tags="true" ' . $params . '>';
	$field_html[] = $opts;
	$field_html[] = '</select>';
}
if ($option['type'] == 'aos') {
	ob_start();
	require_once 'parts/admin-form-aos.php';
	$field_html[] = ob_get_clean();
}

if ($option['type'] == 'auto-complete') {
	$settings_array = [
		'url' => admin_url('admin-ajax.php') . '?action=<span class="cj-color-danger">REGISTERED_ACTION</span>',
		'post_data' => [
			'search' => '*<span class="cj-color-danger">input_query</span>*',
		],
		'key_field' => 'key_field',
		'value_field' => 'value_field',
		'search_field' => 'search_field',
	];

	if (!isset($option['settings'])) {
		echo '<div class="cj-notification cj-is-danger">' . __('Settings for this field is not defined.', 'wp-builder-locale') . '</div>';
		echo '<pre class="cj-mb-20">';
		print_r($settings_array);
		echo '</pre>';
	}

	$url = (isset($option['settings']) && isset($option['settings']['url'])) ? $option['settings']['url'] : '';
	$post_data = (isset($option['settings']) && isset($option['settings']['post_data'])) ? json_encode($option['settings']['post_data']) : '';
	$key_field = (isset($option['settings']) && isset($option['settings']['key_field'])) ? $option['settings']['key_field'] : '';
	$value_field = (isset($option['settings']) && isset($option['settings']['value_field'])) ? $option['settings']['value_field'] : '';
	$search_field = (isset($option['settings']) && isset($option['settings']['search_field'])) ? $option['settings']['search_field'] : '';
	$field_html[] = '<div class="cjwpbldr-auto-complete" data-url="' . $url . '" data-post-data=\'' . $post_data . '\' data-key-field="' . $key_field . '" data-value-field="' . $value_field . '" data-search-field="' . $search_field . '">';
	$field_html[] = '<input id="' . $form_field_id . '" name="' . $option_name . '" type="text" value="' . $option_value . '" autocomplete="off" />';
	$field_html[] = '</div>';
}
if ($option['type'] == 'group' && is_array($option['items'])) {
	$opts = '';
	$selected = (is_array($option_value) && array_key_exists('selected_value', $option_value) && $option_value['selected_value'] != '') ? $option_value['selected_value'] : '';
	if (!isset($option['options_disable_blank'])) {
		$opts = '<option value="">None</option>';
	}
	foreach ($option['options'] as $opt => $val) {
		if ($selected != '' && $opt == $selected) {
			$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
		} else {
			$opts .= '<option value="' . $opt . '">' . $val . '</option>';
		}
	}

	$field_html[] = '<div class="cj-form-dropdown">';
	$field_html[] = '<div class="cj-form-field cj-has-group-items" data-group-name=' . $option['group'] . '>';
	$field_html[] = '<select id="' . $form_field_id . '" name="' . $option_name . '[selected_value]" data-tags="true" class="selectize">';
	$field_html[] = $opts;
	$field_html[] = '</select>';
	$field_html[] = '<div class="cj-help">' . $option['info'] . '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';

	$group_options = $this->compileGroupItems($option, $option_value, $selected);

	if (!empty($group_options)) {
		$field_html[] = '<div class="group-fields">';
		foreach ($group_options as $g_key => $g_option) {
			$field_html[] = '<div class="cj-field ' . $g_option['container_class'] . '">';
			$field_html[] = '<label class="cj-label">' . $g_option['label'] . '</label>';
			$field_html[] = '<p class="cj-control">';
			$field_html[] = $this->formField($g_option);
			$field_html[] = '</p>';
			if (isset($g_option['info'])) {
				$field_html[] = '<div class="cj-help">' . $g_option['info'] . '</div>';
			}
			$field_html[] = '</div>';
		}
		$field_html[] = '</div>';
	}
}

if ($option['type'] == 'css-styles') {
	ob_start();
	require 'parts/admin-form-css-styles.php';
	$field_html[] = ob_get_clean();
}
if ($option['type'] == 'css-shorthand') {

	$placeholder = (isset($option['params']['placeholder'])) ? $option['params']['placeholder'] : '';

	$field_html[] = '<div class="cj-columns cj-is-gapless">';
	$field_html[] = '<div class="cj-column"><p class="cj-control cj-mr-5"><label class="fs-12 pt-0">' . __('Top', 'wp-builder-locale') . '</label><input style="min-width: 100px; " type="text" name="' . $option_name . '[top]" class="cj-input cj-is-medium" placeholder="' . $placeholder . '" /></p></div>';
	$field_html[] = '<div class="cj-column"><p class="cj-control cj-mr-5"><label class="fs-12 pt-0">' . __('Right', 'wp-builder-locale') . '</label><input style="min-width: 100px; " type="text" name="' . $option_name . '[right]" class="cj-input cj-is-medium" placeholder="' . $placeholder . '" /></p></div>';
	$field_html[] = '<div class="cj-column"><p class="cj-control cj-mr-5"><label class="fs-12 pt-0">' . __('Left', 'wp-builder-locale') . '</label><input style="min-width: 100px; " type="text" name="' . $option_name . '[left]" class="cj-input cj-is-medium" placeholder="' . $placeholder . '" /></p></div>';
	$field_html[] = '<div class="cj-column"><p class="cj-control cj-mr-5"><label class="fs-12 pt-0">' . __('Bottom', 'wp-builder-locale') . '</label><input style="min-width: 100px; " type="text" name="' . $option_name . '[bottom]" class="cj-input cj-is-medium" placeholder="' . $placeholder . '" /></p></div>';
	$field_html[] = '</div>';
}

if ($option['type'] == 'submit') {
	$field_html[] = (isset($option['prefix'])) ? '' . $option['prefix'] : '';
	$field_html[] = '<button name="' . $option_name . '" type="submit"  ' . $params . ' >' . $option_value . '</button>';
	$field_html[] = (isset($option['suffix'])) ? '' . $option['suffix'] : '';
}

if ($option['type'] == 'full_address') { // check if field type == 'full_address'

	$field_name = $option['settings']['unique_id']; //Get unique_id of the field

	if (array_key_exists('params', $option)) {
		if (is_array($option['params']) && !empty($option['params'])) {
			$classes = ($option['params']['class'] != '') ? $option['params']['class'] : '';
		}
	}

	$field_html[] = '<!-- Full Address Field  -->';

	$field_html[] = '<div class="cj-special-field-full_address">';

	// Address Line 1
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('Address Line 1', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control">';
	$field_html[] = '<input name="' . $field_name . '[add_line_1]" class="cj-input ' . $classes . '" placeholder="Placeholder" type="text" value="' . $option['default']['add_line_1'] . '">';
	$field_html[] = '</div>';
	$field_html[] = '</div>';

	// Address Line 2
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('Address Line 2', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control">';
	$field_html[] = '<input name="' . $field_name . '[add_line_2]" class="cj-input ' . $classes . '" placeholder="Placeholder" type="text" value="' . $option['default']['add_line_2'] . '">';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '<div class="cj-field">';

	$field_html[] = '<div class="cj-field-body full-add-geo-vue">';

	//Country
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('Country', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control cj-is-expanded">';
	$field_html[] = '<div class="cj-select">';
	$field_html[] = '<select @change="fetchStates( selected_country )" v-model="selected_country" class="cj-select cj-input cj-is-medium" name="' . $field_name . '[country]">';
	$field_html[] = '<option v-for="( country, index ) in countries" :key="index" :value="country">{{country}}</option>';
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '<input type="hidden" id="selected-country" value="' . $option['default']['country'] . '">';
	$field_html[] = '</div>';

	// State
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('State', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control cj-is-expanded">';
	$field_html[] = '<div class="cj-select">';
	$field_html[] = '<select v-model="selected_state" class="cj-select" name="' . $field_name . '[state]">';
	$field_html[] = '<option v-if="states.length == 0" value="0">' . __('Please select a country first', 'wp-builder-locale') . '</option>';
	$field_html[] = '<option v-else v-for="( state, index ) in states" :key="index" :value="state">{{state}}</option>';
	$field_html[] = '</select>';
	$field_html[] = '</div>';
	$field_html[] = '<input type="hidden" id="selected-state" value="' . $option['default']['state'] . '">';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';

	// City
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<div class="cj-field-body">';
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('City', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control cj-is-expanded">';
	$field_html[] = '<input class="cj-input ' . $classes . '" type="text" name="' . $field_name . '[city]" placeholder="City" value="' . $option['default']['city'] . '">';
	$field_html[] = '</div>';
	$field_html[] = '</div>';

	// Zip
	$field_html[] = '<div class="cj-field">';
	$field_html[] = '<label class="label">' . __('Zip Code', 'wp-builder-locale') . '</label>';
	$field_html[] = '<div class="cj-control cj-is-expanded">';
	$field_html[] = '<input name="' . $field_name . '[zip]" class="cj-input ' . $classes . '" placeholder="Placeholder" type="text" value="' . $option['default']['zip'] . '">';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';
	$field_html[] = '</div>';

	$field_html[] = '<!--/ Full Address Field  -->';
}

$field_html = apply_filters('cjwpbldr_framework_field_html', $field_html, $option, $option_value, $option_name);

echo implode('', $field_html);
