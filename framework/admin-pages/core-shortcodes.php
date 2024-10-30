<?php
global $shortcode_tags;
$cjwpbldr_shortcodes = array();
if( is_array( $shortcode_tags ) && ! empty( $shortcode_tags ) ) {
	foreach( $shortcode_tags as $shortcode_key => $shortcode ) {
		if( strpos( $shortcode_key, 'cjwpbldr' ) === 0 ) {
			$shortcode_info = (array) $shortcode[0];
			if( isset( $shortcode_info['defaults'] ) ) {
				if( isset( $shortcode_info['defaults']['info']['group'] ) ) {
					$group = $shortcode_info['defaults']['info']['group'];
				} else {
					$group = __( 'MISC', 'wp-builder-locale' );
				}
				$cjwpbldr_shortcodes[ $group ][ $shortcode_key . '_shortcode' ] = $shortcode_info['defaults'];
			}
		}
	}
}
?>
<div class="cj-columns cj-is-multiline cj-is-mobile">
    <div class="cj-column cj-is-3-widescreen cj-is-3-desktop cj-is-12-tablet cj-is-12-mobile">
        <div class="cj-box cj-br-0">
            <div class="cj-menu">
				<?php if( is_array( $cjwpbldr_shortcodes ) && ! empty( $cjwpbldr_shortcodes ) ) { ?>
					<?php
					$count = 0;
					foreach( $cjwpbldr_shortcodes as $group => $shortcodes ) {
						$count ++;
						?>
                        <p class="cj-menu-label <?php echo ($count > 1) ? 'cj-mt-20' : ''; ?>"><?php echo $group ?></p>
						<?php if( is_array( $shortcodes ) && ! empty( $shortcodes ) ) { ?>
                            <ul class="cj-menu-list">
								<?php foreach( $shortcodes as $shortcode => $shortcode_details ) { ?>
                                    <li class="cj-bg-light">
                                        <a class="cj-color-dark" href="#" data-toggle="class" data-target="#shortcode-<?php echo $shortcode; ?>" data-toggle-group=".shortcode-info" data-classes="cj-is-hidden">
											<?php echo $shortcode_details['info']['name'] ?>
                                            <br>
                                            <small class="cj-color-dark cj-opacity-50"><?php echo $shortcode_details['info']['description'] ?></small>
                                        </a>
                                    </li>
								<?php } ?>
                            </ul>
						<?php } ?>
					<?php } ?>
				<?php } ?>
            </div>
        </div>
    </div>
    <div class="cj-column cj-is-9-widescreen cj-is-9-desktop cj-is-12-tablet cj-is-12-mobile">
        <div class="cj-box cj-br-0">
			<?php

			if( is_array( $cjwpbldr_shortcodes ) && ! empty( $cjwpbldr_shortcodes ) ) {
				$shortcode_count = 0;
				?>
				<?php foreach( $cjwpbldr_shortcodes as $group => $shortcodes ) { ?>
					<?php if( is_array( $shortcodes ) && ! empty( $shortcodes ) ) { ?>
						<?php
						foreach( $shortcodes as $shortcode => $shortcode_details ) {
							$shortcode_count ++;
							$shortcode_tag = $this->helpers->generateShortcodeTag( $shortcode_details );

							?>
                            <div id="shortcode-<?php echo $shortcode; ?>" class="cj-content shortcode-info <?php echo ($shortcode_count > 1) ? 'cj-is-hidden' : ''; ?>">
                                <h3 class="cj-m-0 cj-mb-5"><b><?php echo $shortcode_details['info']['name'] ?></b></h3>
                                <p class="cj-opacity-70"><?php echo $shortcode_details['info']['description'] ?></p>
                                <hr class="cj-mt-15 cj-mb-25">
                                <div class="cj-field cj-is-grouped">
                                    <div class="cj-control cj-is-expanded">
                                        <pre class="cj-fs-16"><?php echo $shortcode_tag; ?></pre>
                                    </div>
                                    <div class="cj-control">
                                        <button data-clipboard-text='<?php echo $shortcode_tag; ?>' class="cj-button cj-is-light"><i class="fa fa-copy"></i></button>
                                    </div>
                                    <div class="cj-control">
                                        <a href="<?php echo $this->helpers->queryString( $this->helpers->site_url ) . 'preview=cjwpbldr-shortcode&shortcode=' . $shortcode_details['info']['tag'] ?>" target="_blank" class="cj-button cj-is-light"><i class="fa fa-eye"></i></a>
                                    </div>
                                </div>
                                <hr class="cj-mt-25 cj-mb-15">
                                <table class="cj-table cj-is-bordered">
                                    <thead>
                                    <tr>
                                        <th width="20%" class="cj-bg-light"><?php _e( 'Attribute', 'wp-builder-locale' ) ?></th>
                                        <th width="40%" class="cj-bg-light"><?php _e( 'Info', 'wp-builder-locale' ) ?></th>
                                        <th width="40%" class="cj-bg-light"><?php _e( 'Default', 'wp-builder-locale' ) ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php foreach( $shortcode_details['options'] as $option_key => $option_value ) { ?>
                                        <tr>
                                            <td><?php echo $option_value['label'] ?></td>
                                            <td><?php echo $option_value['info'] ?></td>
                                            <td><?php echo $option_value['default'] ?></td>
                                        </tr>
									<?php } ?>
                                    </tbody>
                                </table>
                            </div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
        </div>
    </div>
</div>
