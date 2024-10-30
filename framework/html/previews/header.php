<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
    </style>
</head>
<?php
$previews = (isset( $_GET['cjwpbldr-action'] )) ? ' cjwpbldr-previews ' : '';
?>
<body <?php body_class( ' cjwpbldr-template ' . $previews ) ?>>

