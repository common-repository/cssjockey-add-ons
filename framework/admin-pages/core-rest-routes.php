<?php

$response = $this->helpers->wpRemoteGet( site_url( 'wp-json' ) );
$routes = json_decode( $response, 1 );
$routes_array = [];
$count = 0;
if( is_array( $routes ) && ! empty( $routes ) ) {
	foreach( $routes['routes'] as $key => $route ) {
		$methods = $route['methods'];
		if( is_array( $methods ) && ! empty( $methods ) ) {
			foreach( $methods as $method_key => $method_value ) {
				if( $this->helpers->containString( 'cjwpbldr', $key ) ) {
					$count ++;
					//$routes_array[ $key ][ $method_value ] = $route['_links']['self'];
					$routes_array['routes'][ $key ][ $method_value ] = $route['_links']['self'];
				}
			}
		}
	}
}

if( isset( $routes_array['routes'] ) && ! empty( $routes_array['routes'] ) ) {
	foreach( $routes_array['routes'] as $route => $methods ) {
		$form_options = [];
		$form_options['sub-heading'] = [
			'id' => 'id',
			'type' => 'sub-heading',
			'label' => '',
			'info' => '',
			'default' => $route
		];
		if( is_array( $methods ) && ! empty( $methods ) ) {
			foreach( $methods as $method_key => $method_value ) {
				$form_options[ $route ] = [
					'id' => 'id',
					'type' => 'info',
					'label' => $method_key,
					'info' => '',
					'default' => $method_value
				];
			}
		}
		echo '<div class="cj-mt-30">';
		echo $this->helpers->renderAdminForm( $form_options );
		echo '</div>';
	}
}



