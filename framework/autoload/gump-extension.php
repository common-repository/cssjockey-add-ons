<?php

/**
 * class GUMP_Ext
 *
 * This is an extension of GUMP class
 * @path : framework/lib/form-validation
 */
if( ! class_exists( 'cjwpbldr_gump_extension' ) ) {
	class cjwpbldr_gump_extension {

		public $helpers;

		/**
		 * Instance of the parent class
		 *
		 * [$_parent_class_instance]
		 *
		 * @var null
		 */
		public static $parent_class_instance = null;

		/**
		 * Validation Errors
		 *
		 * [$val_errors]
		 *
		 * @var array
		 */
		public $val_errors = array();

		public static $instance;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class constructor method
		 *
		 * [__construct]
		 */
		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			self::$parent_class_instance = GUMP::get_instance();
		}

		public function validation_errors( $postData, $validation_rules, $options_array ) {
			$validation_rules = array_filter( $validation_rules );
			$this->val_errors = self::$parent_class_instance->validate( $postData, $validation_rules );
			if( empty ( $this->val_errors ) ) {
				return;
			}

			$resp = array();

			if( is_array( $this->val_errors ) ):

				foreach( $this->val_errors as $e ) {

					$label = $options_array[ $e['field'] ]['label'];

					if( '' == $label ) {
						$field_name = ucfirst( str_replace( '_', ' ', $e['field'] ) );
					} else {
						$field_name = $label;
					}

					$param = $e['param'];

					switch( $e['rule'] ) {
						case 'mismatch' :
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'There is no validation rule for %s', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_required' :
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field is required', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_valid_email':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field is not a valid email address', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_max_len':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be %s or shorter in length', 'wp-builder-locale' ), $field_name, $param )
							);
							break;
						case 'validate_min_len':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be %s or longer in length', 'wp-builder-locale' ), $field_name, $param )
							);
							break;
						case 'validate_exact_len':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be exactly %s characters in length', 'wp-builder-locale' ), $field_name, $param )
							);
							break;
						case 'validate_alpha':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain alpha characters (a-z) without any space', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_alpha_numeric':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain alpha-numeric characters', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_alpha_dash':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain alpha-numeric characters, dashes and underscores', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_alpha_space':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain only alpha-numeric characters and spaces', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_numeric':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain numeric characters', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_integer':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain a valid integer value', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_boolean':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain a true or false value', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_float':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field may only contain a float value', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_valid_url':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field is required to be a valid URL', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_url_exists':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s URL does not exist', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_valid_ip':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to contain a valid IP address', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_valid_cc':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to contain a valid credit card number', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_iban':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to contain a valid IBAN number', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_valid_name':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to contain a valid human name', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_contains':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to contain one of these values: ', 'wp-builder-locale' ), $field_name ) . implode( ', ', $param )
							);
							break;
						case 'validate_contains_list':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs contain a value from its drop down list', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_doesnt_contain_list':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field contains a value that is not accepted', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_street_address':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be a valid street address', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_date':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be a valid date', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_min_numeric':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be a numeric value, equal to, or higher than ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;
						case 'validate_max_numeric':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to be a numeric value, equal to, or lower than ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;
						case 'validate_starts':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to start with ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;
						case 'validate_extension':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field can have the following extensions ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;
						case 'validate_required_file':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field is required', 'wp-builder-locale' ), $field_name )
							);
							break;
						case 'validate_equalsfield':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field does not equal %s field', 'wp-builder-locale' ), $field_name, $param )
							);
							break;
						case 'validate_min_age':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field needs to have an age greater than or equal to ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;

						case 'validate_phone_number':
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field must be a valid phone number. ', 'wp-builder-locale' ), $field_name ) . $param
							);
							break;
						default:
							$resp[ $e['field'] ][ $e['rule'] ] = array(
								'field_unique_id' => $e['field'],
								'field_name' => $field_name,
								'error_msg' => sprintf( esc_attr__( 'The %s field is invalid', 'wp-builder-locale' ), $field_name )
							);
					}
				}

			endif;

			if( ! empty( $resp ) ) {
				return apply_filters( 'cjwpbldr_form_validation_errors', $resp, $resp );
			} else {
				return false;
			}
		}

	}//END class

	cjwpbldr_gump_extension::getInstance();
}