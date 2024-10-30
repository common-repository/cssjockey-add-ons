<?php

trait cjwpbldr_helpers_forms {

	public function inputTextFieldTypes() {
		return ['text', 'text-hidden', 'textarea', 'number', 'email', 'url', 'search', 'text', 'text-readonly', 'text-disabled', 'number', 'email', 'url', 'password', 'date', 'date-time', 'date_time', 'date-range', 'time', 'google-address', 'google_address', 'google-location', 'gradient'];
	}

	public function selectFields() {
		return ['select', 'dropdown', 'form_options_dropdown', 'multi-dropdown', 'multi_dropdown', 'taxonomy-terms', 'user', 'users', 'page', 'pages', 'post', 'posts', 'role', 'roles', 'taxonomy', 'taxonomies', 'category', 'categories', 'post-type', 'post-types', 'tag', 'tags', 'nav_menu', 'nav-menu', 'sidebar', 'icon', 'country'];
	}

	public function formField( $option ) {
		ob_start();
		require($this->root_dir . "/framework/html/form-field.php");

		return ob_get_clean();
	}

	public function generateSelectOptions( $array = [], $selected = '' ) {
		$output = [];
		if( is_array( $array ) && ! empty( $array ) ) {
			foreach( $array as $key => $value ) {
				$selected_value = ($selected == $key) ? 'selected' : '';
				$output[] = '<option ' . $selected_value . ' value="' . $key . '">' . $value . '</option>';
			}
		}

		return implode( '', $output );
	}

	public function aosAnimationsArray( $var = null ) {
		$array = [
			"" => __( "None", 'wp-builder-locale' ),
			"fade" => "fade",
			"fade-up" => "fade-up",
			"fade-down" => "fade-down",
			"fade-left" => "fade-left",
			"fade-right" => "fade-right",
			"fade-up-right" => "fade-up-right",
			"fade-up-left" => "fade-up-left",
			"fade-down-right" => "fade-down-right",
			"fade-down-left" => "fade-down-left",
			"flip-up" => "flip-up",
			"flip-down" => "flip-down",
			"flip-left" => "flip-left",
			"flip-right" => "flip-right",
			"slide-up" => "slide-up",
			"slide-down" => "slide-down",
			"slide-left" => "slide-left",
			"slide-right" => "slide-right",
			"zoom-in" => "zoom-in",
			"zoom-in-up" => "zoom-in-up",
			"zoom-in-down" => "zoom-in-down",
			"zoom-in-left" => "zoom-in-left",
			"zoom-in-right" => "zoom-in-right",
			"zoom-out" => "zoom-out",
			"zoom-out-up" => "zoom-out-up",
			"zoom-out-down" => "zoom-out-down",
			"zoom-out-left" => "zoom-out-left",
			"zoom-out-right" => "zoom-out-right",
		];

		return (is_null( $var )) ? $array : $array[ $var ];
	}

	public function aosAnchorPlacement( $var = null ) {
		$array = [
			"" => __( "Select anchor placement", 'wp-builder-locale' ),
			"top-bottom" => "top-bottom",
			"top-center" => "top-center",
			"top-top" => "top-top",
			"center-bottom" => "center-bottom",
			"center-center" => "center-center",
			"center-top" => "center-top",
			"bottom-bottom" => "bottom-bottom",
			"bottom-center" => "bottom-center",
			"bottom-top" => "bottom-top",
		];

		return (is_null( $var )) ? $array : $array[ $var ];
	}

	public function aosEasingArray( $var = null ) {
		$array = [
			"" => __( "Select easing type", 'wp-builder-locale' ),
			"linear" => "linear",
			"ease" => "ease",
			"ease-in" => "ease-in",
			"ease-out" => "ease-out",
			"ease-in-out" => "ease-in-out",
			"ease-in-back" => "ease-in-back",
			"ease-out-back" => "ease-out-back",
			"ease-in-out-back" => "ease-in-out-back",
			"ease-in-sine" => "ease-in-sine",
			"ease-out-sine" => "ease-out-sine",
			"ease-in-out-sine" => "ease-in-out-sine",
			"ease-in-quad" => "ease-in-quad",
			"ease-out-quad" => "ease-out-quad",
			"ease-in-out-quad" => "ease-in-out-quad",
			"ease-in-cubic" => "ease-in-cubic",
			"ease-out-cubic" => "ease-out-cubic",
			"ease-in-out-cubic" => "ease-in-out-cubic",
			"ease-in-quart" => "ease-in-quart",
			"ease-out-quart" => "ease-out-quart",
			"ease-in-out-quart" => "ease-in-out-quart",
		];

		return (is_null( $var )) ? $array : $array[ $var ];
	}

