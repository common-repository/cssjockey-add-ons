<?php
$screen_shot = $class->info['screenshot'];
$source_image_url = str_replace( 'screenshot', 'source', $screen_shot );
$source_file_path = str_replace( home_url(), ABSPATH, $source_image_url );
if( ! file_exists( $source_file_path ) ) {
	$source_file_path = str_replace( '.jpg', '.png', $source_file_path );
}

if( isset( $_GET['screenshot'] ) && $_GET['screenshot'] == 1 ) {
	echo '<style type="text/css">body{background: #ededed;}</style>';
}
if( isset( $_GET['source'] ) && $_GET['source'] == 1 ) {
	$tools_url = $this->helpers->config( 'plugin-info', 'cjwpbldr_tools_url' );

	$screenshot_url = $this->helpers->queryString( $this->helpers->currentUrl( true ) ) . 'cjwpbldr-action=screenshot-ui-block&id=' . $post_info['ID'];
	$download_screenshot_url = $tools_url . '/browser-shot?screen=lg&file_name=screenshot&url=' . urlencode( $screenshot_url );
	$current_url = $this->helpers->currentUrl();
	$reset_block_url = $this->helpers->queryString( home_url() ) . 'cjwpbldr-action=reset-ui-block&id=' . $post_info['ID'] . '&redirect=' . urlencode( $current_url );
	$save_screenshot_url = $this->helpers->queryString( site_url() ) . 'cjwpbldr-dev=create-block-screenshots&id=' . $post_info['ID'] . '&redirect=' . urlencode( $this->helpers->currentUrl() );
	$preview_edit_block_url = '';
	if( $this->helpers->containString( 'preview', $current_url ) ) {
		$preview_edit_block_url = str_replace( 'preview', 'edit', $current_url );
	} else {
		$preview_edit_block_url = str_replace( 'edit', 'preview', $current_url );
	}
	?>
    <div class="cssjockey-ui">
        <hr class="cj-mt-30 cj-mb-30">
        <div class="cj-text-center">
            <div class="cj-field cj-has-addons cj-has-addons-centered">
                <div class="cj-control">
                    <span class="cj-button cj-is-dark">SCREENSHOT : </span>
                </div>
                <div class="cj-control">
                    <button class="take-screenshot cj-button cj-is-primary cj-tooltip" data-tooltip="Show Screenshot Window"><i class="fa fa-eye"></i></button>
                </div>
                <div class="cj-control">
                    <a href="<?php echo $download_screenshot_url; ?>" class="cj-button cj-is-warning cj-tooltip" data-tooltip="Download Screenshot"><i class="fa fa-download"></i></a>
                </div>
                <div class="cj-control">
                    <a href="<?php echo $save_screenshot_url; ?>" class="cj-button cj-is-success cj-tooltip" data-tooltip="Save Screenshot"><i class="fa fa-save"></i></a>
                </div>
                <div class="cj-control">
                    <span class="cj-button cj-is-dark">BLOCK : </span>
                </div>
				<?php if( $this->helpers->containString( 'preview', $current_url ) ) {
					?>
                    <div class="cj-control">
                        <a href="<?php echo $preview_edit_block_url; ?>" class="cj-button cj-is-info cj-tooltip" data-tooltip="Enable Inline Editing"><i class="fa fa-edit"></i></a>
                    </div>
				<?php } else { ?>
                    <div class="cj-control">
                        <a href="<?php echo $preview_edit_block_url; ?>" class="cj-button cj-is-info cj-tooltip" data-tooltip="Disable Inline Editing"><i class="fa fa-eye"></i></a>
                    </div>
				<?php } ?>
                <div class="cj-control">
                    <a href="<?php echo $reset_block_url; ?>" class="cj-button cj-is-danger cj-tooltip" data-tooltip="Reset Block Settings"><i class="fa fa-sync"></i></a>
                </div>
            </div>


        </div>
    </div>
	<?php

	echo '<div class="cssjockey-ui">
	<hr class="cj-mt-30 cj-mb-30">
	<div class="cj-text-center">
		<br><div class="cj-mb-10 cj-text-bold">POST Title: ' . $post_info['post_title'] . ' | POST ID: ' . $post_info['ID'] . ' | BLOCK ID: ' . $class->info['id'] . '</div></div></div>';
	if( file_exists( $source_file_path ) ) {
		echo '<div class="cssjockey-ui"><hr class="cj-mt-30 cj-mb-30"><div class="cj-text-center"><div class="cj-mb-10 cj-text-bold">SOURCE</div><img src="' . str_replace( 'jpg', 'png', $source_image_url ) . '"></div></div>';
	} else {
		echo '<div class="cssjockey-ui"><hr class="cj-mt-30 cj-mb-30"><div class="cj-text-center"><div class="cj-mb-10 cj-text-bold">SOURCE</div><p class="cj-color-danger">Source file not found.</p></div></div>';
	}
	echo '<div class="cssjockey-ui"><hr class="cj-mt-30 cj-mb-30"><div class="cj-text-center"><div class="cj-mb-10 cj-text-bold">PREVIEW IMAGE</div><img src="' . $screen_shot . '"></div></div>';
	echo '<br><br><br><br><br>';
}
?>

<script type="text/javascript">
  jQuery(document).ready(function ($) {

    $('button.take-screenshot').on('click', function () {
      let loc = window.location.href
      loc = loc.replace('&source=1', '&screenshot=1')
      loc = loc.replace('edit', 'preview')
      let height = $('.cjwpbldr-ui-block.cjwpbldr-ui-block-outer').innerHeight()
      if (height < 900) {
        height = 950
      }
      let myWindow
      myWindow = window.open(loc, '', 'width=1440, height=' + height)
      myWindow.resizeTo(1440, height)
      myWindow.focus()
    })
	  <?php if($this->helpers->isLocal()){ ?>
    let seo_errors = []
    $('.cjwpbldr-ui-block').find('a').each(function () {
      let $a = $(this)
      let title = $a.attr('title')
      if (!$a.hasClass('cj-edit-post-link')) {
        if (title === undefined) {
          seo_errors.push('-- LINK: Missing title tag : ' + $a.html().toString())
        }
        if (title !== undefined && title === '') {
          seo_errors.push('-- LINK: Blank title tag : ' + $a.html().toString())
        }
      }
    })

    $('.cjwpbldr-ui-block').find('img').each(function () {
      let $img = $(this)
      let alt_tag = $img.attr('alt')
      if (alt_tag === undefined) {
        seo_errors.push('-- IMAGE: Missing alt tag : ' + $img.attr('src'))
      }
      if (alt_tag !== undefined && alt_tag === '') {
        seo_errors.push('-- IMAGE: Blank alt tag : ' + $img.attr('src'))
      }
      let title_tag = $img.attr('title')
      if (title_tag === undefined) {
        seo_errors.push('-- IMAGE: Missing title tag : ' + $img.attr('src'))
      }
      if (title_tag !== undefined && title_tag === '') {
        seo_errors.push('-- IMAGE: Blank alt tag : ' + $img.attr('src'))
      }
    })

    if ($('.cjwpbldr-ui-block').html().search('//placehold.it') > -1) {
      seo_errors.push('-- Do not use placeholder images')
    }

    if (seo_errors.length > 0) {
      swal('BLOCK ERRORS!', 'Found some errors in this block, please check console', 'error')
      console.warn('================================================================================')
      console.warn('== BLOCK ERRORS ================================================================')
      console.warn('================================================================================')
      for (let i = 0; i < seo_errors.length; i++) {
        console.log(seo_errors[i])
      }
      console.warn('================================================================================')
    }

	  <?php } ?>

  })
</script>
