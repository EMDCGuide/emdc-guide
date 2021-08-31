/**
 * These are scripts for the community profile feed.
 */
jQuery(function($) {
  $('.copr-js-hide').hide();
  $('.copr-js-show').show();
  $('.copr-delete-answer').on('click', function() {
    var form = $(this).closest('form');
    var payload = `${form.serialize()}&is_ajax=true`;
    var id = form.find('input[name="answer_id"]').first().val();
    var answerWrapper = '#copr-single-answer-' + id;
    $.post(form.attr('action'), payload).done(function(data) {
      if (data.success) {
        $(answerWrapper).slideUp('slow');
      }
    });
    return false;
  });
});
