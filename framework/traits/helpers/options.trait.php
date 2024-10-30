<?php

/**
 * Trait cjwpbldr_helpers_options
 */
trait cjwpbldr_helpers_options {
	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function savedOption( $key ) {
		return ($this->isValidatedArray( $this->saved_options, true, $key )) ? $this->saved_options[ $key ] : '';
	}

	/**
	 * @param array $options_data
	 *
	 * @return mixed|void
	 */
	public function updateOptions( $options_data = [] ) {
		$saved_options = get_option( $this->options_key );
		if( is_array( $options_data ) && ! empty( $options_data ) ) {
			foreach( $options_data as $key => $value ) {
				$saved_options[ $key ] = $value;
			}
		}
		update_option( $this->options_key, $saved_options );

		return $saved_options;
	}

	/**
	 * @param        $option_key
	 * @param string $option_value
	 *
	 * @return array|mixed|void
	 */
	public function updateOption( $option_key, $option_value = '' ) {
		$saved_options = get_option( $this->options_key, [] );
		if( is_array( $saved_options ) && ! empty( $saved_options ) ) {
			foreach( $saved_options as $key => $saved_option ) {
				if( $key == $option_key ) {
					$saved_options[ $key ] = $option_value;
				}
			}
			update_option( $this->options_key, $saved_options );
		}

		return $saved_options;
	}

	/**
	 * @param null   $var
	 * @param string $default
	 *
	 * @return mixed|string|void
	 */
	public function getOption( $var = null, $default = '' ) {
		$saved_options = get_option( $this->options_key );

		if( is_null( $var ) ) {
			return $saved_options;
		}

		return (isset( $saved_options[ $var ] )) ? $saved_options[ $var ] : $default;
	}

	/**
	 * @param       $var
	 * @param array $array
	 *
	 * @return array|mixed
	 */
	public function getGroupOption( $var, $array = [] ) {
		$return = [];
		if( empty( $array ) ) {
			$saved_options = $this->saved_options;
			if( isset( $saved_options[ $var ] ) ) {
				$selected_value = (isset( $saved_options[ $var ]['selected_value'] )) ? $saved_options[ $var ]['selected_value'] : '';
				$return = (isset( $saved_options[ $var ]['items'][ $selected_value ] )) ? $saved_options[ $var ]['items'][ $selected_value ] : [$selected_value => true];
			}
		}
		if( is_array( $array ) && ! empty( $array ) && isset( $array[ $var ] ) && isset( $array[ $var ]['selected_value'] ) ) {
			$selected_value = $array[ $var ]['selected_value'];
			$return = (isset( $array[ $var ]['items'][ $selected_value ] )) ? $array[ $var ]['items'][ $selected_value ] : [$selected_value => true];
		}

		return $return;
	}
}