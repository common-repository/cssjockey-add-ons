<?php
global $current_user;
$user_info = [
	'avatar_url' => $this->helpers->default_avatar_url,
	'display_name' => __( 'Guest', 'wp-builder-locale' ),
];
if( is_user_logged_in() ) {
	$user_data = $this->helpers->userInfo( $current_user->ID );
	$user_info = [
		'avatar_url' => $user_data['gravatar'],
		'display_name' => $user_data['display_name'],
	];
}
?>
<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php if( $depth == 0 ) { ?>
        <span class="user-avatar">
            <img src="<?php echo $user_info['avatar_url'] ?>" alt="<?php echo $user_info['display_name'] ?>" title="<?php echo $user_info['display_name'] ?>">
        </span>
        <span class="link-title">
            <?php echo $user_info['display_name'] . $arrow_down ?>
        </span>
	<?php } ?>
	<?php echo $args->after ?>
</a>
