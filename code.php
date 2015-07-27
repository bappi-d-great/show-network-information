<?php
/*
Plugin Name: Network Information
Plugin URI: http://premium.wpmudev.org/
Description: Shows information with a network
Author: Ashok (WPMUDEV)
Version: 1.0.0
Author URI: http://premium.wpmudev.org/
Network: True
Text Domain: ni
License: GPL V2 or later
*/

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) wp_die( __( 'Sorry Cowboy!', 'ni' ) );

if( ! class_exists( 'Network_Info' ) ) {
    /**
     * Class Network_Info
     */
    class Network_Info{
        
        /**
         * Singleton Instance of this class
         */
        private static $_instance;
        
        /**
         * If prosite is active
         */
        private $is_pro_site_active;
        
        /**
         * global $wpdb
         */
        private $_db;
        
        public static function get_instance() {
            if ( ! self::$_instance instanceof Network_Info ) {
                self::$_instance = new Network_Info();
            }
            return self::$_instance;
        }
        
        public function __construct() {
            $this->is_pro_site_active = false;
            if( class_exists( 'ProSites' ) ){
                $this->is_pro_site_active = true;
            }
            
            global $wpdb;
            $this->_db = $wpdb;
            
            add_shortcode( 'network_info', array( &$this, 'network_info_cb' ) );
        }
        
        public function network_info_cb( $atts ){
            
            $html = '';
            
            $sites = wp_get_sites();
            $html .= __( 'Total Sites: ', 'ni' ) . count( $sites );
            
            $users = $this->_db->get_var( "SELECT COUNT(1) FROM {$this->_db->users}" );
            $html .= '<br>' . __( 'Total Users: ', 'ni' ) . count( $users );
            
            if( $this->is_pro_site_active ){
                $pro_sites = 0;
                foreach( $sites as $site ){
                    if( is_pro_site( $site['blog_id'] ) ){
                        $pro_sites++;
                    }
                }
                $html .= '<br>' . __( 'Total Pro Sites: ', 'ni' ) . $pro_sites;
            }
            
            if( is_main_site() || ( defined( 'ALLOW_NI_IN_SUBSITE' ) && ALLOW_NI_IN_SUBSITE ) ){
                return $html;
            }
            return __( 'You are not allowed to use this shortcode!', 'ni' );
        }
        
    }
    
    add_action( 'plugins_loaded', 'network_info_init' );
    function network_info_init() {
        return Network_Info::get_instance();
    }
}