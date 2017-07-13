<?php
/**
* Plugin Name: LoginPress - Customizing the WordPress Login
* Plugin URI: http://www.WPBrigade.com/wordpress/plugins/loginpress/
* Description: LoginPress is the best <code>wp-login</code> Login Page Customizer plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to completely change the layout of login, register and forgot password forms.
* Version: 1.0.18
* Author: WPBrigade
* Author URI: http://www.WPBrigade.com/
* Text Domain: loginpress
* Domain Path: /languages
*
* @package loginpress
* @category Core
* @author WPBrigade
*/


if ( ! class_exists( 'LoginPress' ) ) :

  final class LoginPress {

    /**
    * @var string
    */
    public $version = '1.0.18';

    /**
    * @var The single instance of the class
    * @since 1.0.0
    */
    protected static $_instance = null;

    /**
    * @var WP_Session session
    */
    public $session = null;

    /**
    * @var WP_Query $query
    */
    public $query = null;

    /**s
    * @var WP_Countries $countries
    */
    public $countries = null;

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->define_constants();
      $this->includes();
      $this->_hooks();

    }

    /**
    * Define LoginPress Constants
    */
    private function define_constants() {

      $this->define( 'LOGINPRESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
      $this->define( 'LOGINPRESS_DIR_PATH', plugin_dir_path( __FILE__ ) );
      $this->define( 'LOGINPRESS_DIR_URL', plugin_dir_url( __FILE__ ) );
      $this->define( 'LOGINPRESS_ROOT_PATH',  dirname( __FILE__ ) . '/' );
      $this->define( 'LOGINPRESS_ROOT_FILE', __FILE__ );
      $this->define( 'LOGINPRESS_VERSION', $this->version );
      $this->define( 'LOGINPRESS_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
      }

    /**
    * Include required core files used in admin and on the frontend.
    */

    public function includes() {

      include_once( LOGINPRESS_DIR_PATH . 'custom.php' );
      include_once( LOGINPRESS_DIR_PATH . 'classes/loginpress-setup.php' );
      include_once( LOGINPRESS_DIR_PATH . 'include/privacy-policy.php' );
    }

    /**
    * Hook into actions and filters
    * @since  1.0.0
    */
    private function _hooks() {

      add_action( 'admin_menu',             array( $this, 'register_options_page' ) );
      add_action( 'plugins_loaded',         array( $this, 'textdomain' ) );
      add_filter( 'plugin_row_meta',        array( $this, '_row_meta'), 10, 2 );
      add_action( 'admin_enqueue_scripts',  array( $this, '_admin_scripts' ) );
      add_action( 'admin_footer',           array( $this, 'add_deactive_modal' ) );
			add_action( 'admin_init',             array( $this, 'loginpress_review_notice' ) );
      add_action( 'plugin_action_links', 	  array( $this, 'loginpress_action_links' ), 10, 2 );
      add_action( 'admin_init',             array( $this, 'redirect_optin' ) );
      add_filter( 'auth_cookie_expiration', array( $this, '_change_auth_cookie_expiration' ), 10, 3 );

      add_action( 'wp_ajax_loginpress_deactivate', array( $this, '_deactivate' ) );
      add_action( 'wp_ajax_loginpress_optout_yes', array( $this, 'optout_yes' ) );

    }

    /**
     * Opt-out
     *
     * @since  1.0.15
     */
    function optout_yes() {
      update_option( '_loginpress_optin', 'no' );
      wp_die();
    }

    /**
    * Redirect to Optin page.
    *
    * @since 1.0.15
    */
    function redirect_optin() {

      // delete_option( '_loginpress_optin' );

      if ( isset( $_POST['loginpress-submit-optout'] ) ) {

        update_option( '_loginpress_optin', 'no' );
        $this->_send_data( array(
          'action'	=>	'Skip',
        ) );

      } elseif ( isset( $_POST['loginpress-submit-optin'] ) ) {

        update_option( '_loginpress_optin', 'yes' );
        $fields = array(
          'action'	        =>	'Activate',
          'track_mailchimp' =>	'yes'
        );
        $this->_send_data( $fields );

      } elseif ( ! get_option( '_loginpress_optin' ) && isset( $_GET['page'] ) && ( $_GET['page'] === 'loginpress-settings' || $_GET['page'] === 'loginpress' || $_GET['page'] === 'abw' ) ) {

        wp_redirect( admin_url('admin.php?page=loginpress-optin&redirect-page=' . $_GET['page'] ) );
        exit;
      } elseif ( get_option( '_loginpress_optin' ) && ( get_option( '_loginpress_optin' ) == 'yes' || get_option( '_loginpress_optin' ) == 'no' ) && isset( $_GET['page'] ) && $_GET['page'] === 'loginpress-optin' ) {

        wp_redirect( admin_url( 'admin.php?page=loginpress-settings' ) );
        exit;
      }
    }

    /**
    * Main Instance
    *
    * @since 1.0.0
    * @static
    * @see loginPress_loader()
    * @return Main instance
    */
    public static function instance() {
      if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }


    /**
    * Load Languages
    * @since 1.0.0
    */
    public function textdomain() {

      $plugin_dir =  dirname( plugin_basename( __FILE__ ) ) ;
      load_plugin_textdomain( 'loginpress', false, $plugin_dir . '/languages/' );
    }

    /**
    * Define constant if not already set
    * @param  string $name
    * @param  string|bool $value
    */
    private function define( $name, $value ) {
      if ( ! defined( $name ) ) {
        define( $name, $value );
      }
    }

    /**
    * Init WPBrigade when WordPress Initialises.
    */
    public function init() {
      // Before init action
    }
    /**
    * Add new page in Apperance to customize Login Page
    */
    public function register_options_page() {

      add_submenu_page( null, __( 'Activate', 'loginpress' ), __( 'Activate', 'loginpress' ), 'manage_options', 'loginpress-optin', array( $this, 'render_optin' )  );

      add_theme_page( __( 'LoginPress', 'loginpress' ), __( 'LoginPress', 'loginpress' ), 'manage_options', "abw", '__return_null' );
    }


    /**
     * Show Opt-in Page.
     *
     * @since 1.0.15
     */
		function render_optin() {
			include LOGINPRESS_DIR_PATH . 'include/loginpress-optin-form.php';
		}

    /**
    * Wrapper function to send data.
    * @param  [arrays]  $args.
    *
    * @since  1.0.15
    *
    */
 function _send_data( $args ) {

   $cuurent_user = wp_get_current_user();
   $fields = array(
     'email' 		         => get_option( 'admin_email' ),
     'website' 			     => get_site_url(),
     'action'            => '',
     'reason'            => '',
     'reason_detail'     => '',
     'display_name'			 => $cuurent_user->display_name,
     'blog_language'     => get_bloginfo( 'language' ),
     'wordpress_version' => get_bloginfo( 'version' ),
     'plugin_version'    => LOGINPRESS_VERSION,
     'plugin_name' 			 => 'LoginPress Free',
   );

   $args = array_merge( $fields, $args );
   $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
     'method'      => 'POST',
     'timeout'     => 5,
     'httpversion' => '1.0',
     'blocking'    => true,
     'headers'     => array(),
     'body'        => $args,
   ) );

  //  echo '<pre>'; print_r( $args ); echo '</pre>';

 }

   /**
    * Session Expiration
    *
    * @since  1.0.18
    */
   function _change_auth_cookie_expiration( $expiration, $user_id, $remember ) {

     $loginpress_setting  = get_option( 'loginpress_setting' );
     $_expiration =  isset( $loginpress_setting['session_expiration'] ) ? intval( $loginpress_setting['session_expiration'] ) : '';

     if ( empty( $_expiration ) || '0' == $_expiration ) {
       return $expiration;
     }

      $expiration  = $_expiration * 60; // Duration of the expiration period in seconds.

     return $expiration;
   }

    /**
     * Load JS or CSS files at admin side and enqueue them
     * @param  string tell you the Page ID
     * @return void
     */
    function _admin_scripts( $hook ) {

      if( $hook != 'toplevel_page_loginpress-settings' ) {
        return;
      }

      wp_enqueue_style( 'loginpress_stlye', plugins_url( 'css/style.css', __FILE__ ), array(), LOGINPRESS_VERSION );
      wp_enqueue_script( 'loginpress_js', plugins_url( 'js/admin-custom.js', __FILE__ ), array(), LOGINPRESS_VERSION );

      // Array for localize.
      $loginpress_localize = array(
        'plugin_url' => plugins_url(),
      );

      wp_localize_script( 'loginpress_js', 'loginpress_script', $loginpress_localize );
    }


    public function _row_meta( $links, $file ) {

      if ( strpos( $file, 'loginpress.php' ) !== false ) {

        // Set link for Reviews.
        $new_links = array('<a href="https://wordpress.org/support/plugin/loginpress/reviews/?filter=5" target="_blank"><span class="dashicons dashicons-thumbs-up"></span> ' . __( 'Vote!', 'loginpress' ) . '</a>',
        );

        $links = array_merge( $links, $new_links );
      }

      return $links;
    }

    /**
     * Add deactivate modal layout.
     */
    function add_deactive_modal() {
      global $pagenow;

      if ( 'plugins.php' !== $pagenow ) {
        return;
      }

      include LOGINPRESS_DIR_PATH . 'include/deactivate_modal.php';
      include LOGINPRESS_DIR_PATH . 'include/loginpress-optout-form.php';
    }

    function _deactivate() {

      $email         = get_option( 'admin_email' );
      $_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
      $reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
      $reason        = '';

      if ( $_reason == '1' ) {
        $reason = 'I only needed the plugin for a short period';
      } elseif ( $_reason == '2' ) {
        $reason = 'I found a better plugin';
      } elseif ( $_reason == '3' ) {
        $reason = 'The plugin broke my site';
      } elseif ( $_reason == '4' ) {
        $reason = 'The plugin suddenly stopped working';
      } elseif ( $_reason == '5' ) {
        $reason = 'I no longer need the plugin';
      } elseif ( $_reason == '6' ) {
        $reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
      } elseif ( $_reason == '7' ) {
        $reason = 'Other';
      }
      $fields = array(
        'email' 		        => $email,
        'website' 			    => get_site_url(),
        'action'            => 'Deactivate',
        'reason'            => $reason,
        'reason_detail'     => $reason_detail,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'plugin_version'    => LOGINPRESS_VERSION,
        'plugin_name' 			=> 'LoginPress Free',
      );

      $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
        'method'      => 'POST',
        'timeout'     => 5,
        'httpversion' => '1.0',
        'blocking'    => false,
        'headers'     => array(),
        'body'        => $fields,
      ) );

    // if ( is_wp_error( $response ) ) {
    //   $error_message = $response->get_error_message();
    //   echo "Something went wrong: $error_message";
    // } else {
    //   echo 'Response:<pre>';
    //   print_r( $response );
    //   echo '</pre>';
    // }
    wp_die();
  }

  static function plugin_activation() {

    $loginpress_setting = get_option( 'loginpress_setting' );
    if ( ! isset( $loginpress_setting['tracking_anonymous'] ) || 'off' == $loginpress_setting['tracking_anonymous'] ) {
      return;
    }

    $email         = get_option( 'admin_email' );

    $fields = array(
      'email' 		        => $email,
      'website' 			    => get_site_url(),
      'action'            => 'Activate',
      'reason'            => '',
      'reason_detail'     => '',
      'blog_language'     => get_bloginfo( 'language' ),
      'wordpress_version' => get_bloginfo( 'version' ),
      'plugin_version'    => LOGINPRESS_VERSION,
      'plugin_name' 			=> 'LoginPress Free',
    );

    $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
      'method'      => 'POST',
      'timeout'     => 5,
      'httpversion' => '1.0',
      'blocking'    => false,
      'headers'     => array(),
      'body'        => $fields,
    ) );

  }

  static function plugin_uninstallation() {

    $email         = get_option( 'admin_email' );

    $fields = array(
      'email' 		        => $email,
      'website' 			    => get_site_url(),
      'action'            => 'Uninstall',
      'reason'            => '',
      'reason_detail'     => '',
      'blog_language'     => get_bloginfo( 'language' ),
      'wordpress_version' => get_bloginfo( 'version' ),
      'plugin_version'    => LOGINPRESS_VERSION,
      'plugin_name' 			=> 'LoginPress Free',
    );

    $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
      'method'      => 'POST',
      'timeout'     => 5,
      'httpversion' => '1.0',
      'blocking'    => false,
      'headers'     => array(),
      'body'        => $fields,
    ) );

  }

  /**
	 * Ask users to review our plugin on wordpress.org
	 *
	 * @since 1.0.11
	 * @return boolean false
	 */
	public function loginpress_review_notice() {

		$this->loginpress_review_dismissal();
		$this->loginpress_review_pending();

		$activation_time 	= get_site_option( 'loginpress_active_time' );
		$review_dismissal	= get_site_option( 'loginpress_review_dismiss' );

		if ( 'yes' == $review_dismissal ) return;

		if ( ! $activation_time ) :

			$activation_time = time();
			add_site_option( 'loginpress_active_time', $activation_time );
		endif;

		// 1296000 = 15 Days in seconds.
		if ( time() - $activation_time > 1296000 ) :

      wp_enqueue_style( 'loginpress_review_stlye', plugins_url( 'css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
			add_action( 'admin_notices' , array( $this, 'loginpress_review_notice_message' ) );
		endif;

    if ( is_multisite() ) {
      add_action( 'admin_notices' ,        array( $this, 'loginpress_multisite_admin_message' ) );
      add_action( 'network_admin_notices', array( $this, 'loginpress_multisite_admin_message' ) );
    }
	}

  /**
	 *	Multisite Admin Notice.
	 *
	 *	@since 1.0.12
	 */
  public function loginpress_multisite_admin_message() {

    echo sprintf( __( '%1$s%2$sHi, LoginPress is not compatible with multisite right now. %3$s Checkout this Ticket in WordPress core %4$s %5$s%6$s', 'loginpress' ),
    '<div class="notice notice-error is-dismissible">', '<p>', '<a href="https://core.trac.wordpress.org/ticket/40069" target="_blank">', '</a>', '</p>', '</div>' );
  }

  /**
	 *	Check and Dismiss review message.
	 *
	 *	@since 1.0.11
	 */
	private function loginpress_review_dismissal() {

		if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
			! isset( $_GET['loginpress_review_dismiss'] ) ) :

			return;
		endif;

		add_site_option( 'loginpress_review_dismiss', 'yes' );
	}

  /**
	 * Set time to current so review notice will popup after 14 days
	 *
	 * @since 1.0.11
	 */
	function loginpress_review_pending() {

		if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
			! isset( $_GET['loginpress_review_later'] ) ) :

			return;
		endif;

		// Reset Time to current time.
		update_site_option( 'loginpress_active_time', time() );
	}

  /**
	 * Review notice message
	 *
	 * @since  1.0.11
	 */
	public function loginpress_review_notice_message() {

		$scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
		$url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_dismiss=yes';
		$dismiss_url = wp_nonce_url( $url, 'loginpress-review-nonce' );

		$_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_later=yes';
		$later_url   = wp_nonce_url( $_later_link, 'loginpress-review-nonce' );
    ?>

		<div class="loginpress-review-notice">
			<div class="loginpress-review-thumbnail">
				<img src="<?php echo plugins_url( 'img/thumbnail/gray-loginpress.png', __FILE__ ) ?>" alt="">
			</div>
			<div class="loginpress-review-text">
				<h3><?php _e( 'Leave A Review?', 'loginpress' ) ?></h3>
				<p><?php _e( 'We hope you\'ve enjoyed using LoginPress! Would you consider leaving us a review on WordPress.org?', 'loginpress' ) ?></p>
				<ul class="loginpress-review-ul">
          <li><a href="https://wordpress.org/support/view/plugin-reviews/loginpress?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $later_url ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Maybe Later', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'loginpress' ) ?></a></li></ul>
			</div>
		</div>
	<?php
	}

  /**
	 * Add a link to the settings page to the plugins list
	 *
	 * @since  1.0.11
	 */
	public function loginpress_action_links( $links, $file ) {

		static $this_plugin;

		if ( empty( $this_plugin ) ) {

			$this_plugin = 'loginpress/loginpress.php';
		}

		if ( $file == $this_plugin ) {

			$settings_link = sprintf( esc_html__( '%1$s Settings %2$s | %3$s Customize %4$s', 'loginpress' ), '<a href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?page=loginpress' ) . '">', '</a>' );


      if( 'yes' == get_option( '_loginpress_optin' ) ){
        $settings_link .= sprintf( esc_html__( ' | %1$s Opt Out %2$s ', 'loginpress'), '<a class="opt-out" href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>' );
      } else {
        $settings_link .= sprintf( esc_html__( ' | %1$s Opt In %2$s ', 'loginpress'), '<a href="' . admin_url( 'admin.php?page=loginpress-optin&redirect-page=' .'loginpress-settings' ) . '">', '</a>' );
      }

      array_unshift( $links, $settings_link );

      if ( ! has_action( 'loginpress_pro_add_template' ) ) :
        $pro_link = sprintf( esc_html__( '%1$s %3$s Upgrade Pro %4$s %2$s', 'loginpress' ),  '<a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=plugin-action-link&utm_campaign=pro-upgrade" target="_blank">', '</a>', '<span class="loginpress-dasboard-pro-link">', '</span>' );
        array_push( $links, $pro_link );
      endif;
		}

		return $links;
	}

} // End Of Class.
endif;

/**
* Returns the main instance of WP to prevent the need to use globals.
*
* @since  1.0.0
* @return LoginPress
*/
function loginPress_loader() {
  return LoginPress::instance();
}

// Call the function
loginPress_loader();

/**
* Create the Object of Custom Login Entites and Settings.
*
* @since  1.0.0
*/
new LoginPress_Entities();
new LoginPress_Settings();

/**
* Create the Object of Remote Notification.
*
* @since  1.0.9
*/
if (!class_exists('TAV_Remote_Notification_Client')) {
  require( LOGINPRESS_ROOT_PATH . 'include/class-remote-notification-client.php' );
}
$notification = new TAV_Remote_Notification_Client( 125, '16765c0902705d62', 'http://wpbrigade.com?post_type=notification' );

// register_activation_hook( __FILE__, array( 'LoginPress', 'plugin_activation' ) );
register_uninstall_hook( __FILE__, array( 'LoginPress', 'plugin_uninstallation' ) );
?>
