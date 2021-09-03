// This script is loaded both on the frontend page and in the Visual Builder.

jQuery(function($) {
  /**
   * Submit the form.
   *
   * @param  {object}   $form     The JQuery form object
   * @param  {object}   $submit   The JQuery submit button object
   * @param  {function} success   A callback called on success
   * @return {void}
   */
  function submitForm($form, $submit, success) {
    const payload = `${$form.serialize()}&is_ajax=true`;
    const $parent = $form.closest('.copr-question-form').first();
    const $errorHolder = $parent.find('.copr-form-error').first();
    $submit
      .val($submit.attr('data-saving'))
      .prop('disabled', 'disabled')
      .addClass('copr-disabled');
    $.post($form.attr('action'), payload).done(function(data) {
      $submit
        .val($submit.attr('data-save'))
        .prop('disabled', '')
        .removeClass('copr-disabled');
      if (data.success) {
        $errorHolder.html('').hide();
        success(data);
      } else {
        data.errors.forEach(function(error) {
          const $field = $form.find(`*[name="${error.field}"]`).first();
          if ($field.length > 0) {
              $field.addClass('copr-errored');
          }
          const $errorFieldHolder = $form.find(`.copr-${error.field}-error`).first();
          if ($errorFieldHolder.length > 0) {
              $errorFieldHolder.text(error.error).removeClass('copr-hidden');
          }
        });
      }
    }).fail(function() {
      $submit
        .val($submit.attr('data-save'))
        .prop('disabled', '')
        .removeClass('copr-disabled');
      $errorHolder.html(`<p>${$form.attr('data-error-message')}</p>`).show();
    });
  }

  if ($('.copr-questions-wrapper').length > 0) {
    /**
     * Do some prework
     */
    $('.copr-questions-wrapper').each(function() {
      const $wrapper = $(this);
      const $children = $wrapper.children();
      const $displayed = $children.not(':first').not('.copr-hidden');
      const current = $displayed.attr('data-number');
      const total = $children.length;
      // Subtract one for the group selector
      $wrapper.attr('data-current', current).attr('data-total', (total - 1));
      $children.eq(1).find('.copr-previous').first().addClass('copr-disabled');
      $children.last().find('.copr-next').first().addClass('copr-disabled');
    });
    /**
     * Handle next button
     */
    $('.copr-questions-wrapper .copr-next').on('click', function() {
      const $link = $(this);
      if ($link.hasClass('copr-disabled')) {
        return false;
      }
      const $parent = $link.closest('.copr-questions-wrapper');
      const current = parseInt($parent.attr('data-current'), 10);
      const total = parseInt($parent.attr('data-total'), 10);
      const next = current + 1;
      $parent.find(`div[data-number="${current}"]`).fadeOut('slow', function() {
        $parent.find(`div[data-number="${next}"]`).fadeIn('slow');
        $parent.attr('data-current', next);
      });
      return false;
    });
    /**
     * Handle previous button
     */
    $('.copr-questions-wrapper .copr-previous').on('click', function() {
      const $link = $(this);
      if ($link.hasClass('copr-disabled')) {
        return false;
      }
      const $parent = $link.closest('.copr-questions-wrapper');
      const current = parseInt($parent.attr('data-current'), 10);
      const total = parseInt($parent.attr('data-total'), 10);
      const prev = current - 1;
      $parent.find(`div[data-number="${current}"]`).fadeOut('slow', function() {
        $parent.find(`div[data-number="${prev}"]`).fadeIn('slow');
        $parent.attr('data-current', prev);
      });
      return false;
    });
    /**
     * Handle form submissions
     */
    $('.copr-question-field-wrapper form').submit(function() {
      const $form = $(this);
      const $submit = $form.find('input[type="submit"]').first();
      const $next = $form.find('.copr-next').first();
      const success = function() {
        $form.find(`input, textarea`).removeClass('copr-errored');
        $form.find(`.copr-error-message`).addClass('copr-hidden').text('');
        $next.click();
      };
      submitForm($form, $submit, success);
      return false;
    });
    /**
     * Handle adding a new group
     */
    $('form.copr-add-group-form').submit(function() {
      const $form = $(this);
      const $submit = $form.find('input[type="submit"]').first();
      success = function(data) {
        $('.copr-group-selector-wrapper').slideUp('slow', function() {
          $('.copr-group-selector').append(
            $('<option/>').val(data.data.id).text(data.data.group_name)
          ).val(data.data.id);
          $('.copr-questions-wrapper').slideDown('slow');
        });
      };
      submitForm($form, $submit, success);
      return false;
    });
    /**
     * Handle group selection
     */
    $('select.copr-group-selector').on('change', function() {
      const groupId = parseInt($(this).find(":selected").val(), 10);
      const $form = $(this).closest('form.copr-select-group-form').first();
      const $parent = $form.closest('.copr-question-form').first();
      const $errorHolder = $parent.find('.copr-form-error').first();
      if (groupId === -1) {
        $errorHolder.html(`<p>${$form.attr('data-required-message')}</p>`).show();
        return false;
      }
      const payload = `${$form.serialize()}&is_ajax=true`;
      $.post($form.attr('action'), payload).done(function(data) {
        if (data.success) {
          $errorHolder.html('').hide();
          const $selectorWrapper = $('.copr-group-selector-wrapper');
          $('.copr-group-selector, input[name="group_id"]').val(groupId);
          if ($selectorWrapper.is(':visible')) {
            $selectorWrapper.slideUp('slow', function() {
              $('.copr-questions-wrapper').slideDown('slow');
            });
          }
          console.log('Load answers here');
        } else {
          $errorHolder.html(`<p>${$form.attr('data-error-message')}</p>`).show();
        }
      }).fail(function() {
        $errorHolder.html(`<p>${$form.attr('data-error-message')}</p>`).show();
      });
      return false;
    });
  }
});
