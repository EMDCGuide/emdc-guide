(function($) {
    $(function() {

        /*
        After FacetWP reloads, store any updates into a cookie
        https://facetwp.com/how-to-preserve-facet-selections-across-pages/
        */
        $(document).on('facetwp-loaded', function() {
            var date = new Date();
            var facets = window.location.search;
            if (facets && facets !== '') {
              date.setTime(date.getTime()+(24*60*60*1000));
              document.cookie = "facetdata="+facets+"; expires="+date.toGMTString()+"; path=/";
            } else {
              // Remove the cookie if it exists
              document.cookie = 'facetdata=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
            }
            if (typeof gtag === 'undefined') {
              console.log('gtag is undefined!');
              return;
            }
            var searchTerm = gup('_search', window.location);
            if (searchTerm) {
              gtag('event','search', {'search_term': searchTerm});
            }
            var categoriesQuery = gup('_categories', window.location);
            if (categoriesQuery) {
              var categories = categoriesQuery.split('%2C');
              for (var i = 0; i < categories.length; i++) {
                gtag('event','category_filter', {
                  'event_category': categories[i]
                });
              }
            }
            var tagsQuery = gup('_tags', window.location);
            if (tagsQuery) {
              var tags = tagsQuery.split('%2C');
              for (var i = 0; i < tags.length; i++) {
                gtag('event','tag_filter', {
                  'event_category': tags[i]
                });
              }
            }
        });

        /*
        When FacetWP first initializes, look for the "facetdata" cookie
        If it exists, set window.location.search= facetdata
        */
        $(document).on('facetwp-refresh', function() {
            if (! FWP.loaded) {
                var facets = window.location.search;
                var facetdata = readCookie('facetdata');
                if (null != facetdata && '' != facetdata && facets != facetdata) {
                    document.cookie = 'facetdata=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
                    window.location.search = facetdata;
                }
            }
        });

        /*
        Cookie handler
        */
        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }

        /**
         * Get a parameter from the url
         * @param  {string} name  The name of the parmeter
         * @param  {string} url   The url
         * @return {string}       The result
         */
        function gup( name, url ) {
          if (!url) url = location.href;
          name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
          var regexS = "[\\?&]"+name+"=([^&#]*)";
          var regex = new RegExp( regexS );
          var results = regex.exec( url );
          return results == null ? null : results[1];
        }

        /**
         * Make standard form fields to make read-only
         * To apply, add CSS class 'wpf-disable-field' (no quotes) to field in form builder
         *
         * @link https://wpforms.com/developers/disable-a-form-field-to-prevent-user-input/
         */
        $( '.wpf-disable-field input, .wpf-disable-field textarea' ).attr({
          readonly: 'readonly',
          tabindex: '-1'
        });
    });
})(jQuery);
