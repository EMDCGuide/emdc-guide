/**
 * These are scripts for the community profile feed.
 */
jQuery(function($) {
  $('.copr-js-hide').hide();
  $('.copr-js-show').show();
  $('.copr-delete-answer').on('click', function() {
    var form = $(this).closest('form');
    var confirmMsg = form.attr('data-really-message');
    var payload = `${form.serialize()}&is_ajax=true`;
    var id = form.find('input[name="answer_id"]').first().val();
    var answerWrapper = '#copr-single-answer-' + id;
    if (confirm(confirmMsg)) {
      $.post(form.attr('action'), payload).done(function(data) {
        if (data.success) {
          $(answerWrapper).slideUp('slow');
        }
      });
    }
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
  $('form.copr-edit-answer').on('submit', function() {
    var form = $(this);
    var parent = form.closest('.copr-single-answer');
    var payload = `${form.serialize()}&is_ajax=true`;
    var answerEle = form.find('input[name="answer"]').first();
    var data = form.serializeArray();
    var answer = '';
    for (var i = 0; i < data.length; i++) {
      if (data[i]['name'] === 'answer') {
        answer = data[i]['value'];
      }
    }
    $.post(form.attr('action'), payload).done(function(data) {
      if (data.success) {
        var text = parent.find('.copr-answer-text').first();
        text.text(answer);
        parent.find('.copr-edit-link').first().click();
      }
    });
    return false;
  });
});