	public function aosData( $data = [], $offset = 0, $anchor = '', $anchor_placement = '', $repeat = true ) {
		if( isset( $data['animation'] ) ) {
			$data['aos'] = [
				'animation' => $data['animation'],
				'duration' => $data['duration'],
				'easing' => $data['easing'],
				'delay' => $data['delay'],
			];
		}

		if( '' == $this->getArrayValue( 'aos', $data, '' ) ) {
			return '';
		}

		$return = [];

		$animation = $data['aos']['animation'];
		$duration = $data['aos']['duration'];
		$easing = $data['aos']['easing'];
		$delay = $data['aos']['delay'];

		if( $animation === '' ) {
			return '';
		}

		$return['data-aos'] = $animation;

		if( $duration > 0 ) {
			$return['data-aos-duration'] = $duration;
		}
		if( $offset > 0 ) {
			$return['data-aos-offset'] = $offset;
		}
		if( $easing !== '' ) {
			$return['data-aos-easing'] = $easing;
		}
		if( $delay > 0 ) {
			$return['data-aos-delay'] = $delay;
		}
		if( $anchor !== '' ) {
			$return['data-aos-anchor'] = $anchor;
		}
		if( $anchor_placement !== '' ) {
			$return['data-aos-anchor-placement'] = $anchor_placement;
		}
		if( ! $repeat ) {
			//$return['data-aos-once'] = $repeat;
		}

		$string = '';
		foreach( $return as $key => $value ) {
			$string .= $key . '="' . $value . '" ';
		}

		return $string;
	}

	public function dropdownHtml( $array = [], $selected_value ) {
		$html = '';
		if( is_array( $array ) && ! empty( $array ) ) {
			foreach( $array as $key => $value ) {
				if( gettype( $selected_value ) === 'string' ) {
					$selected = ($key === $selected_value) ? 'selected' : '';
				}
				if( gettype( $selected_value ) === 'array' ) {
					$selected = (in_array( $key, $selected_value )) ? 'selected' : '';
				}
				$html .= '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
			}
		}

		return $html;
	}

	public function postDefault( $key, $default = '' ) {
		return (isset( $_POST[ $key ] )) ? $_POST[ $key ] : $default;
	}

	public function getConditionalFieldOption( $var, $array = [], $selected_value ) {
		$return = [];
		$var = '_block_settings_' . $var;
		if( empty( $array ) ) {
			$saved_options = $this->saved_options;
			if( isset( $saved_options[ $var ] ) ) {
				$selected_value = (isset( $saved_options[ $var ][ $selected_value ] )) ? $saved_options[ $var ][ $selected_value ] : '';
				$return = (isset( $saved_options[ $var ]['items'][ $selected_value ] )) ? $saved_options[ $var ]['items'][ $selected_value ] : [$selected_value => true];
			}
		}
		if( is_array( $array ) && ! empty( $array ) && isset( $array[ $var ] ) && isset( $array[ $var ]['items'][ $selected_value ] ) ) {
			$selected_value = $array[ $var ]['items'][ $selected_value ];
			$return = $selected_value;
		}

		return $return;
	}

	public function renderAdminFormField( $option ) {
		if( ! is_array( $option ) ) {
			return false;
		}
		ob_start();
		require sprintf( '%s/framework/html/form-field.php', $this->root_dir );

		return ob_get_clean();
	}

	public function compileFontStyles( $element, $array = [] ) {
		$css[] = '';
		if( is_array( $array ) && ! empty( $array ) ) {
			$css[] = $element . '{';
			foreach( $array as $key => $value ) {
				$quotes = '';
				$suffix = '';
				if( $key == 'line-height' ) {
					$suffix = 'px';
				}
				if( $key == 'font-size' ) {
					$suffix = 'px';
				}
				if( $key == 'font-family' ) {
					$quotes = '"';
				}
				$css[] = $key . ':' . $quotes . $value . $quotes . $suffix . ';';
			}
			$css[] = '}';
		}

		return implode( '', $css );
	}

	public function columnClasses( $suffix ) {
		$classes = [];
		$classes[""] = __( 'Auto', 'wp-builder-locale' );
		$classes["cj-is-hidden-{$suffix}"] = __( 'Hidden', 'wp-builder-locale' );
		for( $i = 1; $i <= 12; $i ++ ) {
			$classes["cj-is-{$i}-{$suffix}"] = $i . '/12';
		}

		return $classes;
	}

	public function renderAdminForm( $form_options = [], $form_defaults = [] ) {
		ob_start();
		require($this->root_dir . "/framework/html/admin-form.php");

		return ob_get_clean();
	}

	public function renderFrontendForm( $form_options = [], $form_defaults = [] ) {
		ob_start();
		require($this->root_dir . "/framework/html/frontend-form.php");

		return ob_get_clean();
	}

