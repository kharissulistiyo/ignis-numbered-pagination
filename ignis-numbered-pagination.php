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
      add_filter( 'ignis_pagination_paged_query', array($this, 'paged_portfolio') );

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
     * Portfolio post type check
     * @return boolean
     */
    function is_portfolio() {

      if ( is_page_template( 'page-templates/template_portfolio.php' ) || is_post_type_archive('jetpack-portfolio') || is_tax( 'jetpack-portfolio-type' ) || is_tax('jetpack-portfolio-tag') ) {
        return true;
      }

      return false;

    }


    /**
     * Object query
     * @return object
     */
    function obj_query() {
      return apply_filters( 'ignis_pagination_query', $GLOBALS['wp_query'] );
    }

    /**
     * Navigation
     * @param string $links Links HTML
     * @return void
     */
     function navigation($links) {
       ob_start();
       ?>
       <nav class="ingnis-numerred-nav navigation paging-navigation" role="navigation">
           <div class="pagination loop-pagination">
               <?php echo $links; ?>
           </div><!-- .pagination -->
       </nav><!-- .navigation -->
       <?php
       $navigation = ob_get_contents(); ob_end_clean();
       return $navigation;
     }

    /**
     * Pagination
     * @param object $query Query object
     * @return string Pagination HTML
     */
    function pagination($query) {

      if( !$this->theme_is('Ignis') ) {
        return;
      }

      // Pagination based on: https://gist.github.com/sudipbd/45cca73a78953b69fdbcd160e6430905

      // Don't print empty markup if there's only one page

      if ( $query->max_num_pages < 2 ) {
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
        'base'      => $pagenum_link,
        'format'    => $format,
        'total'     => $query->max_num_pages,
        'current'   => apply_filters( 'ignis_pagination_paged_query', $paged ),
        'mid_size'  => 1,
        'add_args'  => array_map( 'urlencode', $query_args ),
        'prev_text' => __( '&larr; Previous', 'ignis' ),
        'next_text' => __( 'Next &rarr;', 'ignis' ),
      ) );

      if ( $links ) :

        return $this->navigation($links);

      endif;

    }

    /**
     * Paged query of portfolio post type
     * @return int
     */
    function paged_portfolio() {

      $paged = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

      if ( $this->is_portfolio() ) {

        if ( get_query_var( 'paged' ) ) :
          $paged = get_query_var( 'paged' );
        elseif ( get_query_var( 'page' ) ) :
          $paged = get_query_var( 'page' );
        else :
          $paged = 1;
        endif;

        return $paged;

      }

      return $paged;

    }

    /**
     * Portfolio post type query
     * @return object
     */
    function portfolio_post_type_query() {

      $posts_per_page = get_option( 'jetpack_portfolio_posts_per_page', '10' );
      $args = array(
        'post_type'      => 'jetpack-portfolio',
        'posts_per_page' => $posts_per_page,
        'paged'          => $this->paged_portfolio(),
      );

      if( (null != $posts_per_page) || !empty($posts_per_page) ) :
        $project_query = new WP_Query ( $args );
        return $project_query;
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

      $query = $this->obj_query();

      if ( $this->is_portfolio() ) {
        $query = $this->portfolio_post_type_query();
      }

      if( $query ) :
        $ignis_pagination = $this->pagination($query);

        if( !$ignis_pagination || empty($ignis_pagination) ) {
          $ignis_pagination = 'no-pagination';
        }

        wp_localize_script( 'efw-ignis-pagination-script', 'ignis_pagination', $ignis_pagination );

    		wp_enqueue_script( 'efw-ignis-pagination-script' );
      endif;

    }

  }
endif;

new EFW_IGNIS_PAGINATION;
