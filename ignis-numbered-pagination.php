<?php

/**
 * Ignis Numbered Pagination
 *
 * @package     Ignis Numbered Pagination
 * @author      kharisblank
 * @copyright   2020 kharisblank
 * @license     GPL-2.0+
 *
 * @ignis-numbered-pagination
 * Plugin Name: Ignis Numbered Pagination
 * Plugin URI:  https://easyfixwp.com/
 * Description: Ignis Numbered Pagination enables numbered pagination on aTheme's Ignis WordPress theme. No settings, just activate the plugin. That's it!
 * Version:     0.0.6
 * Author:      kharisblank
 * Author URI:  https://easyfixwp.com
 * Text Domain: ignis-numbered-pagination
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

define( 'EFW_IGNIS_PAGINATION_FILE', __FILE__ );
define( 'EFW_IGNIS_PAGINATION_TEXT_DOMAIN', dirname(__FILE__) );
define( 'EFW_IGNIS_PAGINATION_DIRECTORY_URL', plugins_url( null, EFW_IGNIS_PAGINATION_FILE ) );

if ( !class_exists('EFW_IGNIS_PAGINATION') ) :
  class EFW_IGNIS_PAGINATION {

    public function __construct() {

      add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts'), 9999 );

    }

    /**
     * Theme check
     * @param  string  $theme_name Theme name
     * @return boolean
     */
    function theme_is($theme_name) {

      $theme  = wp_get_theme();
      $parent = wp_get_theme()->parent();

      if ( ($theme != $theme_name ) && ($parent != $theme_name) ) {
        return false;
      }

      return true;

    }

    /**
     * Pagination
     * @return string Pagination HTML
     */
    function pagination() {

      if( !$this->theme_is('Ignis') ) {
        return;
      }

      // Pagination based on: https://gist.github.com/sudipbd/45cca73a78953b69fdbcd160e6430905

      // Don't print empty markup if there's only one page.
      if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
          return;
      }

      $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
      $pagenum_link = html_entity_decode( get_pagenum_link() );
      $query_args   = array();
      $url_parts    = explode( '?', $pagenum_link );

      if ( isset( $url_parts[1] ) ) {
          wp_parse_str( $url_parts[1], $query_args );
      }

      $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
      $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

      $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
      $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

      // Set up paginated links.
      $links = paginate_links( array(
          'base'     => $pagenum_link,
          'format'   => $format,
          'total'    => $GLOBALS['wp_query']->max_num_pages,
          'current'  => $paged,
          'mid_size' => 1,
          'add_args' => array_map( 'urlencode', $query_args ),
          'prev_text' => __( '&larr; Previous', 'ignis' ),
          'next_text' => __( 'Next &rarr;', 'ignis' ),
      ) );

      if ( $links ) :

      ob_start();
      ?>
      <nav class="ingnis-numerred-nav navigation paging-navigation" role="navigation">
          <div class="pagination loop-pagination">
              <?php echo $links; ?>
          </div><!-- .pagination -->
      </nav><!-- .navigation -->
      <?php

      $pagination_html = ob_get_contents(); ob_end_clean();

      return $pagination_html;

      endif;

    }

    /**
     * Enqueue plugin scripts
     * @return void
     */
    function enqueue_scripts() {

      if( !$this->theme_is('Ignis') ) {
        return;
      }

      $css_file = apply_filters('efw_ignis_pagination_css_file_url', EFW_IGNIS_PAGINATION_DIRECTORY_URL . "/css/main.css");

      wp_register_style( 'efw-ignis-pagination-style', $css_file, array(), null );

      wp_enqueue_style( 'efw-ignis-pagination-style' );

      wp_register_script('efw-ignis-pagination-script',
                        EFW_IGNIS_PAGINATION_DIRECTORY_URL."/js/main.js",
                        array ('jquery'),
                        false, true);

      $ignis_pagination = $this->pagination();

      if( !$ignis_pagination || empty($ignis_pagination) ) {
        $ignis_pagination = 'no-pagination';
      }

      wp_localize_script( 'efw-ignis-pagination-script', 'ignis_pagination', $ignis_pagination );

  		wp_enqueue_script( 'efw-ignis-pagination-script' );

    }

  }
endif;

new EFW_IGNIS_PAGINATION;
