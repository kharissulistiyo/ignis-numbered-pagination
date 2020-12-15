;(function($) {

  'use strict'

  if( 'no-pagination' != ignis_pagination ) {

    $('.posts-navigation, .nav-links').replaceWith( ignis_pagination );

  }

})(jQuery);
