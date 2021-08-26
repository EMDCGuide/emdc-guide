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
  }
});
