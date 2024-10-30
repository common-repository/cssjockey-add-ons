<?php
$block_id = $instance['id'];
$block_info = $this->helpers->postInfo( $block_id );
if( isset( $block_info['_ui_block_class_name'] ) && class_exists( $block_info['_ui_block_class_name'] ) ) {
	$class = $block_info['_ui_block_class_name'];
	$class = $class::getInstance();
	echo $this->helpers->renderUiBlock( $block_info, $class->info );
} else {
	echo '';
}
