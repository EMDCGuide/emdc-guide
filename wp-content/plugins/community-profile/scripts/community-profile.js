/**
 * These are scripts for the community profile feed.
 * This file get's bundled into frontend-bundle.js and loaded on all front end pages
 */
jQuery(function($) {
  $('.copr-js-hide').hide();
  $('.copr-js-show').show();
  $('.copr-answer-textarea').val('');
  /**
   * Set up the deletion of answers
   */
  $(document).on('click', '.copr-delete-answer', function() {
    const form = $(this).closest('form');
    const confirmMsg = form.attr('data-really-message');
    const payload = `${form.serialize()}&is_ajax=true`;
    const id = form.find('input[name="answer_id"]').first().val();
    const answerWrapper = '#copr-single-answer-' + id;
    if (confirm(confirmMsg)) {
      $.post(form.attr('action'), payload).done(function(data) {
        if (data.success) {
          $(answerWrapper).slideUp('slow');
        }
      });
    }
    return false;
  });
  /**
   * Set up the editing of answers
   */
  $(document).on('click', '.copr-edit-link', function() {
    const link = $(this);
    const parent = link.closest('.copr-single-answer');
    const showingText = link.attr('data-showing-text');
    const hidingText = link.attr('data-hiding-text');
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
  /**
   * Set up the submission of answer's editing form
   */
  $(document).on('submit', 'form.copr-edit-answer', function() {
    const form = $(this);
    const parent = form.closest('.copr-single-answer');
    const payload = `${form.serialize()}&is_ajax=true`;
    const answerEle = form.find('input[name="answer"]').first();
    const data = form.serializeArray();
    let answer = '';
    for (var i = 0; i < data.length; i++) {
      if (data[i]['name'] === 'answer') {
        answer = data[i]['value'];
      }
    }
    $.post(form.attr('action'), payload).done(function(data) {
      if (data.success) {
        const text = parent.find('.copr-answer-text').first();
        text.text(answer);
        parent.find('.copr-edit-link').first().click();
      }
    });
    return false;
  });
  /**
   * Set up the filter
   */
  const selector = $('#copr-section-filter select');
  $('.copr-section-wrapper').each(function() {
    const title = $(this).attr('data-title');
    const tag = $(this).attr('data-tag');
    selector.append('<option value="' + tag + '">' + title + '</option>');
  });
  selector.on('change', function() {
    const value = $(this).val();
    if (value === 'all') {
      $('.copr-section-wrapper').show();
    } else {
      $('.copr-section-wrapper').hide();
      $('#section-' + value).show();
    }
  });
  /**
   * Handle a person's response
   */
  $('form.copr-my-response').submit(function() {
    const form = $(this);
    const answerField = form.find('*[name="answer"]').first();
    const answerErrorHolder = form.find(`.copr-answer-error`).first();
    const parent = form.closest('.copr-my-response-wrapper');
    const payload = `${form.serialize()}&is_ajax=true`;
    const submit = form.find('input[type="submit"]').first();
    const data = form.serializeArray();
    let groupId = '';
    for (var i = 0; i < data.length; i++) {
      if (data[i]['name'] === 'group_id') {
        groupId = data[i]['value'];
      }
    }
    submit
      .val(submit.attr('data-saving'))
      .prop('disabled', 'disabled')
      .addClass('copr-disabled');
    $.post(form.attr('action'), payload).done(function(data) {
      if (data.success) {
        const templatePayload = `action=copr_get_template&group_id=${groupId}&answer_id=${data.data.id}&template_name=_single_answer`;
        $.get(form.attr('action'), templatePayload).done(function(html) {
          submit
            .val(submit.attr('data-save'))
            .prop('disabled', '')
            .removeClass('copr-disabled');
          parent.slideUp('slow', function() {
            const ele = $(html);
            ele.find('.copr-js-hide').hide();
            ele.find('.copr-js-show').css('display', 'block');
            ele.find('a.copr-js-show').css('display', 'inline-block');
            parent.replaceWith(ele);
          });
        });
      } else {
        data.errors.forEach(function(error) {
          const field = form.find(`*[name="${error.field}"]`).first();
          if (field.length > 0) {
              field.addClass('copr-errored');
          }
          const errorHolder = form.find(`.copr-${error.field}-error`).first();
          if (errorHolder.length > 0) {
              errorHolder.text(error.error).removeClass('copr-hidden');
          }
        });
        submit
          .val(submit.attr('data-save'))
          .prop('disabled', '')
          .removeClass('copr-disabled');
      }
    }).fail(function() {
      submit
        .val(submit.attr('data-save'))
        .prop('disabled', '')
        .removeClass('copr-disabled');
    });
    return false;
  });
});
