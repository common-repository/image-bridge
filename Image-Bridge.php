<?php

/**
 * Plugin Name: Image-Bridge
 * Plugin URI: 
 * Description: A lightweight plugin for replacing an external image with URL as a featured image for posts/pages/products post type.
 * Version: 1.0
 * Author: Babak Safayi
 * Author URI: 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:
 * Text Domain: Image-Bridge-plugin
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die( 'Hey you can\'t access this site, you are stupid!' );

if ( ! class_exists( 'ImageBridge' ) ) {
    class ImageBridge {
        public $plugin;

        public function __construct() {
            $this->plugin = plugin_basename( __FILE__ );
        }

        public function register() {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_Image_Bridge_pages' ) );
            add_filter( "plugin_action_links_$this->plugin", array( $this, 'Image_Bridge_settings_link' ) );
        }

        public function enqueue() {
            wp_enqueue_style( 'ImageBridgestyle', plugin_dir_url( __FILE__ ) . '/assets/ImageBridge.css', array(), '1.0.0' );           
        }

        public function add_admin_Image_Bridge_pages() {
            add_menu_page( 'ImageBridge', 'Image Bridge', 'manage_options', 'ImageBridge_plugin', array( $this, 'admin_index' ), plugin_dir_url( __FILE__ ) . 'assets/images/my-icon.png', 110 );
        }

        public function admin_index() {
            require_once plugin_dir_path( __FILE__ ) . 'templates/home.php';
        }

        public function Image_Bridge_settings_link( $links ) {
            $settings_link = '<a href="admin.php?page=ImageBridge_plugin">Settings</a>';
            array_push( $links, $settings_link );
            return $links;
        }

        public function activate() {
            require_once plugin_dir_path( __FILE__ ) . 'inc/ImageBridge-plugin-activate.php';
            ImageBridgePluginActivate::activate();
        }
    }

    $ImageBridge = new ImageBridge();
    $ImageBridge->register();

    // Activation plugin.
    register_activation_hook( __FILE__, array( $ImageBridge, 'activate' ) );

    // Deactivation plugin.
    require_once plugin_dir_path( __FILE__ ) . 'inc/ImageBridge-plugin-deactivate.php';
    register_deactivation_hook( __FILE__, array( 'ImageBridgePluginDeactivate', 'deactivate' ) );
}
    
       
    include sprintf( '%s/functions.php', plugin_dir_path( __FILE__ ) );
