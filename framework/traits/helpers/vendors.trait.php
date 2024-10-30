<?php

use Phelium\Component\reCAPTCHA;

/**
 * Trait cjwpbldr_helpers_vendors
 */
trait cjwpbldr_helpers_vendors {
	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public function getGoogleFonts( $field = 'font-name' ) {
		$g_fonts = [];
		// delete_transient( 'cj_google_fonts' );
		//Set WP Transient Cache to cache the request for 1 week
		if( false === ($cj_google_fonts = get_transient( 'cj_google_fonts' )) ) {
			$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAcppe5laBbe858wNvCsNs6sA3FC5AFwsA';
			$cj_google_fonts = (array) json_decode( $this->wpRemoteGet( $url ) );
			set_transient( 'cj_google_fonts', $cj_google_fonts, 1 * (24 * 3600 * 7) );
		} else {
			$cj_google_fonts = get_transient( 'cj_google_fonts' );
		}
		if( ! empty( $cj_google_fonts ) ) {
			foreach( $cj_google_fonts as $cj_google_font ) {
				$cj_g_fonts = (array) $cj_google_font;
				foreach( $cj_g_fonts as $value ) {
					$val = (array) $value;
					if( array_key_exists( 'family', $val ) ) {
						$g_fonts[ $val['family'] ] = $val['family'];
					}
				}
			}
		}
		if( empty( $g_fonts ) ) {
			delete_transient( 'cj_google_fonts' );
		}
		if( ! empty( $g_fonts ) ) {
			return $g_fonts;
		}
	}

	/**
	 * @return bool|reCAPTCHA
	 * @throws Exception
	 */
	public function reCaptcha() {
		if( ! class_exists( 'Phelium\Component\reCAPTCHA' ) ) {
			return false;
		}
		$site_key = $this->getOption( 'core_google_recaptcha_site_key', '' );
		$site_secret = $this->getOption( 'core_google_recaptcha_site_secret', '' );
		$theme = $this->getOption( 'core_google_recaptcha_theme', '' );
		$language = $this->getOption( 'core_google_recaptcha_language', '' );
		if( $site_key != '' && $site_secret != '' ) {
			$recaptcha_instance = new reCAPTCHA( $site_key, $site_secret );
			if( $language != '' ) {
				$recaptcha_instance->setLanguage( $language );
			}
			if( $theme != '' ) {
				$recaptcha_instance->setTheme( $theme );
			}

			return $recaptcha_instance;
		}

		return false;
	}

	/**
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function fontAwesomeIconsArray( $var = null ) {
		ob_start();
		require $this->root_dir . '/framework/json/font-awesome-icons.json';
		$json = ob_get_clean();
		$icons_array = ['none' => __( 'None', 'wp-builder-locale' )];
		$icons = json_decode( $json, true );
		foreach( $icons as $key => $icon ) {
			$icons_array[ $key ] = str_replace( 'fa-', '', $icon );
		}

		return (is_null( $var )) ? $icons_array : $icons_array[ $var ];
	}

	/**
	 * @param        $address
	 * @param string $region
	 *
	 * @return array
	 */
	public function getAddressFromGoogle( $address, $region = '' ) {
		$region_string = ($region != '') ? '&region=' . $region : '';
		$google_key = $this->getOption( 'core_google_maps_key' );
		if( $google_key == '' ) {
			return ['error' => __( 'Google services key not specified in global settings.', 'wp-builder-locale' )];
		}
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . $region_string . '&sensor=false&key=' . $google_key;
		$json = $this->wpRemoteGet( $url );
		$data = json_decode( $json, 1 );
		if( isset( $data['error_message'] ) ) {
			return ['errors' => $data['error_message']];
		}

		return ['success' => $data];
	}

