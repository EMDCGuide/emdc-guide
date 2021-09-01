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
  $('.copr-edit-link').on('click', function() {
    var link = $(this);
    var parent = link.closest('.copr-single-answer');
    var showingText = link.attr('data-showing-text');
    var hidingText = link.attr('data-hiding-text');
    if (parent.hasClass('is-editing')) {
      parent.find('.copr-answer-text').first().show();
      parent.find('.copr-edit-answer').first().hide();
      parent.removeClass('is-editing');
      link.html('<span class="dashicons dashicons-edit"></span>' + hidingText);
    } else {
      parent.find('.copr-answer-text').first().hide();
      parent.find('.copr-edit-answer').first().show();
      parent.addClass('is-editing');
      link.html('<span class="dashicons dashicons-edit"></span> ' + showingText);
    }
    return false;
  });
});
