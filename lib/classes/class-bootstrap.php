<?php

namespace UsabilityDynamics\WPJC;

/**
 * Class Bootstrap
 * @package UsabilityDynamics\WPJC
 */
class Bootstrap {

  /**
   * @var null
   */
  static $instance = null;

  /**
   * @return null|Bootstrap
   */
  public static function get_instance() {
    return self::$instance ? self::$instance : self::$instance = new self;
  }

  /**
   * Bootstrap constructor.
   */
  public function __construct() {

    if ( WPJC_JSONIFY_METHOD == 'query' ) {
      add_action('init', function () {
        add_rewrite_tag('%'.WPJC_GET_ARG_NAME.'%', '([^&]+)');
        add_rewrite_rule('(.?.+?)(?:\/([0-9]+))?\/?\.'.WPJC_GET_ARG_NAME.'$', 'index.php?pagename=$matches[1]&page=$matches[2]&'.WPJC_GET_ARG_NAME.'=1', 'top');
      }, 10);

      add_action( 'template_redirect', function() {
        if ( get_query_var(WPJC_GET_ARG_NAME) == '1' ) {
          add_filter( 'wpjc_json_content', '__return_true' );
          add_filter( 'wpjc_json_content_output', [ '\UsabilityDynamics\WPJC\Bootstrap', 'json_output' ] );
        }
      });
    }

    // Disable this plugin if viewing customizer
    if ( !empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] == '/wp-admin/customize.php' ) return;

    // Disable on admin panel
    if ( is_admin() ) return;

    if ( WPJC_JSONIFY_METHOD == 'get' && isset( $_GET[WPJC_GET_ARG_NAME] ) ) {
      add_filter( 'wpjc_json_content', '__return_true' );
      add_filter( 'wpjc_json_content_output', [ '\UsabilityDynamics\WPJC\Bootstrap', 'json_output' ] );
    }

    add_filter( 'wpjc_json_output_object', function( $object, $html ) {

      $blog_post_id = get_option('page_for_posts');
      $page_on_front = get_option('page_on_front');

      if ( !function_exists( 'the_seo_framework' ) ) {
        $page_title = wp_get_document_title();
      } else {
        $seo = \the_seo_framework();
        if ( is_home() ) {
          $page_title = $seo->title('','','', array('term_id' => $page_on_front, 'get_custom_field' => true, 'escape' => true));
        } else {
          $page_title = $seo->title('','','', array('term_id' => get_queried_object_id(), 'get_custom_field' => true, 'escape' => true));
        }
      }

      $object->site_url = site_url();
      $object->ajax_url = admin_url('admin-ajax.php');
      $object->template_url = get_template_directory_uri() . '/';
      $object->site_name = esc_attr(get_bloginfo('name'));
      $object->page_title = $page_title;
      $object->blog_base = $blog_post_id ? str_replace( home_url(), "", get_permalink( $blog_post_id ) ) : null;
      $object->post     = $this->get_post_data();
      $object->wp_query = $this->get_query_data();
      $object->user     = $this->get_user_data();

      return $object;
    }, 5, 2 );

    ob_start();

    add_action('shutdown', function() {
      $final = '';

      $levels = ob_get_level();

      for ( $i = 0; $i < $levels; $i++ ) {
        $final .= ob_get_clean();
      }

      if ( apply_filters( 'wpjc_json_content', false ) ) {
        header("Content-Type: application/json");
        echo apply_filters( 'wpjc_json_content_output', $final ); exit;
      }

      echo $final; exit;
    }, 0);

  }

  /**
   * @param $html_output
   * @return false|string
   */
  public static function json_output( $html_output ) {
    $response = apply_filters( 'wpjc_json_output_object', new \stdClass(), $html_output );
    return wp_json_encode( $response );
  }

  /**
   * @return \stdClass
   */
  private function get_post_data() {
    global $post;

    $_post = new \stdClass();

    if ( empty($post) ) return $_post;

    $_post->ID = $post->ID;
    $_post->post_title = $post->post_title;
    $_post->post_type = $post->post_type;
    $_post->post_url = get_permalink($post->ID);

    return apply_filters( 'wpjc_json_post', $_post, $post );
  }

  /**
   * @return \stdClass
   */
  private function get_query_data() {
    global $wp_query;

    $query = new \stdClass();

    foreach( $wp_query as $query_key => $query_value ) {
      if ( strstr( $query_key, 'is_' ) )
        $query->$query_key = $query_value;
    }

    $query->is_blog = false;

    return apply_filters( 'wpjc_json_wp_query', $query, $wp_query );
  }

  /**
   * @return bool|User
   */
  private function get_user_data() {

    $user = false;

    if ( is_user_logged_in() ) {
      $user = new User( get_current_user_id() );
    }

    return $user;
  }

}