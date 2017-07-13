<?php
/**
 * LoginPress Settings
 *
 * @since 1.0.9
 */
if ( !class_exists( 'LoginPress_Settings' ) ):

class LoginPress_Settings {

  private $settings_api;

  function __construct() {

    include_once( LOGINPRESS_ROOT_PATH . '/classes/loginpress-settings-api.php' );
    $this->settings_api = new LoginPress_Settings_API;

    add_action( 'admin_init', array( $this, 'loginpress_setting_init' ) );
    add_action( 'admin_menu', array( $this, 'loginpress_setting_menu' ) );
  }

  function loginpress_setting_init() {

    //set the settings.
    $this->settings_api->set_sections( $this->get_settings_sections() );
    $this->settings_api->set_fields( $this->get_settings_fields() );

    //initialize settings.
    $this->settings_api->admin_init();

    //reset settings.
    $this->load_default_settings();
  }

  function load_default_settings() {

    $_loginpress_Setting = get_option( 'loginpress_setting' );
    if ( isset( $_loginpress_Setting['reset_settings'] ) && 'on' == $_loginpress_Setting['reset_settings'] ) {

       $loginpress_last_reset = array( 'last_reset_on' => date('Y-m-d') );
       update_option( 'loginpress_customization', $loginpress_last_reset );
       update_option( 'customize_presets_settings', 'default1' );
       $_loginpress_Setting['reset_settings'] = 'off';
       update_option( 'loginpress_setting', $_loginpress_Setting );
       add_action( 'admin_notices', array( $this, 'settings_reset_message' ) );
    }
  }

  function settings_reset_message() {

    $class = 'notice notice-success';
    $message = __( 'Default Settings Restored', 'loginpress' );

    printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
  }

  function loginpress_setting_menu() {

    add_menu_page( __( 'LoginPress', 'loginpress' ), __( 'LoginPress', 'loginpress' ), 'manage_options', "loginpress-settings", array( $this, 'plugin_page' ), '', 50 );

    add_submenu_page( 'loginpress-settings', __( 'Settings', 'loginpress' ), __( 'Settings', 'loginpress' ), 'manage_options', "loginpress-settings", array( $this, 'plugin_page' ) );

    add_submenu_page( 'loginpress-settings', __( 'Customizer', 'loginpress' ), __( 'Customizer', 'loginpress' ), 'manage_options', "loginpress", '__return_null' );

  }

  function get_settings_sections() {

    $loginpress_general_tab = array(
      array(
        'id'    => 'loginpress_setting',
        'title' => __( 'Settings', 'loginpress' ),
        'desc'  => sprintf( __( 'Everything else is customizable through %1$sWordPress Customizer%2$s.', 'loginpress' ), '<a href="' . admin_url( 'admin.php?page=loginpress' ) . '">', '</a>' ),
      ) );

    if ( ! has_action( 'loginpress_pro_add_template' ) ) {
      array_push( $loginpress_general_tab , array(
        'id'    => 'loginpress_premium',
        'title' => __( 'Upgrade to Pro for More Features', 'loginpress' )
      ) );
    }

      $sections = $loginpress_general_tab;

      return $sections;
  }

  /**
   * Returns all the settings fields
   *
   * @return array settings fields
   */
  function get_settings_fields() {

    $_free_fields = array(
      array(
        'name'        => 'session_expiration',
        'label'       => __( 'Session Expire', 'loginpress' ),
        'desc'        => __( 'Set the session expiration time in minutes. e.g: 10', 'loginpress' ),
        'placeholder' => __( '10', 'loginpress' ),
        'min'         => 0,
        // 'max'         => 100,
        'step'        => '1',
        'type'        => 'number',
        'default'     => 'Title',
        'sanitize_callback' => 'intval'
      ),
      // array(
      //   'name'  => 'enable_privacy_policy',
      //   'label' => __( 'Enable Privacy Policy', 'loginpress' ),
      //   'desc'  => __( 'Enable Privacy Policy checkbox on registration page.', 'loginpress' ),
      //   'type'  => 'checkbox'
      // ),
      // array(
      //   'name'  => 'privacy_policy',
      //   'label' => __( 'Privacy & Policy', 'loginpress' ),
      //   'desc'  => __( 'Right down the privacy and policy description.', 'loginpress' ),
      //   'type'  => 'wysiwyg',
      //   'default' => __( sprintf( __( '%1$sPrivacy Policy%2$s.', 'loginpress' ), '<a href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>' ) )
      // ),
      array(
        'name'  => 'reset_settings',
        'label' => __( 'Reset Default Settings', 'loginpress' ),
        'desc'  => __( 'Remove my custom settings.', 'loginpress' ),
        'type'  => 'checkbox'
      ),
    );

    if ( ! has_action( 'loginpress_pro_add_template' ) ) {
      array_unshift( $_free_fields , array(
        'name'  => 'enable_repatcha_promo',
        'label' => __( 'Enable reCAPTCHA', 'loginpress' ),
        'desc'  => __( 'Enable LoginPress reCaptcha', 'loginpress' ),
        'type'  => 'checkbox'
      ) );
    }

    $_settings_fields = apply_filters( 'loginpress_pro_settings', $_free_fields );

    $settings_fields = array( 'loginpress_setting' => $_settings_fields, );

    return $settings_fields;
  }

  function plugin_page() {
      echo '<div class="wrap loginpress-admin-setting">';

      include_once LOGINPRESS_DIR_PATH . '/include/settings.php';
      $this->settings_api->show_navigation();
      $this->settings_api->show_forms();

      echo '</div>';
  }

  /**
   * Get all the pages
   *
   * @return array page names with key value pairs
   */
  function get_pages() {
    $pages = get_pages();
    $pages_options = array();
    if ( $pages ) {
        foreach ($pages as $page) {
            $pages_options[$page->ID] = $page->post_title;
        }
    }

    return $pages_options;
  }

}
endif;
