<div class="cj-columns cj-is-multiline cj-is-mobile" style="border-top: 0;">
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<label class="cj-label"><?php _e( 'Animation Type:', 'wp-builder-locale' ) ?></label>
				<select name="<?php echo $option['id'] . '[animation]' ?>" class="selectize">
					<?php
					$aos_selected = (isset( $option['default']['animation'] )) ? $option['default']['animation'] : '';
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
				<select name="<?php echo $option['id'] . '[easing]' ?>" class="selectize">
					<?php
					$aos_selected = (isset( $option['default']['easing'] )) ? $option['default']['easing'] : '';
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
				<input name="<?php echo $option['id'] . '[duration]' ?>" type="number" class="cj-input" min="0" value="<?php echo (isset( $option['default']['duration'] )) ? $option['default']['duration'] : 250; ?>"/>
			</div>
		</div>
	</div>
	<div class="cj-column cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile cj-pb-0">
		<div class="cj-field">
			<div class="cj-control">
				<div class="cj-field">
					<div class="cj-control">
						<label class="cj-label"><?php _e( 'Delay (ms):', 'wp-builder-locale' ) ?></label>
						<input name="<?php echo $option['id'] . '[delay]' ?>" type="number" class="cj-input" min="0" value="<?php echo (isset( $option['default']['delay'] )) ? $option['default']['delay'] : 0; ?>"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>