<div class="cj-clear"></div>
<div class="cj-columns cj-is-multiline cj-is-mobile cj-mt-5" style="border-top: 0;">
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<label class="cj-label"><?php _e( 'Animation Type:', 'wp-builder-locale' ) ?></label>
				<select name="<?php echo $option['id'] . '[aos][animation]' ?>" class="selectize">
					<?php
					$aos_selected = (isset( $option['default']['aos']['animation'] )) ? $option['default']['aos']['animation'] : '';
					echo $this->dropdownHtml( $this->aosAnimationsArray(), $aos_selected );
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<label class="cj-label"><?php _e( 'Easing:', 'wp-builder-locale' ) ?></label>
				<select name="<?php echo $option['id'] . '[aos][easing]' ?>" class="selectize">
					<?php
					$aos_selected = (isset( $option['default']['aos']['easing'] )) ? $option['default']['aos']['easing'] : '';
					echo $this->dropdownHtml( $this->aosEasingArray(), $aos_selected );
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<label class="cj-label"><?php _e( 'Duration (ms):', 'wp-builder-locale' ) ?></label>
				<input name="<?php echo $option['id'] . '[aos][duration]' ?>" type="number" class="cj-input" min="0" value="<?php echo (isset( $option['default']['aos']['duration'] )) ? $option['default']['aos']['duration'] : 250; ?>"/>
			</div>
		</div>
	</div>
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<div class="cj-field">
					<div class="cj-control">
						<label class="cj-label"><?php _e( 'Delay (ms):', 'wp-builder-locale' ) ?></label>
						<input name="<?php echo $option['id'] . '[aos][delay]' ?>" type="number" class="cj-input" min="0" value="<?php echo (isset( $option['default']['aos']['delay'] )) ? $option['default']['aos']['delay'] : 0; ?>"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>