	public function renderWidgetForm( $options, $form_instance ) {
		ob_start();
		require($this->root_dir . "/framework/html/widget-form.php");

		return ob_get_clean();
	}

	public function validateFormWithGump( $post_data, $validation_rules, $options_array = [], $return = 'all|messages' ) {
		if( ! class_exists( 'cjwpbldr_gump_extension' ) ) {
			require sprintf( '%s/framework/autoload/cjwpbldr_gump_extension.php', $this->root_dir );
		}
		$gump_ext = new cjwpbldr_gump_extension();
		$new_options = [];
		if( ! empty( $options_array ) ) {
			foreach( $options_array as $key => $value ) {
				if( is_array( $value ) && array_key_exists( 'id', $value ) ) {
					$new_options[ $value['id'] ] = $value;
				}
			}
		}

		$errors = $gump_ext->validation_errors( $post_data, $validation_rules, $new_options );

		if( $errors ) {
			if( $return == 'all|messages' ) {
				return $errors;
			}
			if( $return == 'all' ) {
				return $errors;
			}
			if( $return == 'messages' ) {
				$return = [];
				foreach( $errors as $key => $value ) {
					$return[ $key ] = sprintf( __( '%s', 'wp-builder-locale' ), array_values( $value )[0]['error_msg'] );
				}

				return $return;
			}
		} else {
			return null;
		}
	}

	public function validateWithGump( $validation_rules, $post_data, $messages = [], $filters = [] ) {
		$gump = new GUMP();
		$post_data = $gump->sanitize( $post_data );
		$gump->validation_rules( $validation_rules );
		$gump->filter_rules( $filters );
		try {
			$validated_data = $gump->run( $post_data );
			if( $validated_data === false ) {
				$errors = [];
				$gump_errors = $gump->get_errors_array();
				foreach( $gump_errors as $key => $value ) {
					$key = strtolower( str_replace( ' ', '_', $key ) );
					$errors[ $key ] = $value;
				}
				if( ! empty( $messages ) ) {
					foreach( $messages as $m_key => $m_value ) {
						if( isset( $errors[ $m_key ] ) ) {
							$errors[ $m_key ] = $m_value;
						}
					}
				}

				return $errors;
			} else {
				return [];
			}
		} catch( Exception $e ) {
			return $e->getMessage();
		}
	}

	public function prepareValidationRules( $array ) {
		$boolean_fields = [
			'required',
			'valid_email',
			'valid_name',
			'valid_url',
			'valid_cc',
			'iban',
			'street_address',
			'phone_number',
			'alpha',
			'alpha_numeric',
			'alpha_dash',
			'alpha_space',
			'numeric',
			'integer',
			'float',
			'valid_ip',
			'valid_ipv4',
			'valid_ipv6',
		];
		$input_fields = [
			'max_len',
			'min_len',
			'exact_len',
			'contains',
		];
		$validation_rules = [];
		foreach( $array as $key => $value ) {
			if( in_array( $key, $boolean_fields ) && $value == 'yes' ) {
				$validation_rules[] = $key;
			}
			if( in_array( $key, $input_fields ) && $value != '' ) {
				$validation_rules[] = $key . ',' . $value;
			}
		}

		return implode( '|', $validation_rules );
	}

	public function getOptionPageUrl( $var, $query_string = false ) {
		if( $this->saved_options[ $var ] == '' ) {
			return '';
		}
		$page_url = get_permalink( $this->saved_options[ $var ] );

		return ( ! $query_string) ? $page_url : $this->queryString( $page_url );
	}

	public function pageUrlsFromOption( $option ) {
		if( $this->saved_options[ $option ] != '' ) :
			$page_urls = '<a target="_blank" href="' . $this->site_url . '?p=' . $this->saved_options[ $option ] . '">' . esc_attr__( 'View', $this->text_domain ) . '</a>';
			$page_urls .= ' | ';
			$page_urls .= '<a target="_blank" href="' . admin_url( 'post.php' ) . '?post=' . $this->saved_options[ $option ] . '&action=edit">' . esc_attr__( 'Edit', $this->text_domain ) . '</a>';
		else :
			$page_urls = '';
		endif;

		return $page_urls;
	}

	public function isValidEmail( $email ) {
		return (filter_var( $email, FILTER_VALIDATE_EMAIL )) ? true : false;
	}

