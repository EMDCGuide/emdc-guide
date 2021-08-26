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
      if (current) {
        // Disable previous
        displayed.find('.copr-previous').first().addClass('copr-disabled');
      }
      if (current == total) {
        // Disable next
        displayed.find('.copr-next').first().addClass('copr-disabled');
      }
    });
  }
});
