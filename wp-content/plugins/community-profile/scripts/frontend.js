// This script is loaded both on the frontend page and in the Visual Builder.

jQuery(function($) {
  if ($('.copr-questions-wrapper').length > 0) {
    /**
     * Do some prework
     */
    $('.copr-questions-wrapper').each(function() {
      const wrapper = $(this);
      const children = wrapper.children();
      const displayed = children.not('.copr-hidden');
      const current = displayed.attr('data-number');
      const total = children.length;
      wrapper.attr('data-current', current);
      wrapper.attr('data-total', total);
      children.first().find('.copr-previous').first().addClass('copr-disabled');
      children.last().find('.copr-next').first().addClass('copr-disabled');
    });
    /**
     * Handle next button
     */
    $('.copr-questions-wrapper .copr-next').on('click', function() {
      const link = $(this);
      if (link.hasClass('copr-disabled')) {
        return false;
      }
      const parent = link.closest('.copr-questions-wrapper');
      const current = parseInt(parent.attr('data-current'), 10);
      const total = parseInt(parent.attr('data-total'), 10);
      const next = current + 1;
      parent.find(`div[data-number="${current}"]`).fadeOut('slow', function() {
        parent.find(`div[data-number="${next}"]`).fadeIn('slow');
        parent.attr('data-current', next);
      });
      return false;
    });
    /**
     * Handle previous button
     */
    $('.copr-questions-wrapper .copr-previous').on('click', function() {
      const link = $(this);
      if (link.hasClass('copr-disabled')) {
        return false;
      }
      const parent = link.closest('.copr-questions-wrapper');
      const current = parseInt(parent.attr('data-current'), 10);
      const total = parseInt(parent.attr('data-total'), 10);
      const prev = current - 1;
      parent.find(`div[data-number="${current}"]`).fadeOut('slow', function() {
        parent.find(`div[data-number="${prev}"]`).fadeIn('slow');
        parent.attr('data-current', prev);
      });
      return false;
    });
    /**
     * Handle form submissions
     */
    $('.copr-question-field-wrapper form').submit(function() {
      const form = $(this);
      const payload = `${form.serialize()}&is_ajax=true`;
      const submit = form.find('input[type="submit"]').first();
      const next = form.find('.copr-next').first();
      submit
        .val(submit.attr('data-saving'))
        .prop('disabled', 'disabled')
        .addClass('copr-disabled');
      $.post(form.attr('action'), payload).done(function(data) {
        submit
          .val(submit.attr('data-save'))
          .prop('disabled', '')
          .removeClass('copr-disabled');
        if (data.success) {
          form.find(`input, textarea`).removeClass('copr-errored');
          form.find(`.copr-error-message`).addClass('copr-hidden').text('');
          next.click();
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
        }
      });
      return false;
    });
  }
});