	public function isStrongPassword( $string ) {
		if( $string != '' ) {
			$uppercase = preg_match( '@[A-Z]@', $string );
			$lowercase = preg_match( '@[a-z]@', $string );
			$number = preg_match( '@[0-9]@', $string );
			$special_char = preg_match( '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $string );
			if( $string != '' && $uppercase && $lowercase && $number && $special_char ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function sanitizeFormOption( $option = [], $saved_value = '' ) {
		global $wp_registered_sidebars, $current_user;
		$current_user = wp_get_current_user();
		$return = [];

		$id = $option['id'];
		$type = $option['type'];
		$label = (isset( $option['label'] )) ? $option['label'] : '';
		if( $label == '' ) {
			$label = (isset( $option['name'] )) ? $option['name'] : '';
		}
		$info = (isset( $option['info'] )) ? $option['info'] : '';
		if( $info == '' ) {
			$info = (isset( $option['description'] )) ? $option['description'] : '';
		}
		$prefix = (isset( $option['prefix'] )) ? $option['prefix'] : '';
		if( $prefix == '' && isset( $option['before_field'] ) ) {
			$prefix = $option['before_field'];
		}
		$suffix = (isset( $option['suffix'] )) ? $option['suffix'] : '';
		if( $suffix == '' && isset( $option['after_field'] ) ) {
			$suffix = $option['after_field'];
		}
		$params = [];
		if( isset( $option['attributes'] ) && is_array( $option['attributes'] ) && ! empty( $option['attributes'] ) ) {
			$params = $option['attributes'];
		}
		if( isset( $option['params'] ) && is_array( $option['params'] ) && ! empty( $option['params'] ) ) {
			$params = $option['params'];
		}
		$taxonomy_args = [];
		if( isset( $option['taxonomy_args'] ) && is_array( $option['taxonomy_args'] ) && ! empty( $option['taxonomy_args'] ) ) {
			$taxonomy_args = $option['taxonomy_args'];
		}

		$options = (isset( $option['options'] )) ? $option['options'] : [];
		$repeatable = (isset( $option['repeatable'] )) ? $option['repeatable'] : false;
		$default = $saved_value;

		// type
		if( in_array( $type, ['title', 'heading', 'sub-heading', 'info'] ) ) {
			$type = 'sub-heading';
			$repeatable = 'false';
			$default = $label;
		}
		if( in_array( $type, ['text', 'text_small', 'text_medium', 'text-readonly', 'text-disabled'] ) ) {
			$type = 'text';
			if( $option['type'] == 'text-readonly' ) {
				$params['readonly'] = 'readonly';
			}
			if( $option['type'] == 'text-disabled' ) {
				$params['disabled'] = 'disabled';
			}
		}
		if( in_array( $type, ['text_email', 'text-email', 'email', 'email-address'] ) ) {
			$type = 'text';
			$params['data_field_type'] = 'email';
		}
		if( in_array( $type, ['text_url', 'text-url', 'url'] ) ) {
			$type = 'text';
			$params['data_field_type'] = 'url';
		}
		if( $type == 'password' ) {
			$type = 'text';
			$params['data_field_type'] = 'password';
		}
		if( $type == 'text_money' || $type == 'number' ) {
			$type = 'text';
			$params['data_field_type'] = 'number';
		}
		if( $type == 'text_time' || $type == 'time' ) {
			$type = 'text';
			$params['data_field_type'] = 'text';
			$params['data_field_type'] = 'time';
		}
		if( $type == 'text_date' || $type == 'date' ) {
			$type = 'text';
			$params['data_field_type'] = 'text';
			$params['data_field_type'] = 'date';
		}
		if( $type == 'text_datetime' || $type == 'date-time' || $type == 'datetime' ) {
			$type = 'text';
			$params['data_field_type'] = 'text';
			$params['data_field_type'] = 'datetime-local';
			if( empty( $saved_value ) && $repeatable ) {
				$default = [''];
			}
		}
		if( $type == 'date-range' ) {
			$type = 'date-range';
			$params['from']['type'] = 'date';
			$params['to']['type'] = 'date';
			if( ! is_array( $default ) ) {
				$default = [
					'from' => '',
					'to' => '',
				];
				if( $repeatable ) {
					$default = [];
					$default[] = [
						'from' => '',
						'to' => '',
					];
				}
			}
		}

		if( $type == 'datetime-range' ) {
			$type = 'datetime-range';
			$params['from']['type'] = 'datetime-local';
			$params['to']['type'] = 'datetime-local';
			if( ! is_array( $default ) ) {
				$default = [
					'from' => '',
					'to' => '',
				];
				if( $repeatable ) {
					$default = [];
					$default[] = [
						'from' => '',
						'to' => '',
					];
				}
			}
		}

		if( $type == 'weekdays' ) {
			$type = 'select';
			$params['data_field_type'] = 'select';
			if( $repeatable ) {
				$params['multiple'] = 'multiple';
			}
			$options = [
				'Sunday' => __( 'Sunday', 'wp-builder-locale' ),
				'Monday' => __( 'Monday', 'wp-builder-locale' ),
				'Tuesday' => __( 'Tuesday', 'wp-builder-locale' ),
				'Wednesday' => __( 'Wednesday', 'wp-builder-locale' ),
				'Thursday' => __( 'Thursday', 'wp-builder-locale' ),
				'Friday' => __( 'Friday', 'wp-builder-locale' ),
				'Saturday' => __( 'Saturday', 'wp-builder-locale' ),
			];
		}

		if( $type == 'font-family' ) {
			$type = 'select';
			$params['data_field_type'] = 'select';
			$repeatable = false;
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

			if( is_array( $default_fonts ) && is_array( $google_fonts ) ) {
				$options = array_merge( $default_fonts, $google_fonts );
			}
		}

		if( $type == 'time-range' ) {
			$type = 'time-range';
			$params['from']['type'] = 'time';
			$params['to']['type'] = 'time';
			if( ! is_array( $default ) ) {
				$default = [];
			}
		}

		if( $type == 'datetime-range' ) {
			$type = 'datetime-range';
			$params['from']['type'] = 'datetime-local';
			$params['to']['type'] = 'datetime-local';
			if( ! is_array( $default ) ) {
				$default = [];
			}
			if( ! $repeatable ) {
				if( isset( $default['from'] ) ) {
					$default['from'] = date( "Y-m-d\TH:i:s", strtotime( $default['from'] ) );
				}
				if( isset( $default['to'] ) ) {
					$default['to'] = date( "Y-m-d\TH:i:s", strtotime( $default['to'] ) );
				}
			}
			if( $repeatable && ! empty( $default ) ) {
				foreach( $default as $key => $value ) {
					if( isset( $default[ $key ]['from'] ) ) {
						$default[ $key ]['from'] = date( "Y-m-d\TH:i:s", strtotime( $default[ $key ]['from'] ) );
					}
					if( isset( $default[ $key ]['to'] ) ) {
						$default[ $key ]['to'] = date( "Y-m-d\TH:i:s", strtotime( $default[ $key ]['to'] ) );
					}
				}
			}
		}

		if( $type == 'color' ) {
			$type = 'color';
			$params['data_field_type'] = 'text';
			$prefix = '<i class="fa fa-tint"></i>';
		}

		if( $type == 'hidden' ) {
			$type = 'text';
			$params['data_field_type'] = 'hidden';
		}
		if( $type == 'textarea' ) {
			$params['rows'] = 4;
		}
		if( $type == 'textarea_small' ) {
			$type = 'textarea';
			$params['rows'] = 2;
		}
		if( $type == 'textarea_large' ) {
			$type = 'textarea';
			$params['rows'] = 6;
		}
		if( in_array( $type, ['textarea_code', 'code-html', 'code-css', 'code-js'] ) ) {
			$type = 'code';
			$params['data_field_type'] = $option['type'];
			$params['rows'] = 4;
			$params['class'] = 'cj-text-code cj-textarea-code cj-text-area-code-' . $option['type'];
			$repeatable = false;
		}

		if( in_array( $type, ['font', 'font-styles'] ) ) {
			$type = 'font';
			$params['data_field_type'] = 'font';

			$default = [
				'font-family' => (isset( $saved_value['font-family'] )) ? $saved_value['font-family'] : $option['default']['font-family'],
				'font-size' => (isset( $saved_value['font-size'] )) ? $saved_value['font-size'] : $option['default']['font-size'],
				'line-height' => (isset( $saved_value['line-height'] )) ? $saved_value['line-height'] : $option['default']['line-height'],
				'font-weight' => (isset( $saved_value['font-weight'] )) ? $saved_value['font-weight'] : $option['default']['font-weight'],
				'color' => (isset( $saved_value['color'] )) ? $saved_value['color'] : $option['default']['color'],
				'font-style' => (isset( $saved_value['font-style'] )) ? $saved_value['font-style'] : $option['default']['font-style'],
				'text-align' => (isset( $saved_value['text-align'] )) ? $saved_value['text-align'] : $option['default']['text-align'],
			];

			$options = [];
			$options['font-family'] = [
				'body-family' => __( 'Body', 'wp-builder-locale' ),
				'headings-family' => __( 'Headings', 'wp-builder-locale' ),
				'code-family' => __( 'Code', 'wp-builder-locale' ),
			];
			$options['font-weight'] = $this->arrays( 'font-weight' );
			$options['font-style'] = $this->arrays( 'font-style' );
			$options['text-align'] = $this->arrays( 'text-align' );
			$options['font-size'] = 16;
			$options['line-height'] = 24;
			$options['color'] = '#000000';
			$repeatable = false;
		}

		if( $type == 'wysiwyg' || $type == 'wysiwyg-tiny' ) {
			$type = 'wysiwyg';
		}

		if( in_array( $type, $this->form_field_types['select'] ) ) {
			//$repeatable = false;
			if( in_array( $type, $this->form_field_types['select_multiple'] ) ) {
				$repeatable = true;
			}
			$type = 'select';
			$params['data_field_type'] = $option['type'];
			if( $repeatable ) {
				$params['multiple'] = true;
			}

			if( $params['data_field_type'] == 'countries' || $params['data_field_type'] == 'country' ) {
				$options = $this->arrays( 'countries' );
			}

			if( in_array( $params['data_field_type'], ['nav-menu', 'nav-menus', 'nav_menu', 'nav_menus'] ) ) {
				$options = $this->getTaxonomyArray( 'nav_menu' );
			}

			if( in_array( $params['data_field_type'], ['sidebar', 'sidebars'] ) ) {
				$sidebars = $wp_registered_sidebars;
				$options = [];
				if( ! empty( $sidebars ) ) {
					foreach( $sidebars as $sidebar_id => $sidebar ) {
						$options[ $sidebar_id ] = $sidebar['name'];
					}
				}
			}
		}

		if( $option['type'] == 'user' || $option['type'] == 'users' ) {
			$params['key_value'] = (isset( $params['key_value'] ) && $params['key_value'] !== '') ? $params['key_value'] : 'ID';
			$params['name_value'] = (isset( $params['name_value'] ) && $params['name_value'] !== '') ? $params['name_value'] : 'user_login';
			$options = $this->usersArray( $params['key_value'], $params['name_value'] );
		}

		if( $option['type'] == 'role' || $option['type'] == 'roles' ) {
			$options = $this->userRolesArray();
		}

		if( $option['type'] == 'page' || $option['type'] == 'pages' ) {
			$options = $this->postsArray( 'ID', 'post_title', 'page' );
		}
		if( $option['type'] == 'post' || $option['type'] == 'posts' ) {
			$options = $this->postsArray( 'ID', 'post_title', 'post' );
		}
		if( $option['type'] == 'custom-post' || $option['type'] == 'custom-posts' ) {
			if( ! isset( $params['post_type'] ) && current_user_can( 'manage_options' ) ) {
				$options = ['not-configured' => __( 'You must specify "post_type" in option params', 'wp-builder-locale' )];
			} else {
				$options = [];
			}
			if( isset( $params['post_type'] ) ) {
				$options = $this->postsArray( 'ID', 'post_title', $params['post_type'] );
			}
		}

		if( $option['type'] == 'tag' || $option['type'] == 'tags' ) {
			$options = $this->termsArray( 'slug', 'name', 'post_tag', $taxonomy_args );
		}

		if( $option['type'] == 'category' || $option['type'] == 'categories' ) {
			$options = $this->termsArray( 'slug', 'name', 'category', $taxonomy_args );
		}

		if( $option['type'] == 'taxonomy' || $option['type'] == 'taxonomies' ) {
			if( ! isset( $params['taxonomy'] ) ) {
				if( current_user_can( 'manage_options' ) ) {
					$options = ['' => __( 'You must specify "taxonomy" in option params', 'wp-builder-locale' )];
				} else {
					$options = [];
				}
			}
			if( isset( $params['taxonomy'] ) ) {
				$options = $this->termsArray( 'slug', 'name', $params['taxonomy'], $taxonomy_args );
			}
		}

		if( $option['type'] == 'taxonomy-term' || $option['type'] == 'taxonomy-terms' ) {
			$params['tax_args'] = (isset( $params['tax_args'] ) && ! empty( $params['tax_args'] )) ? $params['tax_args'] : ['public' => true];
			$params['term_args'] = (isset( $params['term_args'] ) && ! empty( $params['term_args'] )) ? $params['term_args'] : ['hide_empty' => true];
			$options = $this->taxonomyTermsArray( $params['tax_args'], $params['term_args'] );
		}

		if( $option['type'] == 'post-type' || $option['type'] == 'post-types' ) {
			$options = get_post_types( [
				'public' => true
			] );
		}

		if( $type == 'checkbox' || $type == 'checkbox-inline' || $type == 'checkbox_inline' ) {
			$type = 'checkbox';
			if( $option['type'] == 'checkbox-inline' || $option['type'] == 'checkbox-inline' ) {
				$params['data_field_type'] = 'checkbox-inline';
			} else {
				$params['data_field_type'] = 'checkbox';
			}
			if( ! is_array( $default ) ) {
				$default = [];
			}
		}

		if( $type == 'radio' || $type == 'radio-inline' || $type == 'radio_inline' ) {
			$type = 'radio';
			if( $option['type'] == 'radio-inline' || $option['type'] == 'radio_inline' ) {
				$params['data_field_type'] = 'radio-inline';
			} else {
				$params['data_field_type'] = 'radio';
			}
			if( is_array( $default ) ) {
				$default = '';
			}
		}

		if( in_array( $option['type'], ['file', 'files', 'upload', 'uploads', 'vue-file-upload', 'user-avatar', 'user_avatar', 'avatar'] ) ) {
			$type = 'file';
			$params['data_field_type'] = $option['type'];
			$allowed_file_types = [];
			$wp_allowed_files = get_allowed_mime_types();
			foreach( $wp_allowed_files as $key => $value ) {
				$allowed_file_types[] = $value;
			}
			$url = trailingslashit( rest_url( 'cjwpbldr' ) ) . 'core/file/upload';
			$max_file_size = (int) str_replace( 'MB', '', $this->upload_max_file_size );
			$max_files = 1;
			if( in_array( $option['type'], ['files', 'uploads'] ) ) {
				$max_files = 100;
			}
			$file_options = [
				'welcome_message' => __( 'Click or Drag and Drop files here to upload.', 'wp-builder-locale' ),
				'method' => 'post',
				'url' => $url,
				'parallelUploads' => 3,
				'maxFilesize' => [
					'limit' => $max_file_size,
					'message' => sprintf( __( 'File size must be less than %s MB.', 'wp-builder-locale' ), $max_file_size ),
				],
				'maxFiles' => [
					'limit' => $max_files,
					'message' => sprintf( __( 'You are not allowed to upload more than %s files at once.', 'wp-builder-locale' ), $max_files ),
				],
				'acceptedFiles' => [
					'extensions' => $allowed_file_types,
					'message' => __( 'You are uploading an invalid file.', 'wp-builder-locale' )
				],
				'uploadMultiple' => false,
				'label_processing' => __( 'Processing', 'wp-builder-locale' ),
				'display_uploaded_files' => true,
			];
			if( isset( $option['options'] ) && is_array( $option['options'] ) && ! empty( $option['options'] ) ) {
				foreach( $option['options'] as $key => $value ) {
					$file_options[ $key ] = $value;
				}
			}
			$file_options['paramName'] = 'file';
			$file_options['uploadMultiple'] = false;
			$file_options['headers'] = [
				'X-Authorization' => $this->getCookie( 'cjwpbldr_token' )
			];

			// option overrides for user avatar uses UserAvatar.vue
			if( in_array( $option['type'], ['user-avatar', 'user_avatar', 'avatar'] ) ) {
				$type = 'avatar';
				$params['data_field_type'] = $option['type'];
				$file_options['acceptedFiles'] = [
					'extensions' => ['image/*'],
					'message' => __( 'You must upload a valid image file.', 'wp-builder-locale' )
				];
				$file_options['maxFiles'] = [
					'limit' => 1,
					'message' => sprintf( __( 'You are not allowed to upload more than %s files at once.', 'wp-builder-locale' ), '1' ),
				];
				$file_options['welcome_message'] = __( 'Click or Drag and Drop your profile image.', 'wp-builder-locale' );
				$file_options['upload_dir'] = trailingslashit( $this->upload_path ) . 'avatars';
				$user_avatar = $this->default_avatar_url;
				if( is_user_logged_in() ) {
					$user_info = $this->userInfo( $current_user->ID, ['user_avatar', 'gravatar'] );
					$user_avatar = $user_info['user_avatar'];
				}
				$file_options['user_avatar'] = $user_avatar;
				$file_options['default_avatar'] = $this->default_avatar_url;

				// set default
				$repeatable = false;
				$default = $user_avatar;
			}

			$options = $file_options;
		}

		if( in_array( $option['type'], ['file-raw', 'file_raw'] ) ) {
			$type = 'file-raw';
			$params['data_field_type'] = 'file-raw';
			$options['label_choose_file'] = __( 'Choose a file', 'wp-builder-locale' );
			$repeatable = false;
		}

		if( $option['type'] == 'address' ) {
			$type = 'address';
			$params['data_field_type'] = $option['type'];
			$params = [
				'address_line_1' => [
					'placeholder' => __( 'Address line 1', 'wp-builder-locale' ),
					'required' => true,
				],
				'address_line_2' => [
					'placeholder' => __( 'Address line 2', 'wp-builder-locale' ),
				],
				'city' => [
					'placeholder' => __( 'city', 'wp-builder-locale' ),
					'required' => true,
				],
				'state' => [
					'placeholder' => __( 'State / Province / Region', 'wp-builder-locale' ),
					'required' => true,
				],
				'country' => [
					'placeholder' => __( 'Country', 'wp-builder-locale' ),
					'required' => true,
				],
				'zipcode' => [
					'placeholder' => __( 'Postal / Zipcode', 'wp-builder-locale' ),
					'required' => true,
				],
			];
			$options = [
				'countries' => $this->arrays( 'countries' )
			];
			$default = [
				'address_line_1' => (isset( $saved_value['address_line_1'] ) && 'address_line_1' != '') ? $saved_value['address_line_1'] : '',
				'address_line_2' => (isset( $saved_value['address_line_2'] ) && 'address_line_2' != '') ? $saved_value['address_line_2'] : '',
				'city' => (isset( $saved_value['city'] ) && 'city' != '') ? $saved_value['city'] : '',
				'state' => (isset( $saved_value['state'] ) && 'state' != '') ? $saved_value['state'] : '',
				'country' => (isset( $saved_value['country'] ) && 'country' != '') ? $saved_value['country'] : '',
				'zipcode' => (isset( $saved_value['zipcode'] ) && 'zipcode' != '') ? $saved_value['zipcode'] : '',
			];
			//$repeatable = false;
		}

		if( $option['type'] == 'icon' ) {
			$option['options'] = [];
			$option['default'] = $saved_value;
		}

		// global config
		if( $type == 'select' ) {
			if( isset( $params['required'] ) ) {
				unset( $params['required'] );
			}
			if( ! is_array( $options ) && $repeatable ) {
				$options = [];
			}
		}

		if( $repeatable && ! is_array( $default ) ) {
			$default = [''];
		}

		// params defaults
		$params['field_container_class'] = (isset( $params['field_container_class'] )) ? $params['field_container_class'] : '';
		$params['before_field'] = (isset( $params['before_field'] )) ? $params['before_field'] : '';
		$params['after_field'] = (isset( $params['after_field'] )) ? $params['after_field'] : '';
		if( isset( $option['attributes'] ) && ! empty( $option['attributes'] ) ) {
			$params = array_merge( $params, $option['attributes'] );
		}
		if( ! isset( $params['data_field_type'] ) ) {
			$params['data_field_type'] = '';
		}

		if( $option['type'] == 'group' ) {
			$option['group_fields_array'] = [];
			$type = 'group';
			$params['data_field_type'] = 'group';
			$options['remove_button'] = '<i class="fa fa-trash-alt"></i>';

			if( ! is_array( $default ) ) {
				$default = [];
			}
			if( empty( $default ) ) {
				$default_values = [];
				foreach( $option['group_fields'] as $key => $group_field ) {
					$default_values[ $key ][ $group_field['id'] ] = $group_field['default'];
				}
				$default[] = $default_values;
			}

			foreach( $default as $index => $group_items ) {
				foreach( $option['group_fields'] as $parent_key => $parent_field ) {

					$saved_parent_value = (isset( $saved_value[ $index ][ $parent_field['id'] ] )) ? $saved_value[ $index ][ $parent_field['id'] ] : $parent_field['default'];
					$option['group_fields_array'][ $index ][ $parent_field['id'] ] = $this->sanitizeFormOption( $parent_field, $saved_parent_value );

					if( isset( $parent_field['repeatable'] ) && ! $parent_field['repeatable'] ) {

					}
					if( $parent_field['repeatable'] && is_array( $parent_field['default'] ) ) {
						foreach( $parent_field['default'] as $repeat_key => $repeat_value ) {
							$child_option = $parent_field;
							$child_option['repeatable'] = true;
							$saved_child_value = (isset( $saved_value[ $index ][ $child_option['id'] ] )) ? $saved_value[ $index ][ $child_option['id'] ] : $child_option['default'];
							$child_option = $this->sanitizeFormOption( $child_option, $saved_child_value );
							$option['group_fields_array'][ $index ][ $child_option['id'] ] = $child_option;
						}
					}
				}
			}
		}

		// finally return the results
		$return = $option;
		$return['id'] = $id;
		$return['type'] = $type;
		$return['label'] = $label;
		$return['info'] = $info;
		$return['prefix'] = $prefix;
		$return['prefix'] = $suffix;
		$return['params'] = $params;
		$return['default'] = $default;
		$return['options'] = $options;
		$return['repeatable'] = $repeatable;
		if( $repeatable ) {
			$return['add_row_text'] = (isset( $option['add_row_text'] )) ? $option['add_row_text'] : __( 'Add Another', 'wp-builder-locale' );
		}

		// set option name
		if( $type != 'group' && ! isset( $return['params']['name'] ) ) {
			$return['params']['type'] = $option['type'];
			if( $return['repeatable'] ) {
				$return['params']['name'] = $option['id'] . '[]';
			} else {
				$return['params']['name'] = $option['id'];
			}
		}

		return $return;
	}

	public function jsonFormOptions( $options = [], $saved_values = [] ) {
		if( isset( $options ) ) {
			if( empty( $options ) ) {
				return json_encode( [] );
			}
			foreach( $options as $key => $option ) {
				$placeholder = (isset( $option['repeatable'] ) && $option['repeatable']) ? [] : '';
				$saved_value = (isset( $saved_values[ $option['id'] ] )) ? $saved_values[ $option['id'] ] : $placeholder;
				$options[ $key ] = $option;
			}
		}

		return $this->jsonEncode( $options );
	}

}