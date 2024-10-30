jQuery(document).ready(function ($) {
  $(window).load(function () {
    let edit_template_url = $('#cjwpbldr-post-template-id').val()
    let edit_template_preview_url = $('#cjwpbldr-post-template-preview-url').val()
    if (edit_template_url !== '') {
      let html = ''
      html += '<a target="_blank" href="' + edit_template_url + '" class="cj-button cj-is-primary cj-is-small">Launch Template Editor</a>'
      
      let classic_html = ''
      classic_html += '<a target="_blank" href="' + edit_template_url + '" class="cj-button cj-is-primary">Launch Template Editor</a>'
      
      $('.edit-post-header__toolbar').append('<span class="cssjockey-ui">' + html + '</span>')
      $('#titlediv').append('<div class="cssjockey-ui"><div class="cj-mv-10">' + classic_html + '</div></div>')
      $('.edit-post-visual-editor.editor-styles-wrapper').html('<iframe frameborder="0" src="' + edit_template_preview_url + '" style="width: 100%; height: 80vh;">')
      $('#postdivrich').html('<iframe frameborder="0" src="' + edit_template_preview_url + '" style="width: 100%; height: 80vh;">')
    }
  })
})