	/**
	 * @param $lat
	 * @param $lng
	 *
	 * @return array
	 */
	public function getAddressByCoords( $lat, $lng ) {
		$google_key = $this->getOption( 'core_google_maps_key' );
		if( $google_key == '' ) {
			return ['error' => __( 'Google services key not specified in global settings.', 'wp-builder-locale' )];
		}
		$return = [];
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim( $lat ) . ',' . trim( $lng ) . '&sensor=false&key=' . $google_key;
		$json = $this->wpRemoteGet( $url );
		$data = json_decode( $json );
		if( isset( $data->error_message ) ) {
			return ['error' => $data->error_message];
		}
		$status = $data->status;
		if( $status == "OK" ) {
			if( count( $data->results ) > 0 ) {
				foreach( $data->results as $key => $value ) {
					$return[ $key ]['address'] = $data->results[ $key ]->formatted_address;
					$return[ $key ]['latlng'] = $data->results[ $key ]->geometry->location;
					$return[ $key ]['street_number'] = '';
					$return[ $key ]['establishment'] = '';
					$return[ $key ]['route'] = '';
					$return[ $key ]['locality'] = '';
					$return[ $key ]['sublocality'] = '';
					$return[ $key ]['city'] = '';
					$return[ $key ]['state'] = '';
					$return[ $key ]['country'] = '';
					$return[ $key ]['zipcode'] = '';
					foreach( $data->results[ $key ]->address_components as $akey => $avalue ) {
						if( in_array( 'street_number', $avalue->types ) ) {
							$return[ $key ]['street_number'] = $avalue->long_name;
						}
						if( in_array( 'route', $avalue->types ) ) {
							$return[ $key ]['route'] = $avalue->long_name;
						}
						if( in_array( 'neighborhood', $avalue->types ) ) {
							$return[ $key ]['address_line_2'] = $avalue->long_name;
						}
						if( in_array( 'locality', $avalue->types ) ) {
							$return[ $key ]['locality'] = $avalue->long_name;
						}
						if( in_array( 'sublocality', $avalue->types ) ) {
							$return[ $key ]['sublocality'] = $avalue->long_name;
						}
						if( in_array( 'administrative_area_level_2', $avalue->types ) ) {
							$return[ $key ]['city'] = $avalue->long_name;
						}
						if( in_array( 'administrative_area_level_1', $avalue->types ) ) {
							$return[ $key ]['state'] = $avalue->long_name;
						}
						if( in_array( 'country', $avalue->types ) ) {
							$return[ $key ]['country'] = $avalue->long_name;
						}
						if( in_array( 'postal_code', $avalue->types ) ) {
							$return[ $key ]['zipcode'] = $avalue->long_name;
						}
						$return[ $key ]['address_line_1'] = $return[ $key ]['street_number'] . ' ' . $return[ $key ]['route'];
					}
				}
			}
		}

		return ['success' => $return];
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @param string $amount
	 * @param string $return
	 *
	 * @return mixed
	 */
	public function getCurrencyExchangeRate( $from = 'USD', $to = 'INR', $amount = '1.00', $return = 'rate' ) {
		$exchange_rate_data = file_get_contents( 'http://currency-api.appspot.com/api/' . $from . '/' . $to . '.json?amount=' . $amount );
		$exchange_rate_data = json_decode( $exchange_rate_data, 1 );

		return $exchange_rate_data[ $return ];
	}

	/**
	 * @param string $return
	 *
	 * @return mixed
	 */
	public function getRealIpInfo( $return = 'ip' ) {
		$ip_address = $this->wpRemoteGet( 'http://api.ipify.org/' );

		return $ip_address;
	}

	/**
	 * @param string $type
	 *
	 * @return array|mixed
	 */
	public function getIpInfo( $type = 'ip' ) {
		$return = [];
		$query = $this->wpRemoteGet( 'http://ip-api.com/php/' );

		if( $query ) {
			$result = (is_serialized( $query )) ? unserialize( $query ) : [];
			$return = [
				'country' => (isset( $result['country'] )) ? $result['country'] : '',
				'country_code' => (isset( $result['countryCode'] )) ? $result['countryCode'] : '',
				'region' => (isset( $result['region'] )) ? $result['region'] : '',
				'region_name' => (isset( $result['regionName'] )) ? $result['regionName'] : '',
				'city' => (isset( $result['city'] )) ? $result['city'] : '',
				'latitude' => (isset( $result['lat'] )) ? $result['lat'] : '',
				'longitude' => (isset( $result['lon'] )) ? $result['lon'] : '',
				'isp' => (isset( $result['isp'] )) ? $result['isp'] : '',
				'postcode' => (isset( $result['zip'] )) ? $result['zip'] : '',
				'timezone' => (isset( $result['timezone'] )) ? $result['timezone'] : '',
				'ip_address' => (isset( $result['query'] )) ? $result['query'] : '',
			];
		}

		return ($type == 'ip') ? $return['ip_address'] : $return;
	}

	/**
	 * @param $keyword
	 *
	 * @return array
	 */
	public function getKeywordSuggestionsFromGoogle( $keyword ) {
		$keywords = [];
		$data = file_get_contents( 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q=' . urlencode( $keyword ) );
		if( ($data = json_decode( $data, true )) !== null ) {
			$keywords = $data[1];
		}

		return $keywords;
	}

	/**
	 * @param $override_value
	 * @param $value
	 * @param $object_id
	 * @param $field_args
	 * @param $sanitizer_object
	 *
	 * @return mixed
	 */
	public function cmb2_sanitize_conditional_callback( $override_value, $value, $object_id, $field_args, $sanitizer_object ) {
		$selected = $field_args['render_row_cb'][0]->data_to_save['Background_Type'];
		update_post_meta( $object_id, $field_args['_id'] . '_selected', $selected );

		return $value;
	}

}