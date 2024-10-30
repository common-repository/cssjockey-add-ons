<?php

/**
 * Trait cjwpbldr_helpers_strings
 */
trait cjwpbldr_helpers_strings {

	/**
	 * @param      $datetime
	 * @param bool $full
	 *
	 * @return string
	 * @throws Exception
	 */
	public function timeInAgo( $datetime, $full = false ) {
		$now = new DateTime;
		$ago = new DateTime( $datetime );
		$diff = $now->diff( $ago );

		$diff->w = floor( $diff->d / 7 );
		$diff->d -= $diff->w * 7;

		$string = [
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		];
		foreach( $string as $k => &$v ) {
			if( $diff->$k ) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset( $string[ $k ] );
			}
		}

		if( ! $full ) {
			$string = array_slice( $string, 0, 1 );
		}

		return $string ? implode( ', ', $string ) . ' ago' : 'just now';
	}

	/**
	 * @param null $string
	 *
	 * @return string
	 */
	public function queryString( $string = null ) {
		if( ! is_null( $string ) ) {
			if( strpos( $string, '?' ) > 0 ) {
				return $string . '&';
			} else {
				return $string . '?';
			}
		}
	}

	/**
	 * @param        $string
	 * @param string $action
	 *
	 * @return bool|false|string
	 */
	public function secureString( $string, $action = 'e' ) {
		// you may change these values to your own
		$secret_key = md5( $this->site_url );
		$secret_iv = sha1( md5( $this->site_url ) );

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} elseif( $action == 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}

		return $output;
	}

	/**
	 * @param null   $prefix
	 * @param string $case
	 *
	 * @return string
	 */
	public function uniqueString( $prefix = null, $case = 'upper' ) {
		$unique_string = sprintf( "%04s%03s%s", base_convert( mt_rand( 0, pow( 36, 4 ) - 1 ), 10, 36 ), base_convert( mt_rand( 0, pow( 36, 3 ) - 1 ), 10, 36 ), substr( sha1( md5( strtotime( date( 'Y-m-d H:i:s' ) ) ) ), 7, 3 ) );
		$unique_string = ($case == 'upper') ? strtoupper( $unique_string ) : $unique_string;
		$unique_string = ($case == 'lower') ? strtolower( $unique_string ) : $unique_string;

		return $prefix . $unique_string;
	}

	/**
	 * @param $string
	 * @param $start
	 * @param $end
	 *
	 * @return false|string
	 */
	public function getStringBetween( $string, $start, $end ) {
		$string = ' ' . $string;
		$ini = strpos( $string, $start );
		if( $ini == 0 ) {
			return '';
		}
		$ini += strlen( $start );
		$len = strpos( $string, $end, $ini ) - $ini;

		return substr( $string, $ini, $len );
	}

	/**
	 * @param $string
	 *
	 * @return string|string[]|null
	 */
	public function cleanString( $string ) {
		$string = str_replace( ' ', '-', $string ); // Replaces all spaces with hyphens.
		$string = str_replace( ['[', ']', '(', ')', '{', '}'], '-', $string ); // Replaces all spaces with hyphens.
		$string = preg_replace( '/[^A-Za-z0-9\-]/', '-', $string ); // Removes special chars.

		return preg_replace( '/-+/', '-', $string ); // Replaces multiple hyphens with single one.
	}

	/**
	 * @param      $html
	 * @param null $cut
	 *
	 * @return string|string[]|null
	 */
	public function html2text( $html, $cut = null ) {
		$html = strip_tags( $html );
		$html = preg_replace( "/&#?[a-z0-9]{2,8};/i", "", $html );
		$html = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );
		if( ! is_null( $cut ) ) {
			$html = $this->trimChars( $html, $cut );
		}

		return $html;
	}

	/**
	 * @param        $str
	 * @param int    $cut
	 * @param string $after_trim
	 *
	 * @return string
	 */
	public function trimChars( $str, $cut = 200, $after_trim = 'Ã¢â‚¬Â¦' ) {
		$str_length = strlen( $str );
		if( $str_length > $cut ) {
			return substr( $str, 0, $cut ) . $after_trim;
		} else {
			return $str;
		}
	}

	/**
	 * @param        $str
	 * @param int    $cut
	 * @param string $after_trim
	 *
	 * @return string
	 */
	public function trimWords( $str, $cut = 10, $after_trim = 'Ã¢â‚¬Â¦' ) {
		$str_length = str_word_count( $str );
		$return = [];
		if( $str_length > $cut ) {
			foreach( str_word_count( $str, 1 ) as $key => $word ) {
				if( $key <= $cut - 1 ) {
					$return[] = $word;
				}
			}

			return implode( ' ', $return ) . $after_trim;
		} else {
			return $str;
		}
	}

	/**
	 * @param null   $amount
	 * @param string $symbol
	 * @param string $align
	 * @param int    $decimal
	 *
	 * @return string
	 */
	public function formatCurrency( $amount = null, $symbol = '$', $align = 'left', $decimal = 2 ) {
		$amount = number_format( $amount, $decimal );
		$return = ($align == 'left') ? $symbol . '' . $amount : $amount . ' ' . $symbol;

		return $return;
	}

	/**
	 * @param       $page_link
	 * @param array $page_args
	 *
	 * @return string
	 */
	public function generateSafeLink( $page_link, $page_args = [] ) {
		$url = '';
		if( ! empty( $page_args ) ) {
			$query_args = add_query_arg( $page_args, $page_link );
			$url = esc_url( $query_args );
		} else {
			$url = esc_url( $page_link );
		}

		return $url;
	}

	/**
	 * @param      $url
	 * @param null $return
	 *
	 * @return mixed
	 */
	public function parseDomain( $url, $return = null ) {
		// possible values host, path, scheme
		$parse = parse_url( $url );

		return ( ! is_null( $return )) ? $parse[ $return ] : $parse;
	}

	/**
	 * @param $url
	 * @param $key
	 *
	 * @return false|string|string[]|null
	 */
	public function removeQueryStringVar( $url, $key ) {
		$url = preg_replace( '/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&' );
		$url = substr( $url, 0, - 1 );

		return $url;
	}

	/**
	 * @param $str
	 * @param $needle_start
	 * @param $needle_end
	 * @param $replacement
	 *
	 * @return mixed
	 */
	public function replaceBetween( $str, $needle_start, $needle_end, $replacement ) {
		$pos = strpos( $str, $needle_start );
		$start = $pos === false ? 0 : $pos + strlen( $needle_start );

		$pos = strpos( $str, $needle_end, $start );
		$end = $start === false ? strlen( $str ) : $pos;

		return substr_replace( $str, $replacement, $start, $end - $start );
	}

	/**
	 * @param $string
	 *
	 * @return bool
	 */
	public function isJson( $string ) {
		json_decode( $string );

		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * @param $str
	 *
	 * @return array|array[]|false|string[]
	 */
	public function splitUpperCaseLetters( $str ) {
		$array = preg_split( '/(?=[A-Z])/', $str );

		return array_filter( $array );
	}

	/**
	 * @param int $even_number
	 *
	 * @return string
	 */
	public function createStrongPassword( $even_number = 12 ) {
		$pass = []; //remember to declare $pass as an array
		$strings = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ`~!@#$%^*abcdefghijklmnopqrstuvwxyz1234567890';
		$alphaLength = strlen( $strings ) - 1; //put the length -1 in cache
		for( $i = 0; $i < $even_number; $i ++ ) {
			$n = rand( 0, $alphaLength );
			$pass[] = $strings[ $n ];
		}
		$password = implode( $pass );
		if( ! $this->isStrongPassword( $password ) ) {
			$password = $this->createStrongPassword( $even_number );
		}

		return $password;
	}

	/**
	 * @param       $str
	 * @param array $replace
	 *
	 * @return string
	 */
	public function ucWords( $str, $replace = ['-', '_'] ) {
		return ucwords( str_replace( $replace, ' ', $str ) );
	}

	/**
	 * @param $find
	 * @param $in_string
	 *
	 * @return bool
	 */
	public function containString( $find, $in_string ) {
		return strpos( $in_string, $find ) !== false;
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public function strToHex( $string ) {
		$hex = '';
		for( $i = 0; $i < strlen( $string ); $i ++ ) {
			$ord = ord( $string[ $i ] );
			$hexCode = dechex( $ord );
			$hex .= substr( '0' . $hexCode, - 2 );
		}

		return strToUpper( $hex );
	}

	/**
	 * @param $hex
	 *
	 * @return string
	 */
	public function hexToStr( $hex ) {
		$string = '';
		for( $i = 0; $i < strlen( $hex ) - 1; $i += 2 ) {
			$string .= chr( hexdec( $hex[ $i ] . $hex[ $i + 1 ] ) );
		}

		return $string;
	}

	/**
	 * @param $str
	 *
	 * @return string|string[]|null
	 */
	public function getNumbersFromString( $str ) {
		return preg_replace( "/[^0-9]/", "", $str );
	}

	/**
	 * @param $html
	 *
	 * @return false|string
	 */
	public function getFirstParagraph( $html ) {
		$start = strpos( $html, '<p>' );
		$end = strpos( $html, '</p>', $start );
		$paragraph = substr( $html, $start, $end - $start + 4 );
		$paragraph = html_entity_decode( strip_tags( $paragraph ) );

		return $paragraph;
	}


}