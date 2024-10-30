<?php

trait cjwpbldr_helpers_arrays {
	public function arrayPathSet( array &$array, array $path, $newValue ) {
		$aux = &$array;
		foreach( $path as $key ) {
			if( isset( $aux[ $key ] ) ) {
				$aux = &$aux[ $key ];
			} else {
				return false;
			}
		}
		$aux = $newValue;

		return true;
	}

	public function arrayFlatten( $array ) {
		if( ! is_array( $array ) ) {
			return [];
		}
		$return = [];
		$flat = new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) );
		foreach( $flat as $key ) {
			$return[] = $key;
		}

		return $return;
	}

	public function arrayReplace( $array, $find, $replace ) {
		if( is_array( $array ) ) {
			foreach( $array as $Key => $Val ) {
				if( is_array( $array[ $Key ] ) ) {
					$array[ $Key ] = $this->arrayReplace( $array[ $Key ], $find, $replace );
				} else {
					if( $Key == $find ) {
						$array[ $Key ] = $replace;
					}
				}
			}
		}

		return $array;
	}

	public function makeArray( $keys, $value ) {
		$var = [];
		$index = array_shift( $keys );
		if( ! isset( $keys[0] ) ) {
			$var[ $index ] = $value;
		} else {
			$var[ $index ] = $this->makeArray( $keys, $value );
		}

		return $var;
	}

	public function arrays( $type = null ) {
		$arrays['true-false'] = [
			true => esc_attr__( 'True', 'wp-builder-locale' ),
			false => esc_attr__( 'False', 'wp-builder-locale' ),
		];
		$arrays['yes-no'] = [
			'yes' => esc_attr__( 'Yes', 'wp-builder-locale' ),
			'no' => esc_attr__( 'No', 'wp-builder-locale' ),
		];
		$arrays['on-off'] = [
			'on' => esc_attr__( 'On', 'wp-builder-locale' ),
			'off' => esc_attr__( 'Off', 'wp-builder-locale' ),
		];
		$arrays['yes-no-maybe'] = [
			'yes' => esc_attr__( 'Yes', 'wp-builder-locale' ),
			'no' => esc_attr__( 'No', 'wp-builder-locale' ),
			'maybe' => esc_attr__( 'May be', 'wp-builder-locale' ),
		];
		$arrays['enable-disable'] = [
			'enable' => esc_attr__( 'Enable', 'wp-builder-locale' ),
			'disable' => esc_attr__( 'Disable', 'wp-builder-locale' ),
		];
		$arrays['active-inactive'] = [
			'active' => esc_attr__( 'Active', 'wp-builder-locale' ),
			'inactive' => esc_attr__( 'Inactive', 'wp-builder-locale' ),
		];
		$arrays['register-type'] = [
			'normal' => esc_attr__( 'Normal', 'wp-builder-locale' ),
			'approvals' => esc_attr__( 'Approvals Required', 'wp-builder-locale' ),
			'invitations' => esc_attr__( 'Invitations Only', 'wp-builder-locale' ),
		];
		$arrays['register-password-type'] = [
			'enable' => esc_attr__( 'Allow users to set their passwords while registration.', 'wp-builder-locale' ),
			'disable' => esc_attr__( 'Generate password automatically and send via email.', 'wp-builder-locale' ),
		];
		$arrays['avatar-type'] = [
			'gravatar' => esc_attr__( 'Use Gravatar', 'wp-builder-locale' ),
			'custom' => esc_attr__( 'Allow custom profile picture upload', 'wp-builder-locale' ),
			'social' => esc_attr__( 'Use Social Profile Avatar', 'wp-builder-locale' ),
		];
		$arrays['spam-pages'] = [
			'none' => esc_attr__( 'None', 'wp-builder-locale' ),
			'login' => esc_attr__( 'Login Form', 'wp-builder-locale' ),
			'register' => esc_attr__( 'Registration Form', 'wp-builder-locale' ),
			'reset_password' => esc_attr__( 'Reset Password Form', 'wp-builder-locale' ),
		];
		$arrays['spam-theme'] = [
			'light' => esc_attr__( 'light', 'wp-builder-locale' ),
			'dark' => esc_attr__( 'dark', 'wp-builder-locale' ),
		];
		$arrays['mm-themes'] = [
			'default' => esc_attr__( 'Default', 'wp-builder-locale' ),
		];
		$arrays['mm-timer'] = [
			'days-left' => esc_attr__( 'Days Left', 'wp-builder-locale' ),
			'clock' => esc_attr__( 'Countdown Clock', 'wp-builder-locale' ),
		];
		$arrays['body-bg'] = [
			'color' => '#f7f7f7',
			'image' => null,
			'bg_repeat' => 'no-repeat',
			'bg_size' => 'cover',
			'bg_attachment' => 'inherit',
			'bg_position' => 'top center',
		];
		$arrays['background-repeat'] = [
			'inherit' => __( 'Inherit', 'wp-builder-locale' ),
			'repeat' => __( 'Repeat All', 'wp-builder-locale' ),
			'repeat-x' => __( 'Repeat Horizontal', 'wp-builder-locale' ),
			'repeat-y' => __( 'Repeat Vertical', 'wp-builder-locale' ),
			'no-repeat' => __( 'Do not repeat', 'wp-builder-locale' ),
		];
		$arrays['background-size'] = [
			'inherit' => __( 'Inherit', 'wp-builder-locale' ),
			'cover' => __( 'Cover', 'wp-builder-locale' ),
			'contain' => __( 'Contain', 'wp-builder-locale' ),
		];
		$arrays['background-attachment'] = [
			'inherit' => __( 'Inherit', 'wp-builder-locale' ),
			'fixed' => __( 'Fixed', 'wp-builder-locale' ),
			'scroll' => __( 'Scroll', 'wp-builder-locale' ),
		];
		$arrays['background-position'] = [
			'inherit' => __( 'Inherit', 'wp-builder-locale' ),
			'top left' => __( 'Top Left', 'wp-builder-locale' ),
			'top center' => __( 'Top Center', 'wp-builder-locale' ),
			'top right' => __( 'Top Right', 'wp-builder-locale' ),
			'bottom left' => __( 'Bottom Left', 'wp-builder-locale' ),
			'bottom center' => __( 'Bottom Center', 'wp-builder-locale' ),
			'bottom right' => __( 'Bottom Right', 'wp-builder-locale' ),
			'center left' => __( 'Center Left', 'wp-builder-locale' ),
			'center center' => __( 'Center Center', 'wp-builder-locale' ),
			'center right' => __( 'Center Right', 'wp-builder-locale' ),
		];
		$arrays['border-style'] = [
			"solid" => __( 'Solid', 'wp-builder-locale' ),
			"dotted" => __( 'Dotted', 'wp-builder-locale' ),
			"dashed" => __( 'Dashed', 'wp-builder-locale' ),
			"none" => __( 'None', 'wp-builder-locale' ),
			"hidden" => __( 'Hidden', 'wp-builder-locale' ),
			"double" => __( 'Double', 'wp-builder-locale' ),
			"groove" => __( 'Groove', 'wp-builder-locale' ),
			"ridge" => __( 'Ridge', 'wp-builder-locale' ),
			"inset" => __( 'Inset', 'wp-builder-locale' ),
			"outset" => __( 'Outset', 'wp-builder-locale' ),
			"initial" => __( 'Initial', 'wp-builder-locale' ),
			"inherit" => __( 'Inherit', 'wp-builder-locale' ),
		];
		$arrays['position'] = [
			"absolute" => __( 'absolute', 'wp-builder-locale' ),
			"fixed" => __( 'fixed', 'wp-builder-locale' ),
			"relative" => __( 'relative', 'wp-builder-locale' ),
			"static" => __( 'static', 'wp-builder-locale' ),
			"inherit" => __( 'inherit', 'wp-builder-locale' ),
		];
		$arrays['display'] = [
			'none' => __( 'None', 'wp-builder-locale' ),
			'inline' => __( 'Inline', 'wp-builder-locale' ),
			'block' => __( 'Block', 'wp-builder-locale' ),
			'flex' => __( 'Flex', 'wp-builder-locale' ),
			'inline-block' => __( 'Inline Block', 'wp-builder-locale' ),
			'inline-flex' => __( 'Inline Flex', 'wp-builder-locale' ),
			'inline-table' => __( 'Inline Table', 'wp-builder-locale' ),
			'list-item' => __( 'List Item', 'wp-builder-locale' ),
			'run-in' => __( 'Run In', 'wp-builder-locale' ),
			'table' => __( 'Table', 'wp-builder-locale' ),
			'table-caption' => __( 'Table Caption', 'wp-builder-locale' ),
			'table-column-group' => __( 'Table Column Group', 'wp-builder-locale' ),
			'table-header-group' => __( 'Table Header Group', 'wp-builder-locale' ),
			'table-footer-group' => __( 'Table Footer Group', 'wp-builder-locale' ),
			'table-row-group' => __( 'Table Row Group', 'wp-builder-locale' ),
			'table-cell' => __( 'Table Cell', 'wp-builder-locale' ),
			'table-column' => __( 'Table Column', 'wp-builder-locale' ),
			'table-row' => __( 'Table Row', 'wp-builder-locale' ),
		];

		$arrays['float'] = [
			'none' => __( 'None', 'wp-builder-locale' ),
			'left' => __( 'Left', 'wp-builder-locale' ),
			'right' => __( 'Right', 'wp-builder-locale' ),
		];
		$arrays['clear'] = [
			'none' => __( 'None', 'wp-builder-locale' ),
			'left' => __( 'Left', 'wp-builder-locale' ),
			'right' => __( 'Right', 'wp-builder-locale' ),
			'both' => __( 'Both', 'wp-builder-locale' ),
		];
		$arrays['visibility'] = [
			'hidden' => __( 'Hidden', 'wp-builder-locale' ),
			'visible' => __( 'Visible', 'wp-builder-locale' ),
			'collapse' => __( 'Collapse', 'wp-builder-locale' ),
		];
		$arrays['overflow'] = [
			'hidden' => __( 'Hidden', 'wp-builder-locale' ),
			'visible' => __( 'Visible', 'wp-builder-locale' ),
			'scroll' => __( 'Scroll', 'wp-builder-locale' ),
			'auto' => __( 'Auto', 'wp-builder-locale' ),
		];

		$arrays['font-weight'] = [
			'inherit' => esc_attr__( 'Inherit', 'wp-builder-locale' ),
			'bold' => esc_attr__( 'Bold', 'wp-builder-locale' ),
			'bolder' => esc_attr__( 'Bolder', 'wp-builder-locale' ),
			'normal' => esc_attr__( 'Normal', 'wp-builder-locale' ),
			'lighter' => esc_attr__( 'Lighter', 'wp-builder-locale' ),
			'100' => esc_attr__( '100', 'wp-builder-locale' ),
			'200' => esc_attr__( '200', 'wp-builder-locale' ),
			'300' => esc_attr__( '300', 'wp-builder-locale' ),
			'400' => esc_attr__( '400', 'wp-builder-locale' ),
			'500' => esc_attr__( '500', 'wp-builder-locale' ),
			'600' => esc_attr__( '600', 'wp-builder-locale' ),
			'700' => esc_attr__( '700', 'wp-builder-locale' ),
			'800' => esc_attr__( '800', 'wp-builder-locale' ),
			'900' => esc_attr__( '900', 'wp-builder-locale' ),
		];
		$arrays['font-style'] = [
			'inherit' => esc_attr__( 'Inherit', 'wp-builder-locale' ),
			'italic' => esc_attr__( 'Italic', 'wp-builder-locale' ),
			'oblique' => esc_attr__( 'Oblique', 'wp-builder-locale' ),
			'normal' => esc_attr__( 'Normal', 'wp-builder-locale' ),
		];
		for( $i = 8; $i < 37; $i ++ ) {
			$arrays['font-size'][ $i . 'px' ] = $i . 'px';
		}
		$arrays['text-align'] = [
			'inherit' => esc_attr__( 'Inherit', 'wp-builder-locale' ),
			'left' => esc_attr__( 'Left', 'wp-builder-locale' ),
			'right' => esc_attr__( 'Right', 'wp-builder-locale' ),
			'center' => esc_attr__( 'Center', 'wp-builder-locale' ),
			'justify' => esc_attr__( 'Justify', 'wp-builder-locale' ),
		];
		$arrays['image-align'] = [
			'none' => esc_attr__( 'None', 'wp-builder-locale' ),
			'left' => esc_attr__( 'Left', 'wp-builder-locale' ),
			'right' => esc_attr__( 'Right', 'wp-builder-locale' ),
			'center' => esc_attr__( 'Center', 'wp-builder-locale' ),
		];
		$arrays['text-decoration'] = [
			'inherit' => esc_attr__( 'Inherit', 'wp-builder-locale' ),
			'underline' => esc_attr__( 'Underline', 'wp-builder-locale' ),
			'overline' => esc_attr__( 'Overline', 'wp-builder-locale' ),
			'line-through' => esc_attr__( 'Line Through', 'wp-builder-locale' ),
			'initial' => esc_attr__( 'initial', 'wp-builder-locale' ),
		];
		$arrays['text-transform'] = [
			'none' => esc_attr__( 'None', 'wp-builder-locale' ),
			'capitalize' => esc_attr__( 'Capitalize', 'wp-builder-locale' ),
			'uppercase' => esc_attr__( 'Uppercase', 'wp-builder-locale' ),
			'lowercase' => esc_attr__( 'Lowercase', 'wp-builder-locale' ),
			'initial' => esc_attr__( 'Initial', 'wp-builder-locale' ),
			'inherit' => esc_attr__( 'Inherit', 'wp-builder-locale' ),
		];
		$arrays['font-variant'] = [
			'normal' => esc_attr__( 'Normal', 'wp-builder-locale' ),
			'small-caps' => esc_attr__( 'Small Caps', 'wp-builder-locale' ),
		];
		$arrays['white-space'] = [
			'normal' => __( 'normal', 'wp-builder-locale' ),
			'nowrap' => __( 'nowrap', 'wp-builder-locale' ),
			'pre' => __( 'pre', 'wp-builder-locale' ),
			'pre-line' => __( 'pre-line', 'wp-builder-locale' ),
			'pre-wrap' => __( 'pre-wrap', 'wp-builder-locale' ),
			'initial' => __( 'initial', 'wp-builder-locale' ),
			'inherit' => __( 'inherit', 'wp-builder-locale' ),
		];
		$arrays['button-size'] = [
			'mini' => esc_attr__( 'mini', 'wp-builder-locale' ),
			'small' => esc_attr__( 'small', 'wp-builder-locale' ),
			'default' => esc_attr__( 'default', 'wp-builder-locale' ),
			'medium' => esc_attr__( 'medium', 'wp-builder-locale' ),
			'large' => esc_attr__( 'large', 'wp-builder-locale' ),
		];
		$arrays['section-size'] = [
			'default' => esc_attr__( 'Default', 'wp-builder-locale' ),
			'small' => esc_attr__( 'Small', 'wp-builder-locale' ),
			'medium' => esc_attr__( 'Medium', 'wp-builder-locale' ),
			'large' => esc_attr__( 'Large', 'wp-builder-locale' ),
		];
		$arrays['hero-size'] = [
			'small' => esc_attr__( 'Small', 'wp-builder-locale' ),
			'medium' => esc_attr__( 'Medium', 'wp-builder-locale' ),
			'large' => esc_attr__( 'Large', 'wp-builder-locale' ),
			'fullheight' => esc_attr__( 'Full Height', 'wp-builder-locale' ),
		];
		$arrays['link-target'] = [
			'_self' => esc_attr__( 'Same window', 'wp-builder-locale' ),
			'_blank' => esc_attr__( 'New window', 'wp-builder-locale' ),
		];

		$arrays['opacity'] = [
			'0' => '0',
			'10' => '10',
			'20' => '20',
			'30' => '30',
			'40' => '40',
			'50' => '50',
			'60' => '60',
			'70' => '70',
			'80' => '80',
			'90' => '90',
			'100' => '100',
		];

		$arrays['animate-css'] = [
			'bounce' => 'bounce',
			'flash' => 'flash',
			'pulse' => 'pulse',
			'rubberBand' => 'rubberBand',
			'shake' => 'shake',
			'headShake' => 'headShake',
			'swing' => 'swing',
			'tada' => 'tada',
			'wobble' => 'wobble',
			'jello' => 'jello',
			'bounceIn' => 'bounceIn',
			'bounceInDown' => 'bounceInDown',
			'bounceInLeft' => 'bounceInLeft',
			'bounceInRight' => 'bounceInRight',
			'bounceInUp' => 'bounceInUp',
			'bounceOut' => 'bounceOut',
			'bounceOutDown' => 'bounceOutDown',
			'bounceOutLeft' => 'bounceOutLeft',
			'bounceOutRight' => 'bounceOutRight',
			'bounceOutUp' => 'bounceOutUp',
			'fadeIn' => 'fadeIn',
			'fadeInDown' => 'fadeInDown',
			'fadeInDownBig' => 'fadeInDownBig',
			'fadeInLeft' => 'fadeInLeft',
			'fadeInLeftBig' => 'fadeInLeftBig',
			'fadeInRight' => 'fadeInRight',
			'fadeInRightBig' => 'fadeInRightBig',
			'fadeInUp' => 'fadeInUp',
			'fadeInUpBig' => 'fadeInUpBig',
			'fadeOut' => 'fadeOut',
			'fadeOutDown' => 'fadeOutDown',
			'fadeOutDownBig' => 'fadeOutDownBig',
			'fadeOutLeft' => 'fadeOutLeft',
			'fadeOutLeftBig' => 'fadeOutLeftBig',
			'fadeOutRight' => 'fadeOutRight',
			'fadeOutRightBig' => 'fadeOutRightBig',
			'fadeOutUp' => 'fadeOutUp',
			'fadeOutUpBig' => 'fadeOutUpBig',
			'flipInX' => 'flipInX',
			'flipInY' => 'flipInY',
			'flipOutX' => 'flipOutX',
			'flipOutY' => 'flipOutY',
			'lightSpeedIn' => 'lightSpeedIn',
			'lightSpeedOut' => 'lightSpeedOut',
			'rotateIn' => 'rotateIn',
			'rotateInDownLeft' => 'rotateInDownLeft',
			'rotateInDownRight' => 'rotateInDownRight',
			'rotateInUpLeft' => 'rotateInUpLeft',
			'rotateInUpRight' => 'rotateInUpRight',
			'rotateOut' => 'rotateOut',
			'rotateOutDownLeft' => 'rotateOutDownLeft',
			'rotateOutDownRight' => 'rotateOutDownRight',
			'rotateOutUpLeft' => 'rotateOutUpLeft',
			'rotateOutUpRight' => 'rotateOutUpRight',
			'hinge' => 'hinge',
			'rollIn' => 'rollIn',
			'rollOut' => 'rollOut',
			'zoomIn' => 'zoomIn',
			'zoomInDown' => 'zoomInDown',
			'zoomInLeft' => 'zoomInLeft',
			'zoomInRight' => 'zoomInRight',
			'zoomInUp' => 'zoomInUp',
			'zoomOut' => 'zoomOut',
			'zoomOutDown' => 'zoomOutDown',
			'zoomOutLeft' => 'zoomOutLeft',
			'zoomOutRight' => 'zoomOutRight',
			'zoomOutUp' => 'zoomOutUp',
			'slideInDown' => 'slideInDown',
			'slideInLeft' => 'slideInLeft',
			'slideInRight' => 'slideInRight',
			'slideInUp' => 'slideInUp',
			'slideOutDown' => 'slideOutDown',
			'slideOutLeft' => 'slideOutLeft',
			'slideOutRight' => 'slideOutRight',
			'slideOutUp' => 'slideOutUp',
		];

		$arrays['captcha-lang'] = [
			'ar' => 'Arabic',
			'bg' => 'Bulgarian',
			'ca' => 'Catalan',
			'zh-CN' => 'Chinese (Simplified)',
			'zh-TW' => 'Chinese (Traditional)',
			'hr' => 'Croatian',
			'cs' => 'Czech',
			'da' => 'Danish',
			'nl' => 'Dutch',
			'en-GB' => 'English (UK)',
			'en' => 'English (US)',
			'fil' => 'Filipino',
			'fi' => 'Finnish',
			'fr' => 'French',
			'fr-CA' => 'French (Canadian)',
			'de' => 'German',
			'de-AT' => 'German (Austria)',
			'de-CH' => 'German (Switzerland)',
			'el' => 'Greek',
			'iw' => 'Hebrew',
			'hi' => 'Hindi',
			'hu' => 'Hungarain',
			'id' => 'Indonesian',
			'it' => 'Italian',
			'ja' => 'Japanese',
			'ko' => 'Korean',
			'lv' => 'Latvian',
			'lt' => 'Lithuanian',
			'no' => 'Norwegian',
			'fa' => 'Persian',
			'pl' => 'Polish',
			'pt' => 'Portuguese',
			'pt-BR' => 'Portuguese (Brazil)',
			'pt-PT' => 'Portuguese (Portugal)',
			'ro' => 'Romanian',
			'ru' => 'Russian',
			'sr' => 'Serbian',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'es' => 'Spanish',
			'es-419' => 'Spanish (Latin America)',
			'sv' => 'Swedish',
			'th' => 'Thai',
			'tr' => 'Turkish',
			'uk' => 'Ukrainian',
			'vi' => 'Vietnamese',
		];

		$arrays['moment-locales'] = [
			'en' => 'en',
			'af' => 'af',
			'ar-dz' => 'ar-dz',
			'ar-kw' => 'ar-kw',
			'ar-ly' => 'ar-ly',
			'ar-ma' => 'ar-ma',
			'ar-sa' => 'ar-sa',
			'ar-tn' => 'ar-tn',
			'ar' => 'ar',
			'az' => 'az',
			'be' => 'be',
			'bg' => 'bg',
			'bn' => 'bn',
			'bo' => 'bo',
			'br' => 'br',
			'bs' => 'bs',
			'ca' => 'ca',
			'cs' => 'cs',
			'cv' => 'cv',
			'cy' => 'cy',
			'da' => 'da',
			'de-at' => 'de-at',
			'de-ch' => 'de-ch',
			'de' => 'de',
			'dv' => 'dv',
			'el' => 'el',
			'en-au' => 'en-au',
			'en-ca' => 'en-ca',
			'en-gb' => 'en-gb',
			'en-ie' => 'en-ie',
			'en-nz' => 'en-nz',
			'eo' => 'eo',
			'es-do' => 'es-do',
			'es' => 'es',
			'et' => 'et',
			'eu' => 'eu',
			'fa' => 'fa',
			'fi' => 'fi',
			'fo' => 'fo',
			'fr-ca' => 'fr-ca',
			'fr-ch' => 'fr-ch',
			'fr' => 'fr',
			'fy' => 'fy',
			'gd' => 'gd',
			'gl' => 'gl',
			'gom-latn' => 'gom-latn',
			'he' => 'he',
			'hi' => 'hi',
			'hr' => 'hr',
			'hu' => 'hu',
			'hy-am' => 'hy-am',
			'id' => 'id',
			'is' => 'is',
			'it' => 'it',
			'ja' => 'ja',
			'jv' => 'jv',
			'ka' => 'ka',
			'kk' => 'kk',
			'km' => 'km',
			'kn' => 'kn',
			'ko' => 'ko',
			'ky' => 'ky',
			'lb' => 'lb',
			'lo' => 'lo',
			'lt' => 'lt',
			'lv' => 'lv',
			'me' => 'me',
			'mi' => 'mi',
			'mk' => 'mk',
			'ml' => 'ml',
			'mr' => 'mr',
			'ms-my' => 'ms-my',
			'ms' => 'ms',
			'my' => 'my',
			'nb' => 'nb',
			'ne' => 'ne',
			'nl-be' => 'nl-be',
			'nl' => 'nl',
			'nn' => 'nn',
			'pa-in' => 'pa-in',
			'pl' => 'pl',
			'pt-br' => 'pt-br',
			'pt' => 'pt',
			'ro' => 'ro',
			'ru' => 'ru',
			'sd' => 'sd',
			'se' => 'se',
			'si' => 'si',
			'sk' => 'sk',
			'sl' => 'sl',
			'sq' => 'sq',
			'sr-cyrl' => 'sr-cyrl',
			'sr' => 'sr',
			'ss' => 'ss',
			'sv' => 'sv',
			'sw' => 'sw',
			'ta' => 'ta',
			'te' => 'te',
			'tet' => 'tet',
			'th' => 'th',
			'tl-ph' => 'tl-ph',
			'tlh' => 'tlh',
			'tr' => 'tr',
			'tzl' => 'tzl',
			'tzm-latn' => 'tzm-latn',
			'tzm' => 'tzm',
			'uk' => 'uk',
			'ur' => 'ur',
			'uz-latn' => 'uz-latn',
			'uz' => 'uz',
			'vi' => 'vi',
			'x-pseudo' => 'x-pseudo',
			'yo' => 'yo',
			'zh-cn' => 'zh-cn',
			'zh-hk' => 'zh-hk',
			'zh-tw' => 'zh-tw',
		];

		$arrays['social-logins'] = [
			'Facebook',
			'Twitter',
			'Google',
			'Yahoo',
			'Live',
			'LinkedIn',
			'Foursquare',
			'OpenID',
			'Github',
			'LastFM',
			'Vimeo',
			'Viadeo',
			'Identica',
			'Tumblr',
			'Goodreads',
			'QQ',
			'Sina',
			'Murmur',
			'Pixnet',
			'Plurk',
			'Skyrock',
			'Geni',
			'FamilySearch',
			'MyHeritage',
			'px500',
			'Vkontakte',
			'Mail.ru',
			'Yandex',
			'Odnoklassniki',
			'Instagram',
			'TwitchTV',
			'Steam',
		];

		$arrays['social-icons'] = [
			'pe-so-500px' => 'pe-so-500px',
			'pe-so-aim' => 'pe-so-aim',
			'pe-so-amazon' => 'pe-so-amazon',
			'pe-so-android' => 'pe-so-android',
			'pe-so-app-store' => 'pe-so-app-store',
			'pe-so-apple' => 'pe-so-apple',
			'pe-so-behance' => 'pe-so-behance',
			'pe-so-bitbucket' => 'pe-so-bitbucket',
			'pe-so-blogger' => 'pe-so-blogger',
			'pe-so-codepen' => 'pe-so-codepen',
			'pe-so-css3' => 'pe-so-css3',
			'pe-so-deviantart-1' => 'pe-so-deviantart-1',
			'pe-so-deviantart-2' => 'pe-so-deviantart-2',
			'pe-so-digg' => 'pe-so-digg',
			'pe-so-dribbble' => 'pe-so-dribbble',
			'pe-so-dropbox' => 'pe-so-dropbox',
			'pe-so-drupal' => 'pe-so-drupal',
			'pe-so-etsy' => 'pe-so-etsy',
			'pe-so-evernote' => 'pe-so-evernote',
			'pe-so-facebook' => 'pe-so-facebook',
			'pe-so-firefox' => 'pe-so-firefox',
			'pe-so-flattr' => 'pe-so-flattr',
			'pe-so-forrst' => 'pe-so-forrst',
			'pe-so-foursquare' => 'pe-so-foursquare',
			'pe-so-git' => 'pe-so-git',
			'pe-so-github' => 'pe-so-github',
			'pe-so-google-plus' => 'pe-so-google-plus',
			'pe-so-grooveshark' => 'pe-so-grooveshark',
			'pe-so-habbo' => 'pe-so-habbo',
			'pe-so-hacker-news' => 'pe-so-hacker-news',
			'pe-so-html5' => 'pe-so-html5',
			'pe-so-ie' => 'pe-so-ie',
			'pe-so-instagram' => 'pe-so-instagram',
			'pe-so-jsfiddle' => 'pe-so-jsfiddle',
			'pe-so-lanyrd' => 'pe-so-lanyrd',
			'pe-so-lastfm' => 'pe-so-lastfm',
			'pe-so-linkedin' => 'pe-so-linkedin',
			'pe-so-linux' => 'pe-so-linux',
			'pe-so-love' => 'pe-so-love',
			'pe-so-myspace' => 'pe-so-myspace',
			'pe-so-odnolassniki' => 'pe-so-odnolassniki',
			'pe-so-openid' => 'pe-so-openid',
			'pe-so-opera' => 'pe-so-opera',
			'pe-so-paypal-1' => 'pe-so-paypal-1',
			'pe-so-paypal-2' => 'pe-so-paypal-2',
			'pe-so-pied-piper' => 'pe-so-pied-piper',
			'pe-so-pinterest' => 'pe-so-pinterest',
			'pe-so-qq' => 'pe-so-qq',
			'pe-so-qzone' => 'pe-so-qzone',
			'pe-so-rdio' => 'pe-so-rdio',
			'pe-so-reddit' => 'pe-so-reddit',
			'pe-so-renren' => 'pe-so-renren',
			'pe-so-rss' => 'pe-so-rss',
			'pe-so-safari-1' => 'pe-so-safari-1',
			'pe-so-safari-2' => 'pe-so-safari-2',
			'pe-so-share' => 'pe-so-share',
			'pe-so-skype' => 'pe-so-skype',
			'pe-so-slideshare' => 'pe-so-slideshare',
			'pe-so-soundcloud' => 'pe-so-soundcloud',
			'pe-so-spotify' => 'pe-so-spotify',
			'pe-so-stack-exchange' => 'pe-so-stack-exchange',
			'pe-so-stack-overflow' => 'pe-so-stack-overflow',
			'pe-so-steam' => 'pe-so-steam',
			'pe-so-stumbleupon' => 'pe-so-stumbleupon',
			'pe-so-tencent-weibo' => 'pe-so-tencent-weibo',
			'pe-so-trello' => 'pe-so-trello',
			'pe-so-tripadvisor' => 'pe-so-tripadvisor',
			'pe-so-tumblr' => 'pe-so-tumblr',
			'pe-so-twitch' => 'pe-so-twitch',
			'pe-so-twitter' => 'pe-so-twitter',
			'pe-so-viadeo' => 'pe-so-viadeo',
			'pe-so-vimeo' => 'pe-so-vimeo',
			'pe-so-vine' => 'pe-so-vine',
			'pe-so-vk' => 'pe-so-vk',
			'pe-so-wechat' => 'pe-so-wechat',
			'pe-so-weibo' => 'pe-so-weibo',
			'pe-so-wikipedia' => 'pe-so-wikipedia',
			'pe-so-windows' => 'pe-so-windows',
			'pe-so-wordpress-1' => 'pe-so-wordpress-1',
			'pe-so-wordpress-2' => 'pe-so-wordpress-2',
			'pe-so-xing' => 'pe-so-xing',
			'pe-so-yahoo-1' => 'pe-so-yahoo-1',
			'pe-so-yahoo-2' => 'pe-so-yahoo-2',
			'pe-so-yelp' => 'pe-so-yelp',
			'pe-so-youtube-1' => 'pe-so-youtube-1',
			'pe-so-youtube-2' => 'pe-so-youtube-2',
			'pe-so-zerply' => 'pe-so-zerply',
			'pe-so-bootstrap' => 'pe-so-bootstrap',
			'pe-so-pixeden' => 'pe-so-pixeden',
			'pe-so-magento' => 'pe-so-magento',
			'pe-so-sass' => 'pe-so-sass',
			'pe-so-ubuntu' => 'pe-so-ubuntu',
			'pe-so-like' => 'pe-so-like',
			'pe-so-chrome' => 'pe-so-chrome',
			'pe-so-delicious' => 'pe-so-delicious',
			'pe-so-ebay' => 'pe-so-ebay',
			'pe-so-flickr' => 'pe-so-flickr',
			'pe-so-google-drive' => 'pe-so-google-drive',
			'pe-so-joomla' => 'pe-so-joomla',
			'pe-so-picasa' => 'pe-so-picasa',
		];

		$arrays['mime-types'] = [
			'x-world/x-3dmf' => '3dm',
			'x-world/x-3dmf' => '3dmf',
			'application/octet-stream' => 'a',
			'application/x-authorware-bin' => 'aab',
			'application/x-authorware-map' => 'aam',
			'application/x-authorware-seg' => 'aas',
			'text/vnd.abc' => 'abc',
			'text/html' => 'acgi',
			'video/animaflex' => 'afl',
			'application/postscript' => 'ai',
			'audio/aiff' => 'aif',
			'audio/x-aiff' => 'aif',
			'audio/aiff' => 'aifc',
			'audio/x-aiff' => 'aifc',
			'audio/aiff' => 'aiff',
			'audio/x-aiff' => 'aiff',
			'application/x-aim' => 'aim',
			'text/x-audiosoft-intra' => 'aip',
			'application/x-navi-animation' => 'ani',
			'application/x-nokia-9000-communicator-add-on-software' => 'aos',
			'application/mime' => 'aps',
			'application/octet-stream' => 'arc',
			'application/arj' => 'arj',
			'application/octet-stream' => 'arj',
			'image/x-jg' => 'art',
			'video/x-ms-asf' => 'asf',
			'text/x-asm' => 'asm',
			'text/asp' => 'asp',
			'application/x-mplayer2' => 'asx',
			'video/x-ms-asf' => 'asx',
			'video/x-ms-asf-plugin' => 'asx',
			'audio/basic' => 'au',
			'audio/x-au' => 'au',
			'application/x-troff-msvideo' => 'avi',
			'video/avi' => 'avi',
			'video/msvideo' => 'avi',
			'video/x-msvideo' => 'avi',
			'video/avs-video' => 'avs',
			'application/x-bcpio' => 'bcpio',
			'application/mac-binary' => 'bin',
			'application/macbinary' => 'bin',
			'application/octet-stream' => 'bin',
			'application/x-binary' => 'bin',
			'application/x-macbinary' => 'bin',
			'image/bmp' => 'bm',
			'image/bmp' => 'bmp',
			'image/x-windows-bmp' => 'bmp',
			'application/book' => 'boo',
			'application/book' => 'book',
			'application/x-bzip2' => 'boz',
			'application/x-bsh' => 'bsh',
			'application/x-bzip' => 'bz',
			'application/x-bzip2' => 'bz2',
			'text/plain' => 'c',
			'text/x-c' => 'c',
			'text/plain' => 'c++',
			'application/vnd.ms-pki.seccat' => 'cat',
			'text/plain' => 'cc',
			'text/x-c' => 'cc',
			'application/clariscad' => 'ccad',
			'application/x-cocoa' => 'cco',
			'application/cdf' => 'cdf',
			'application/x-cdf' => 'cdf',
			'application/x-netcdf' => 'cdf',
			'application/pkix-cert' => 'cer',
			'application/x-x509-ca-cert' => 'cer',
			'application/x-chat' => 'cha',
			'application/x-chat' => 'chat',
			'application/java' => 'class',
			'application/java-byte-code' => 'class',
			'application/x-java-class' => 'class',
			'application/octet-stream' => 'com',
			'text/plain' => 'com',
			'text/plain' => 'conf',
			'application/x-cpio' => 'cpio',
			'text/x-c' => 'cpp',
			'application/mac-compactpro' => 'cpt',
			'application/x-compactpro' => 'cpt',
			'application/x-cpt' => 'cpt',
			'application/pkcs-crl' => 'crl',
			'application/pkix-crl' => 'crl',
			'application/pkix-cert' => 'crt',
			'application/x-x509-ca-cert' => 'crt',
			'application/x-x509-user-cert' => 'crt',
			'application/x-csh' => 'csh',
			'text/x-script.csh' => 'csh',
			'application/x-pointplus' => 'css',
			'text/css' => 'css',
			'text/plain' => 'cxx',
			'application/x-director' => 'dcr',
			'application/x-deepv' => 'deepv',
			'text/plain' => 'def',
			'application/x-x509-ca-cert' => 'der',
			'video/x-dv' => 'dif',
			'application/x-director' => 'dir',
			'video/dl' => 'dl',
			'video/x-dl' => 'dl',
			'application/msword' => 'doc',
			'application/msword' => 'dot',
			'application/commonground' => 'dp',
			'application/drafting' => 'drw',
			'application/octet-stream' => 'dump',
			'video/x-dv' => 'dv',
			'application/x-dvi' => 'dvi',
			'drawing/x-dwf (old)' => 'dwf',
			'model/vnd.dwf' => 'dwf',
			'application/acad' => 'dwg',
			'image/vnd.dwg' => 'dwg',
			'image/x-dwg' => 'dwg',
			'application/dxf' => 'dxf',
			'image/vnd.dwg' => 'dxf',
			'image/x-dwg' => 'dxf',
			'application/x-director' => 'dxr',
			'text/x-script.elisp' => 'el',
			'application/x-bytecode.elisp (compiled elisp)' => 'elc',
			'application/x-elc' => 'elc',
			'application/x-envoy' => 'env',
			'application/postscript' => 'eps',
			'application/x-esrehber' => 'es',
			'text/x-setext' => 'etx',
			'application/envoy' => 'evy',
			'application/x-envoy' => 'evy',
			'application/octet-stream' => 'exe',
			'text/plain' => 'f',
			'text/x-fortran' => 'f',
			'text/x-fortran' => 'f77',
			'text/plain' => 'f90',
			'text/x-fortran' => 'f90',
			'application/vnd.fdf' => 'fdf',
			'application/fractals' => 'fif',
			'image/fif' => 'fif',
			'video/fli' => 'fli',
			'video/x-fli' => 'fli',
			'image/florian' => 'flo',
			'text/vnd.fmi.flexstor' => 'flx',
			'video/x-atomic3d-feature' => 'fmf',
			'text/plain' => 'for',
			'text/x-fortran' => 'for',
			'image/vnd.fpx' => 'fpx',
			'image/vnd.net-fpx' => 'fpx',
			'application/freeloader' => 'frl',
			'audio/make' => 'funk',
			'text/plain' => 'g',
			'image/g3fax' => 'g3',
			'image/gif' => 'gif',
			'video/gl' => 'gl',
			'video/x-gl' => 'gl',
			'audio/x-gsm' => 'gsd',
			'audio/x-gsm' => 'gsm',
			'application/x-gsp' => 'gsp',
			'application/x-gss' => 'gss',
			'application/x-gtar' => 'gtar',
			'application/x-compressed' => 'gz',
			'application/x-gzip' => 'gz',
			'application/x-gzip' => 'gzip',
			'multipart/x-gzip' => 'gzip',
			'text/plain' => 'h',
			'text/x-h' => 'h',
			'application/x-hdf' => 'hdf',
			'application/x-helpfile' => 'help',
			'application/vnd.hp-hpgl' => 'hgl',
			'text/plain' => 'hh',
			'text/x-h' => 'hh',
			'text/x-script' => 'hlb',
			'application/hlp' => 'hlp',
			'application/x-helpfile' => 'hlp',
			'application/x-winhelp' => 'hlp',
			'application/vnd.hp-hpgl' => 'hpg',
			'application/vnd.hp-hpgl' => 'hpgl',
			'application/binhex' => 'hqx',
			'application/binhex4' => 'hqx',
			'application/mac-binhex' => 'hqx',
			'application/mac-binhex40' => 'hqx',
			'application/x-binhex40' => 'hqx',
			'application/x-mac-binhex40' => 'hqx',
			'application/hta' => 'hta',
			'text/x-component' => 'htc',
			'text/html' => 'htm',
			'text/html' => 'html',
			'text/html' => 'htmls',
			'text/webviewhtml' => 'htt',
			'text/html' => 'htx',
			'x-conference/x-cooltalk' => 'ice',
			'image/x-icon' => 'ico',
			'text/plain' => 'idc',
			'image/ief' => 'ief',
			'image/ief' => 'iefs',
			'application/iges' => 'iges',
			'model/iges' => 'iges',
			'application/iges' => 'igs',
			'model/iges' => 'igs',
			'application/x-ima' => 'ima',
			'application/x-httpd-imap' => 'imap',
			'application/inf' => 'inf',
			'application/x-internett-signup' => 'ins',
			'application/x-ip2' => 'ip',
			'video/x-isvideo' => 'isu',
			'audio/it' => 'it',
			'application/x-inventor' => 'iv',
			'i-world/i-vrml' => 'ivr',
			'application/x-livescreen' => 'ivy',
			'audio/x-jam' => 'jam',
			'text/plain' => 'jav',
			'text/x-java-source' => 'jav',
			'text/plain' => 'java',
			'text/x-java-source' => 'java',
			'application/x-java-commerce' => 'jcm',
			'image/jpeg' => 'jfif',
			'image/pjpeg' => 'jfif',
			'image/jpeg' => 'jfif-tbnl',
			'image/jpeg' => 'jpe',
			'image/pjpeg' => 'jpe',
			'image/jpeg' => 'jpeg',
			'image/pjpeg' => 'jpeg',
			'image/jpeg' => 'jpg',
			'image/pjpeg' => 'jpg',
			'image/x-jps' => 'jps',
			'application/x-javascript' => 'js',
			'application/javascript' => 'js',
			'application/ecmascript' => 'js',
			'text/javascript' => 'js',
			'text/ecmascript' => 'js',
			'image/jutvision' => 'jut',
			'audio/midi' => 'kar',
			'music/x-karaoke' => 'kar',
			'application/x-ksh' => 'ksh',
			'text/x-script.ksh' => 'ksh',
			'audio/nspaudio' => 'la',
			'audio/x-nspaudio' => 'la',
			'audio/x-liveaudio' => 'lam',
			'application/x-latex' => 'latex',
			'application/lha' => 'lha',
			'application/octet-stream' => 'lha',
			'application/x-lha' => 'lha',
			'application/octet-stream' => 'lhx',
			'text/plain' => 'list',
			'audio/nspaudio' => 'lma',
			'audio/x-nspaudio' => 'lma',
			'text/plain' => 'log',
			'application/x-lisp' => 'lsp',
			'text/x-script.lisp' => 'lsp',
			'text/plain' => 'lst',
			'text/x-la-asf' => 'lsx',
			'application/x-latex' => 'ltx',
			'application/octet-stream' => 'lzh',
			'application/x-lzh' => 'lzh',
			'application/lzx' => 'lzx',
			'application/octet-stream' => 'lzx',
			'application/x-lzx' => 'lzx',
			'text/plain' => 'm',
			'text/x-m' => 'm',
			'video/mpeg' => 'm1v',
			'audio/mpeg' => 'm2a',
			'video/mpeg' => 'm2v',
			'audio/x-mpequrl' => 'm3u',
			'application/x-troff-man' => 'man',
			'application/x-navimap' => 'map',
			'text/plain' => 'mar',
			'application/mbedlet' => 'mbd',
			'application/x-magic-cap-package-1.0' => 'mc$',
			'application/mcad' => 'mcd',
			'application/x-mathcad' => 'mcd',
			'image/vasa' => 'mcf',
			'text/mcf' => 'mcf',
			'application/netmc' => 'mcp',
			'application/x-troff-me' => 'me',
			'message/rfc822' => 'mht',
			'message/rfc822' => 'mhtml',
			'application/x-midi' => 'mid',
			'audio/midi' => 'mid',
			'audio/x-mid' => 'mid',
			'audio/x-midi' => 'mid',
			'music/crescendo' => 'mid',
			'x-music/x-midi' => 'mid',
			'application/x-midi' => 'midi',
			'audio/midi' => 'midi',
			'audio/x-mid' => 'midi',
			'audio/x-midi' => 'midi',
			'music/crescendo' => 'midi',
			'x-music/x-midi' => 'midi',
			'application/x-frame' => 'mif',
			'application/x-mif' => 'mif',
			'message/rfc822' => 'mime',
			'www/mime' => 'mime',
			'audio/x-vnd.audioexplosion.mjuicemediafile' => 'mjf',
			'video/x-motion-jpeg' => 'mjpg',
			'application/base64' => 'mm',
			'application/x-meme' => 'mm',
			'application/base64' => 'mme',
			'audio/mod' => 'mod',
			'audio/x-mod' => 'mod',
			'video/quicktime' => 'moov',
			'video/quicktime' => 'mov',
			'video/x-sgi-movie' => 'movie',
			'audio/mpeg' => 'mp2',
			'audio/x-mpeg' => 'mp2',
			'video/mpeg' => 'mp2',
			'video/x-mpeg' => 'mp2',
			'video/x-mpeq2a' => 'mp2',
			'audio/mpeg3' => 'mp3',
			'audio/x-mpeg-3' => 'mp3',
			'video/mpeg' => 'mp3',
			'video/x-mpeg' => 'mp3',
			'audio/mpeg' => 'mpa',
			'video/mpeg' => 'mpa',
			'application/x-project' => 'mpc',
			'video/mpeg' => 'mpe',
			'video/mpeg' => 'mpeg',
			'audio/mpeg' => 'mpg',
			'video/mpeg' => 'mpg',
			'audio/mpeg' => 'mpga',
			'application/vnd.ms-project' => 'mpp',
			'application/x-project' => 'mpt',
			'application/x-project' => 'mpv',
			'application/x-project' => 'mpx',
			'application/marc' => 'mrc',
			'application/x-troff-ms' => 'ms',
			'video/x-sgi-movie' => 'mv',
			'audio/make' => 'my',
			'application/x-vnd.audioexplosion.mzz' => 'mzz',
			'image/naplps' => 'nap',
			'image/naplps' => 'naplps',
			'application/x-netcdf' => 'nc',
			'application/vnd.nokia.configuration-message' => 'ncm',
			'image/x-niff' => 'nif',
			'image/x-niff' => 'niff',
			'application/x-mix-transfer' => 'nix',
			'application/x-conference' => 'nsc',
			'application/x-navidoc' => 'nvd',
			'application/octet-stream' => 'o',
			'application/oda' => 'oda',
			'application/x-omc' => 'omc',
			'application/x-omcdatamaker' => 'omcd',
			'application/x-omcregerator' => 'omcr',
			'text/x-pascal' => 'p',
			'application/pkcs10' => 'p10',
			'application/x-pkcs10' => 'p10',
			'application/pkcs-12' => 'p12',
			'application/x-pkcs12' => 'p12',
			'application/x-pkcs7-signature' => 'p7a',
			'application/pkcs7-mime' => 'p7c',
			'application/x-pkcs7-mime' => 'p7c',
			'application/pkcs7-mime' => 'p7m',
			'application/x-pkcs7-mime' => 'p7m',
			'application/x-pkcs7-certreqresp' => 'p7r',
			'application/pkcs7-signature' => 'p7s',
			'application/pro_eng' => 'part',
			'text/pascal' => 'pas',
			'image/x-portable-bitmap' => 'pbm',
			'application/vnd.hp-pcl' => 'pcl',
			'application/x-pcl' => 'pcl',
			'image/x-pict' => 'pct',
			'image/x-pcx' => 'pcx',
			'chemical/x-pdb' => 'pdb',
			'application/pdf' => 'pdf',
			'audio/make' => 'pfunk',
			'audio/make.my.funk' => 'pfunk',
			'image/x-portable-graymap' => 'pgm',
			'image/x-portable-greymap' => 'pgm',
			'image/pict' => 'pic',
			'image/pict' => 'pict',
			'application/x-newton-compatible-pkg' => 'pkg',
			'application/vnd.ms-pki.pko' => 'pko',
			'text/plain' => 'pl',
			'text/x-script.perl' => 'pl',
			'application/x-pixclscript' => 'plx',
			'image/x-xpixmap' => 'pm',
			'text/x-script.perl-module' => 'pm',
			'application/x-pagemaker' => 'pm4',
			'application/x-pagemaker' => 'pm5',
			'image/png' => 'png',
			'application/x-portable-anymap' => 'pnm',
			'image/x-portable-anymap' => 'pnm',
			'application/mspowerpoint' => 'pot',
			'application/vnd.ms-powerpoint' => 'pot',
			'model/x-pov' => 'pov',
			'application/vnd.ms-powerpoint' => 'ppa',
			'image/x-portable-pixmap' => 'ppm',
			'application/mspowerpoint' => 'pps',
			'application/vnd.ms-powerpoint' => 'pps',
			'application/mspowerpoint' => 'ppt',
			'application/powerpoint' => 'ppt',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/x-mspowerpoint' => 'ppt',
			'application/mspowerpoint' => 'ppz',
			'application/x-freelance' => 'pre',
			'application/pro_eng' => 'prt',
			'application/postscript' => 'ps',
			'application/octet-stream' => 'psd',
			'paleovu/x-pv' => 'pvu',
			'application/vnd.ms-powerpoint' => 'pwz',
			'text/x-script.phyton' => 'py',
			'application/x-bytecode.python' => 'pyc',
			'audio/vnd.qcelp' => 'qcp',
			'x-world/x-3dmf' => 'qd3',
			'x-world/x-3dmf' => 'qd3d',
			'image/x-quicktime' => 'qif',
			'video/quicktime' => 'qt',
			'video/x-qtc' => 'qtc',
			'image/x-quicktime' => 'qti',
			'image/x-quicktime' => 'qtif',
			'audio/x-pn-realaudio' => 'ra',
			'audio/x-pn-realaudio-plugin' => 'ra',
			'audio/x-realaudio' => 'ra',
			'audio/x-pn-realaudio' => 'ram',
			'application/x-cmu-raster' => 'ras',
			'image/cmu-raster' => 'ras',
			'image/x-cmu-raster' => 'ras',
			'image/cmu-raster' => 'rast',
			'text/x-script.rexx' => 'rexx',
			'image/vnd.rn-realflash' => 'rf',
			'image/x-rgb' => 'rgb',
			'application/vnd.rn-realmedia' => 'rm',
			'audio/x-pn-realaudio' => 'rm',
			'audio/mid' => 'rmi',
			'audio/x-pn-realaudio' => 'rmm',
			'audio/x-pn-realaudio' => 'rmp',
			'audio/x-pn-realaudio-plugin' => 'rmp',
			'application/ringing-tones' => 'rng',
			'application/vnd.nokia.ringing-tone' => 'rng',
			'application/vnd.rn-realplayer' => 'rnx',
			'application/x-troff' => 'roff',
			'image/vnd.rn-realpix' => 'rp',
			'audio/x-pn-realaudio-plugin' => 'rpm',
			'text/richtext' => 'rt',
			'text/vnd.rn-realtext' => 'rt',
			'application/rtf' => 'rtf',
			'application/x-rtf' => 'rtf',
			'text/richtext' => 'rtf',
			'application/rtf' => 'rtx',
			'text/richtext' => 'rtx',
			'video/vnd.rn-realvideo' => 'rv',
			'text/x-asm' => 's',
			'audio/s3m' => 's3m',
			'application/octet-stream' => 'saveme',
			'application/x-tbook' => 'sbk',
			'application/x-lotusscreencam' => 'scm',
			'text/x-script.guile' => 'scm',
			'text/x-script.scheme' => 'scm',
			'video/x-scm' => 'scm',
			'text/plain' => 'sdml',
			'application/sdp' => 'sdp',
			'application/x-sdp' => 'sdp',
			'application/sounder' => 'sdr',
			'application/sea' => 'sea',
			'application/x-sea' => 'sea',
			'application/set' => 'set',
			'text/sgml' => 'sgm',
			'text/x-sgml' => 'sgm',
			'text/sgml' => 'sgml',
			'text/x-sgml' => 'sgml',
			'application/x-bsh' => 'sh',
			'application/x-sh' => 'sh',
			'application/x-shar' => 'sh',
			'text/x-script.sh' => 'sh',
			'application/x-bsh' => 'shar',
			'application/x-shar' => 'shar',
			'text/html' => 'shtml',
			'text/x-server-parsed-html' => 'shtml',
			'audio/x-psid' => 'sid',
			'application/x-sit' => 'sit',
			'application/x-stuffit' => 'sit',
			'application/x-koan' => 'skd',
			'application/x-koan' => 'skm',
			'application/x-koan' => 'skp',
			'application/x-koan' => 'skt',
			'application/x-seelogo' => 'sl',
			'application/smil' => 'smi',
			'application/smil' => 'smil',
			'audio/basic' => 'snd',
			'audio/x-adpcm' => 'snd',
			'application/solids' => 'sol',
			'application/x-pkcs7-certificates' => 'spc',
			'text/x-speech' => 'spc',
			'application/futuresplash' => 'spl',
			'application/x-sprite' => 'spr',
			'application/x-sprite' => 'sprite',
			'application/x-wais-source' => 'src',
			'text/x-server-parsed-html' => 'ssi',
			'application/streamingmedia' => 'ssm',
			'application/vnd.ms-pki.certstore' => 'sst',
			'application/step' => 'step',
			'application/sla' => 'stl',
			'application/vnd.ms-pki.stl' => 'stl',
			'application/x-navistyle' => 'stl',
			'application/step' => 'stp',
			'application/x-sv4cpio' => 'sv4cpio',
			'application/x-sv4crc' => 'sv4crc',
			'image/vnd.dwg' => 'svf',
			'image/x-dwg' => 'svf',
			'application/x-world' => 'svr',
			'x-world/x-svr' => 'svr',
			'application/x-shockwave-flash' => 'swf',
			'application/x-troff' => 't',
			'text/x-speech' => 'talk',
			'application/x-tar' => 'tar',
			'application/toolbook' => 'tbk',
			'application/x-tbook' => 'tbk',
			'application/x-tcl' => 'tcl',
			'text/x-script.tcl' => 'tcl',
			'text/x-script.tcsh' => 'tcsh',
			'application/x-tex' => 'tex',
			'application/x-texinfo' => 'texi',
			'application/x-texinfo' => 'texinfo',
			'application/plain' => 'text',
			'text/plain' => 'text',
			'application/gnutar' => 'tgz',
			'application/x-compressed' => 'tgz',
			'image/tiff' => 'tif',
			'image/x-tiff' => 'tif',
			'image/tiff' => 'tiff',
			'image/x-tiff' => 'tiff',
			'application/x-troff' => 'tr',
			'audio/tsp-audio' => 'tsi',
			'application/dsptype' => 'tsp',
			'audio/tsplayer' => 'tsp',
			'text/tab-separated-values' => 'tsv',
			'image/florian' => 'turbot',
			'text/plain' => 'txt',
			'text/x-uil' => 'uil',
			'text/uri-list' => 'uni',
			'text/uri-list' => 'unis',
			'application/i-deas' => 'unv',
			'text/uri-list' => 'uri',
			'text/uri-list' => 'uris',
			'application/x-ustar' => 'ustar',
			'multipart/x-ustar' => 'ustar',
			'application/octet-stream' => 'uu',
			'text/x-uuencode' => 'uu',
			'text/x-uuencode' => 'uue',
			'application/x-cdlink' => 'vcd',
			'text/x-vcalendar' => 'vcs',
			'application/vda' => 'vda',
			'video/vdo' => 'vdo',
			'application/groupwise' => 'vew',
			'video/vivo' => 'viv',
			'video/vnd.vivo' => 'viv',
			'video/vivo' => 'vivo',
			'video/vnd.vivo' => 'vivo',
			'application/vocaltec-media-desc' => 'vmd',
			'application/vocaltec-media-file' => 'vmf',
			'audio/voc' => 'voc',
			'audio/x-voc' => 'voc',
			'video/vosaic' => 'vos',
			'audio/voxware' => 'vox',
			'audio/x-twinvq-plugin' => 'vqe',
			'audio/x-twinvq' => 'vqf',
			'audio/x-twinvq-plugin' => 'vql',
			'application/x-vrml' => 'vrml',
			'model/vrml' => 'vrml',
			'x-world/x-vrml' => 'vrml',
			'x-world/x-vrt' => 'vrt',
			'application/x-visio' => 'vsd',
			'application/x-visio' => 'vst',
			'application/x-visio' => 'vsw',
			'application/wordperfect6.0' => 'w60',
			'application/wordperfect6.1' => 'w61',
			'application/msword' => 'w6w',
			'audio/wav' => 'wav',
			'audio/x-wav' => 'wav',
			'application/x-qpro' => 'wb1',
			'image/vnd.wap.wbmp' => 'wbmp',
			'application/vnd.xara' => 'web',
			'application/msword' => 'wiz',
			'application/x-123' => 'wk1',
			'windows/metafile' => 'wmf',
			'text/vnd.wap.wml' => 'wml',
			'application/vnd.wap.wmlc' => 'wmlc',
			'text/vnd.wap.wmlscript' => 'wmls',
			'application/vnd.wap.wmlscriptc' => 'wmlsc',
			'application/msword' => 'word',
			'application/wordperfect' => 'wp',
			'application/wordperfect' => 'wp5',
			'application/wordperfect6.0' => 'wp5',
			'application/wordperfect' => 'wp6',
			'application/wordperfect' => 'wpd',
			'application/x-wpwin' => 'wpd',
			'application/x-lotus' => 'wq1',
			'application/mswrite' => 'wri',
			'application/x-wri' => 'wri',
			'application/x-world' => 'wrl',
			'model/vrml' => 'wrl',
			'x-world/x-vrml' => 'wrl',
			'model/vrml' => 'wrz',
			'x-world/x-vrml' => 'wrz',
			'text/scriplet' => 'wsc',
			'application/x-wais-source' => 'wsrc',
			'application/x-wintalk' => 'wtk',
			'image/x-xbitmap' => 'xbm',
			'image/x-xbm' => 'xbm',
			'image/xbm' => 'xbm',
			'video/x-amt-demorun' => 'xdr',
			'xgl/drawing' => 'xgz',
			'image/vnd.xiff' => 'xif',
			'application/excel' => 'xl',
			'application/excel' => 'xla',
			'application/x-excel' => 'xla',
			'application/x-msexcel' => 'xla',
			'application/excel' => 'xlb',
			'application/vnd.ms-excel' => 'xlb',
			'application/x-excel' => 'xlb',
			'application/excel' => 'xlc',
			'application/vnd.ms-excel' => 'xlc',
			'application/x-excel' => 'xlc',
			'application/excel' => 'xld',
			'application/x-excel' => 'xld',
			'application/excel' => 'xlk',
			'application/x-excel' => 'xlk',
			'application/excel' => 'xll',
			'application/vnd.ms-excel' => 'xll',
			'application/x-excel' => 'xll',
			'application/excel' => 'xlm',
			'application/vnd.ms-excel' => 'xlm',
			'application/x-excel' => 'xlm',
			'application/excel' => 'xls',
			'application/vnd.ms-excel' => 'xls',
			'application/x-excel' => 'xls',
			'application/x-msexcel' => 'xls',
			'application/excel' => 'xlt',
			'application/x-excel' => 'xlt',
			'application/excel' => 'xlv',
			'application/x-excel' => 'xlv',
			'application/excel' => 'xlw',
			'application/vnd.ms-excel' => 'xlw',
			'application/x-excel' => 'xlw',
			'application/x-msexcel' => 'xlw',
			'audio/xm' => 'xm',
			'application/xml' => 'xml',
			'text/xml' => 'xml',
			'xgl/movie' => 'xmz',
			'application/x-vnd.ls-xpix' => 'xpix',
			'image/x-xpixmap' => 'xpm',
			'image/xpm' => 'xpm',
			'image/png' => 'x-png',
			'video/x-amt-showrun' => 'xsr',
			'image/x-xwd' => 'xwd',
			'image/x-xwindowdump' => 'xwd',
			'chemical/x-pdb' => 'xyz',
			'application/x-compress' => 'z',
			'application/x-compressed' => 'z',
			'application/x-compressed' => 'zip',
			'application/x-zip-compressed' => 'zip',
			'application/zip' => 'zip',
			'multipart/x-zip' => 'zip',
			'application/octet-stream' => 'zoo',
			'text/x-script.zsh' => 'zsh',
		];

		$arrays['meta-robots'] = [
			'index, nofollow' => 'index, nofollow',
			'noindex, follow' => 'noindex, follow',
			'noindex, nofollow' => 'noindex, nofollow',
		];

		$arrays['gradient-types'] = [
			'horizontal' => 'Horizontal',
			'vertical' => 'Vertical',
			'directional' => 'Directional (45deg)',
			'radial' => 'Radial',
		];

		$arrays['brand-colors'] = [
			'primary' => __( 'Primary', 'wp-builder-locale' ),
			'primary-dark' => __( 'Primary Dark', 'wp-builder-locale' ),
			'primary-light' => __( 'Primary Light', 'wp-builder-locale' ),
			'success' => __( 'Success', 'wp-builder-locale' ),
			'success-dark' => __( 'Success Dark', 'wp-builder-locale' ),
			'success-light' => __( 'Success Light', 'wp-builder-locale' ),
			'danger' => __( 'Danger', 'wp-builder-locale' ),
			'danger-dark' => __( 'Danger Dark', 'wp-builder-locale' ),
			'danger-light' => __( 'Danger Light', 'wp-builder-locale' ),
			'info' => __( 'Info', 'wp-builder-locale' ),
			'info-dark' => __( 'Info Dark', 'wp-builder-locale' ),
			'info-light' => __( 'Info Light', 'wp-builder-locale' ),
			'warning' => __( 'Warning', 'wp-builder-locale' ),
			'warning-dark' => __( 'Warning Dark', 'wp-builder-locale' ),
			'warning-light' => __( 'Warning Light', 'wp-builder-locale' ),

			'dark' => __( 'Dark', 'wp-builder-locale' ),
			'light' => __( 'Light', 'wp-builder-locale' ),
			'black' => __( 'Black', 'wp-builder-locale' ),
			'white' => __( 'White', 'wp-builder-locale' ),
		];

		$arrays['countries'] = [
			'BD' => 'Bangladesh',
			'BE' => 'Belgium',
			'BF' => 'Burkina Faso',
			'BG' => 'Bulgaria',
			'BA' => 'Bosnia and Herzegovina',
			'BB' => 'Barbados',
			'WF' => 'Wallis and Futuna',
			'BL' => 'Saint Barthelemy',
			'BM' => 'Bermuda',
			'BN' => 'Brunei',
			'BO' => 'Bolivia',
			'BH' => 'Bahrain',
			'BI' => 'Burundi',
			'BJ' => 'Benin',
			'BT' => 'Bhutan',
			'JM' => 'Jamaica',
			'BV' => 'Bouvet Island',
			'BW' => 'Botswana',
			'WS' => 'Samoa',
			'BQ' => 'Bonaire, Saint Eustatius and Saba ',
			'BR' => 'Brazil',
			'BS' => 'Bahamas',
			'JE' => 'Jersey',
			'BY' => 'Belarus',
			'BZ' => 'Belize',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'RS' => 'Serbia',
			'TL' => 'East Timor',
			'RE' => 'Reunion',
			'TM' => 'Turkmenistan',
			'TJ' => 'Tajikistan',
			'RO' => 'Romania',
			'TK' => 'Tokelau',
			'GW' => 'Guinea-Bissau',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'GR' => 'Greece',
			'GQ' => 'Equatorial Guinea',
			'GP' => 'Guadeloupe',
			'JP' => 'Japan',
			'GY' => 'Guyana',
			'GG' => 'Guernsey',
			'GF' => 'French Guiana',
			'GE' => 'Georgia',
			'GD' => 'Grenada',
			'GB' => 'United Kingdom',
			'GA' => 'Gabon',
			'SV' => 'El Salvador',
			'GN' => 'Guinea',
			'GM' => 'Gambia',
			'GL' => 'Greenland',
			'GI' => 'Gibraltar',
			'GH' => 'Ghana',
			'OM' => 'Oman',
			'TN' => 'Tunisia',
			'JO' => 'Jordan',
			'HR' => 'Croatia',
			'HT' => 'Haiti',
			'HU' => 'Hungary',
			'HK' => 'Hong Kong',
			'HN' => 'Honduras',
			'HM' => 'Heard Island and McDonald Islands',
			'VE' => 'Venezuela',
			'PR' => 'Puerto Rico',
			'PS' => 'Palestinian Territory',
			'PW' => 'Palau',
			'PT' => 'Portugal',
			'SJ' => 'Svalbard and Jan Mayen',
			'PY' => 'Paraguay',
			'IQ' => 'Iraq',
			'PA' => 'Panama',
			'PF' => 'French Polynesia',
			'PG' => 'Papua New Guinea',
			'PE' => 'Peru',
			'PK' => 'Pakistan',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PM' => 'Saint Pierre and Miquelon',
			'ZM' => 'Zambia',
			'EH' => 'Western Sahara',
			'EE' => 'Estonia',
			'EG' => 'Egypt',
			'ZA' => 'South Africa',
			'EC' => 'Ecuador',
			'IT' => 'Italy',
			'VN' => 'Vietnam',
			'SB' => 'Solomon Islands',
			'ET' => 'Ethiopia',
			'SO' => 'Somalia',
			'ZW' => 'Zimbabwe',
			'SA' => 'Saudi Arabia',
			'ES' => 'Spain',
			'ER' => 'Eritrea',
			'ME' => 'Montenegro',
			'MD' => 'Moldova',
			'MG' => 'Madagascar',
			'MF' => 'Saint Martin',
			'MA' => 'Morocco',
			'MC' => 'Monaco',
			'UZ' => 'Uzbekistan',
			'MM' => 'Myanmar',
			'ML' => 'Mali',
			'MO' => 'Macao',
			'MN' => 'Mongolia',
			'MH' => 'Marshall Islands',
			'MK' => 'Macedonia',
			'MU' => 'Mauritius',
			'MT' => 'Malta',
			'MW' => 'Malawi',
			'MV' => 'Maldives',
			'MQ' => 'Martinique',
			'MP' => 'Northern Mariana Islands',
			'MS' => 'Montserrat',
			'MR' => 'Mauritania',
			'IM' => 'Isle of Man',
			'UG' => 'Uganda',
			'TZ' => 'Tanzania',
			'MY' => 'Malaysia',
			'MX' => 'Mexico',
			'IL' => 'Israel',
			'FR' => 'France',
			'IO' => 'British Indian Ocean Territory',
			'SH' => 'Saint Helena',
			'FI' => 'Finland',
			'FJ' => 'Fiji',
			'FK' => 'Falkland Islands',
			'FM' => 'Micronesia',
			'FO' => 'Faroe Islands',
			'NI' => 'Nicaragua',
			'NL' => 'Netherlands',
			'NO' => 'Norway',
			'NA' => 'Namibia',
			'VU' => 'Vanuatu',
			'NC' => 'New Caledonia',
			'NE' => 'Niger',
			'NF' => 'Norfolk Island',
			'NG' => 'Nigeria',
			'NZ' => 'New Zealand',
			'NP' => 'Nepal',
			'NR' => 'Nauru',
			'NU' => 'Niue',
			'CK' => 'Cook Islands',
			'XK' => 'Kosovo',
			'CI' => 'Ivory Coast',
			'CH' => 'Switzerland',
			'CO' => 'Colombia',
			'CN' => 'China',
			'CM' => 'Cameroon',
			'CL' => 'Chile',
			'CC' => 'Cocos Islands',
			'CA' => 'Canada',
			'CG' => 'Republic of the Congo',
			'CF' => 'Central African Republic',
			'CD' => 'Democratic Republic of the Congo',
			'CZ' => 'Czech Republic',
			'CY' => 'Cyprus',
			'CX' => 'Christmas Island',
			'CR' => 'Costa Rica',
			'CW' => 'Curacao',
			'CV' => 'Cape Verde',
			'CU' => 'Cuba',
			'SZ' => 'Swaziland',
			'SY' => 'Syria',
			'SX' => 'Sint Maarten',
			'KG' => 'Kyrgyzstan',
			'KE' => 'Kenya',
			'SS' => 'South Sudan',
			'SR' => 'Suriname',
			'KI' => 'Kiribati',
			'KH' => 'Cambodia',
			'KN' => 'Saint Kitts and Nevis',
			'KM' => 'Comoros',
			'ST' => 'Sao Tome and Principe',
			'SK' => 'Slovakia',
			'KR' => 'South Korea',
			'SI' => 'Slovenia',
			'KP' => 'North Korea',
			'KW' => 'Kuwait',
			'SN' => 'Senegal',
			'SM' => 'San Marino',
			'SL' => 'Sierra Leone',
			'SC' => 'Seychelles',
			'KZ' => 'Kazakhstan',
			'KY' => 'Cayman Islands',
			'SG' => 'Singapore',
			'SE' => 'Sweden',
			'SD' => 'Sudan',
			'DO' => 'Dominican Republic',
			'DM' => 'Dominica',
			'DJ' => 'Djibouti',
			'DK' => 'Denmark',
			'VG' => 'British Virgin Islands',
			'DE' => 'Germany',
			'YE' => 'Yemen',
			'DZ' => 'Algeria',
			'US' => 'United States',
			'UY' => 'Uruguay',
			'YT' => 'Mayotte',
			'UM' => 'United States Minor Outlying Islands',
			'LB' => 'Lebanon',
			'LC' => 'Saint Lucia',
			'LA' => 'Laos',
			'TV' => 'Tuvalu',
			'TW' => 'Taiwan',
			'TT' => 'Trinidad and Tobago',
			'TR' => 'Turkey',
			'LK' => 'Sri Lanka',
			'LI' => 'Liechtenstein',
			'LV' => 'Latvia',
			'TO' => 'Tonga',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'LR' => 'Liberia',
			'LS' => 'Lesotho',
			'TH' => 'Thailand',
			'TF' => 'French Southern Territories',
			'TG' => 'Togo',
			'TD' => 'Chad',
			'TC' => 'Turks and Caicos Islands',
			'LY' => 'Libya',
			'VA' => 'Vatican',
			'VC' => 'Saint Vincent and the Grenadines',
			'AE' => 'United Arab Emirates',
			'AD' => 'Andorra',
			'AG' => 'Antigua and Barbuda',
			'AF' => 'Afghanistan',
			'AI' => 'Anguilla',
			'VI' => 'U.S. Virgin Islands',
			'IS' => 'Iceland',
			'IR' => 'Iran',
			'AM' => 'Armenia',
			'AL' => 'Albania',
			'AO' => 'Angola',
			'AQ' => 'Antarctica',
			'AS' => 'American Samoa',
			'AR' => 'Argentina',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AW' => 'Aruba',
			'IN' => 'India',
			'AX' => 'Aland Islands',
			'AZ' => 'Azerbaijan',
			'IE' => 'Ireland',
			'ID' => 'Indonesia',
			'UA' => 'Ukraine',
			'QA' => 'Qatar',
			'MZ' => 'Mozambique',
		];

		$arrays['column-classes'] = [
			'cj-is-1-widescreen' => '1/12 widescreen',
			'cj-is-2-widescreen' => '2/12 widescreen',
			'cj-is-3-widescreen' => '3/12 widescreen',
			'cj-is-4-widescreen' => '4/12 widescreen',
			'cj-is-5-widescreen' => '5/12 widescreen',
			'cj-is-6-widescreen' => '6/12 widescreen',
			'cj-is-7-widescreen' => '7/12 widescreen',
			'cj-is-8-widescreen' => '8/12 widescreen',
			'cj-is-9-widescreen' => '9/12 widescreen',
			'cj-is-10-widescreen' => '10/12 widescreen',
			'cj-is-11-widescreen' => '11/12 widescreen',
			'cj-is-12-widescreen' => '12/12 widescreen',

			'cj-is-1-desktop' => '1/12 desktop',
			'cj-is-2-desktop' => '2/12 desktop',
			'cj-is-3-desktop' => '3/12 desktop',
			'cj-is-4-desktop' => '4/12 desktop',
			'cj-is-5-desktop' => '5/12 desktop',
			'cj-is-6-desktop' => '6/12 desktop',
			'cj-is-7-desktop' => '7/12 desktop',
			'cj-is-8-desktop' => '8/12 desktop',
			'cj-is-9-desktop' => '9/12 desktop',
			'cj-is-10-desktop' => '10/12 desktop',
			'cj-is-11-desktop' => '11/12 desktop',
			'cj-is-12-desktop' => '12/12 desktop',

			'cj-is-1-tablet' => '1/12 tablet',
			'cj-is-2-tablet' => '2/12 tablet',
			'cj-is-3-tablet' => '3/12 tablet',
			'cj-is-4-tablet' => '4/12 tablet',
			'cj-is-5-tablet' => '5/12 tablet',
			'cj-is-6-tablet' => '6/12 tablet',
			'cj-is-7-tablet' => '7/12 tablet',
			'cj-is-8-tablet' => '8/12 tablet',
			'cj-is-9-tablet' => '9/12 tablet',
			'cj-is-10-tablet' => '10/12 tablet',
			'cj-is-11-tablet' => '11/12 tablet',
			'cj-is-12-tablet' => '12/12 tablet',

			'cj-is-1-mobile' => '1/12 mobile',
			'cj-is-2-mobile' => '2/12 mobile',
			'cj-is-3-mobile' => '3/12 mobile',
			'cj-is-4-mobile' => '4/12 mobile',
			'cj-is-5-mobile' => '5/12 mobile',
			'cj-is-6-mobile' => '6/12 mobile',
			'cj-is-7-mobile' => '7/12 mobile',
			'cj-is-8-mobile' => '8/12 mobile',
			'cj-is-9-mobile' => '9/12 mobile',
			'cj-is-10-mobile' => '10/12 mobile',
			'cj-is-11-mobile' => '11/12 mobile',
			'cj-is-12-mobile' => '12/12 mobile',
		];

		$arrays['currency-symbols'] = [
			'&#36;' => 'Dollar',
			'&#1583;.&#1573;' => 'UAE Dirham',
			'&#65;&#102;' => 'Afghani',
			'&#76;&#101;&#107;' => 'Lek',
			'&#75;&#122;' => 'Kwanza',
			'&#402;' => 'Netherlands Antillian Guilder | Aruban Guilder/Florin',
			'&#1084;&#1072;&#1085;' => 'Azerbaijanian Manat',
			'&#75;&#77;' => 'Convertible Mark',
			'&#2547;' => 'Taka',
			'.&#1583;.&#1576;' => 'Bahraini Dinar',
			'&#70;&#66;&#117;' => 'Burundi Franc',
			'&#36;&#98;' => 'Boliviano',
			'&#82;&#36;' => 'Brazilian Real',
			'&#78;&#117;&#46;' => 'Ngultrum',
			'&#80;' => 'Pula',
			'&#112;&#46;' => 'Belarusian Ruble',
			'&#66;&#90;&#36;' => 'Belize Dollar',
			'&#70;&#67;' => 'Congolese Franc',
			'&#67;&#72;&#70;' => 'Swiss Franc',
			'&#165;' => 'Yuan Renminbi | Yen',
			'&#8353;' => 'Costa Rican Colon',
			'&#8396;' => 'Cuban Peso',
			'&#75;&#269;' => 'Czech Koruna',
			'&#70;&#100;&#106;' => 'Djibouti Franc',
			'&#82;&#68;&#36;' => 'Dominican Peso',
			'&#1583;&#1580;' => 'Algerian Dinar',
			'&#66;&#114;' => 'Ethiopian Birr',
			'&#8364;' => 'Euro',
			'&#4314;' => 'Lari',
			'&#162;' => 'Ghana Cedi',
			'&#68;' => 'Dalasi',
			'&#70;&#71;' => 'Guinea Franc',
			'&#81;' => 'Quetzal',
			'&#107;&#110;' => 'Kuna',
			'&#71;' => 'Gourde',
			'&#70;&#116;' => 'Forint',
			'&#82;&#112;' => 'Rupiah',
			'&#8362;' => 'New Israeli Sheqel',
			'&#8377;' => 'Indian Rupee',
			'&#1593;.&#1583;' => 'Iraqi Dinar',
			'&#74;&#36;' => 'Jamaican Dollar',
			'&#74;&#68;' => 'Jordanian Dinar',
			'&#75;&#83;&#104;' => 'Kenyan Shilling',
			'&#1083;&#1074;' => 'Bulgarian Lev | Som | Tenge | Uzbekistan Sum',
			'&#6107;' => 'Riel',
			'&#67;&#70;' => 'Comoro Franc',
			'&#8361;' => 'North Korean Won | Won',
			'&#1583;.&#1603;' => 'Kuwaiti Dinar',
			'&#8365;' => 'Kip',
			'&#76;&#116;' => 'Lithuanian litas',
			'&#76;&#115;' => 'Latvian lats',
			'&#1604;.&#1583;' => 'Libyan Dinar',
			'&#1583;.&#1605;.' => 'Moroccan Dirham',
			'&#76;' => 'Lempira | Loti | Moldovan Leu | Lilangeni',
			'&#65;&#114;' => 'Malagasy Ariary',
			'&#1076;&#1077;&#1085;' => 'Denar',
			'&#75;' => 'Kyat | Kina',
			'&#8366;' => 'Tugrik',
			'&#77;&#79;&#80;&#36;' => 'Pataca',
			'&#85;&#77;' => 'Mauritanian ouguiya',
			'.&#1923;' => 'Rufiyaa',
			'&#77;&#75;' => 'Kwacha',
			'&#82;&#77;' => 'Malaysian Ringgit',
			'&#77;&#84;' => 'Mozambique Metical',
			'&#8358;' => 'Naira',
			'&#67;&#36;' => 'Cordoba Oro',
			'&#107;&#114;' => 'Danish Krone | Iceland Krona | Norwegian Krone | Swedish Krona',
			'&#66;&#47;&#46;' => 'Balboa',
			'&#83;&#47;&#46;' => 'Nuevo Sol',
			'&#8369;' => 'Philippine Peso',
			'&#8360;' => 'Sri Lanka Rupee | Mauritius Rupee | Nepalese Rupee | Pakistan Rupee| Seychelles Rupee',
			'&#122;&#322;' => 'Zloty',
			'&#71;&#115;' => 'Guarani',
			'&#108;&#101;&#105;' => 'Romanian Leu',
			'&#1044;&#1080;&#1085;&#46;' => 'Serbian Dinar',
			'&#1088;&#1091;&#1073;' => 'Russian Ruble',
			'&#1585;.&#1587;' => 'Rwanda Franc',
			'&#65020;' => 'Iranian Rial | Rial Omani | Qatari Rial| Saudi Riyal | Yemeni Rial',
			'&#163;' => 'Egyptian Pound | Falkland Islands Pound | Pound Sterling| Gibraltar Pound | Jersey pound| Lebanese Pound | Sudanese Pound | Saint Helena Pound | Syrian Pound',
			'&#76;&#101;' => 'Leone',
			'&#83;' => 'Somali Shilling',
			'&#68;&#98;' => 'São Tomé and Príncipe dobra',
			'&#3647;' => 'Baht',
			'&#84;&#74;&#83;' => 'Somoni',
			'&#109;' => 'Turkmenistan New Manat',
			'&#1583;.&#1578;' => 'Tunisian Dinar',
			'&#84;&#36;' => 'Pa’anga',
			'&#8356;' => 'Turkish Lira',
			'&#78;&#84;&#36;' => 'New Taiwan Dollar',
			'&#8372;' => 'Hryvnia',
			'&#85;&#83;&#104;' => 'Uganda Shilling',
			'&#36;&#85;' => 'Peso Uruguayo',
			'&#66;&#115;' => 'Bolivar',
			'&#8363;' => 'Dong',
			'&#86;&#84;' => 'Vatu',
			'&#87;&#83;&#36;' => 'Tala',
			'&#70;&#67;&#70;&#65;' => 'CFA Franc BEAC',
			'&#70;' => 'CFP Franc',
			'&#82;' => 'Rand',
			'&#90;&#75;' => 'Zambian kwacha',
			'&#90;&#36;' => 'Zimbabwe Dollar',
		];

		if( is_null( $type ) ) {
			return $arrays;
		} else {
			return $arrays[ $type ];
		}
	}

	public function getArrayValue( $key, $array, $default = '' ) {
		if( $this->isValidatedArray( $array, true, $key ) ) {

			if( $array[ $key ] != '' && $array[ $key ] != 'none' ) {
				return $array[ $key ];
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	}

	public function containsArray( $array ) {
		if( is_array( $array ) ) {
			foreach( $array as $value ) {
				if( is_array( $value ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function getUniqueArrayAssoc( $array, $return = [] ) {
		for( $x = 0; $x <= count( $array ); $x ++ ) {
			if( isset( $array[ $x ] ) && is_array( $array[ $x ] ) ) {
				$return = $this->getUniqueArrayAssoc( $array[ $x ], $return );
			} else {
				if( isset( $array[ $x ] ) ) {
					$return[] = $array[ $x ];
				}
			}
		}

		return array_unique( $return );
	}

	public function arrayInsert( &$array, $position, $insert ) {
		if( is_int( $position ) ) {
			array_splice( $array, $position, 0, $insert );
		} else {
			$pos = array_search( $position, array_keys( $array ) );
			$array = array_merge( array_slice( $array, 0, $pos ), $insert, array_slice( $array, $pos ) );
		}

		return $array;
	}

	public function arrayInterlace() {
		$args = func_get_args();
		$total = count( $args );
		if( $total < 2 ) {
			return false;
		}
		$i = 0;
		$j = 0;
		$arr = [];
		foreach( $args as $arg ) {
			foreach( $arg as $key => $v ) {
				$arr[ $key ] = $v;
			}
		}
		ksort( $arr );

		return $arr;
	}

	public function isValidArray( $var ) {
		$return = false;
		if( is_array( $var ) && ( ! in_array( 'none', $var ) || (isset( $var[0] ) && $var[0] != '')) ) {
			$return = true;
		}

		return $return;
	}

	public function getBlockGroupOption( $var, $array = [] ) {
		$return = [];
		$var = '_block_settings_' . $var;
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

	public function convertArrayToCss( $array, $tag = '' ) {
		unset( $array['custom-css-code'] );
		unset( $array['form-field-type-css-styles'] );
		unset( $array['custom-classes'] );
		$css_code = '';
		$tag_start = '';
		$tag_end = '';
		foreach( array_filter( $array ) as $key => $value ) {
			if( is_numeric( $value ) ) {
				$value = $value . 'px';
			}
			if( $key == 'background-image' ) {
				$value = "url('" . $value . "')";
			}
			$css_code .= $key . ':' . strtolower( $value ) . ';';
		}
		if( $tag != '' ) {
			$tag_start = $tag . '{';
			$tag_end = '}';
		}

		return $tag_start . $css_code . $tag_end;
	}

	public function objectToArray( $obj, &$arr ) {
		if( ! is_object( $obj ) && ! is_array( $obj ) ) {
			$arr = $obj;

			return $arr;
		}
		foreach( $obj as $key => $value ) {
			if( ! empty( $value ) ) {
				$arr[ $key ] = [];
				$this->objectToArray( $value, $arr[ $key ] );
			} else {
				$arr[ $key ] = $value;
			}
		}

		return $arr;
	}

	public function arrayToAttributes( $array ) {
		$return = [];
		if( is_array( $array ) && ! empty( $array ) ) {
			foreach( $array as $key => $value ) {
				$return[] = $key . '="' . $value . '"';
			}
		}

		return implode( ' ', $return );
	}

	public function inArray( $find, $objects = [] ) {

		if( empty( $objects ) ) {
			return false;
		}
		$find = ( ! is_array( $find )) ? [$find] : $find;
		if( ! is_array( $objects ) && ! is_array( $find ) ) {
			return false;
		}

		foreach( $find as $key => $value ) {
			if( in_array( $value, $objects ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * isValidatedArray()
	 *
	 * This function will check an array for multiple things
	 * 1. If an array is actually of type array.
	 * 2. If an array is empty or not.
	 * 3. If a particular key exists in an array or not.
	 *
	 * @param        $array     | Array
	 * @param bool   $check_key | If need to check a key in an array. Defaults to false
	 * @param string $array_key | If need to check array key, then specify the key. Defaults to empty string
	 *
	 * @return bool | true or false
	 *
	 * @since 1.0.0
	 */
	public function isValidatedArray( $array, $check_key = false, $array_key = '' ) {

		if( isset( $array ) && is_array( $array ) ) {
			if( ! empty( $array ) ) {
				if( $check_key && $array_key != '' ) {
					if( array_key_exists( $array_key, $array ) ) {
						return true;
					} else {
						return false;
					}
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Return false if an array is not empty but it has a key
	 * of 0 with no value in it.
	 *
	 * @param $array | array | Array to be checked
	 *
	 * @return bool | true or false
	 */
	public function validateZeroArrayValue( $array ) {

		if( ! $this->isValidatedArray( $array ) ) {
			return false;
		}

		$result = (array_key_exists( 0, $array ) && $array[0] != '') ? true : false;

		return $result;
	}

	public function createTimeRange( $start, $end, $interval = '30 mins', $format = '12', $show_both = false ) {
		$startTime = strtotime( $start );
		$endTime = strtotime( $end );
		$returnTimeFormat = ($format == '12') ? 'g:i A' : 'G:i';

		$current = time();
		$addTime = strtotime( '+' . $interval, $current );
		$diff = $addTime - $current;

		$return = [];
		$times = [];
		while( $startTime < $endTime ) {
			$times[] = date( $returnTimeFormat, $startTime );
			if( $show_both ) {
				$key = date( $returnTimeFormat, $startTime ) . ' - ' . date( $returnTimeFormat, strtotime( '+' . $interval, $startTime ) );
			} else {
				$key = date( $returnTimeFormat, $startTime );
			}
			$startTime += $diff;
			$return[ $key ] = $key;
		}
		$times[] = date( $returnTimeFormat, $startTime );

		return $return;
	}

	/**
	 * Use this function if you need to replace a word or a string inside
	 * an array. Works with multidimensional array as well.
	 *
	 * This would replace the string found inside an array everywhere, keys
	 * & values
	 *
	 * @param       $search  | String to search
	 * @param       $replace | String to replace
	 * @param array $array   | Array in which string would be replaced.
	 *
	 * @return false|mixed|string|void
	 *
	 * @since 1.0.0
	 */
	public function arrayReplaceString( $search, $replace, $array = [] ) {
		if( ! $this->isValidatedArray( $array ) ) {
			return;
		}

		$string = json_encode( $array ); // Convert array into string

		// Make sure the searched string exists in the array
		if( strpos( $string, $search ) !== false ) {
			$string = str_replace( $search, $replace, $string ); // Replace the string
			$array = json_decode( $string, true ); // Convert string to array again
		}

		return $array;
	}
}
