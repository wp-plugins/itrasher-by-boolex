<?php
    /*
    Plugin Name: iTrasher by Boolex
    Plugin URI: http://www.boolex.com
    Description: This plugin allows you to delete unused images from your wordpress website
    Version: 1.0.0
    Author: Boolex
    Author URI: http://www.boolex.com
    License: GPLv2
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    */

add_action( 'admin_menu', 'register_itrasher_menu' );

function register_itrasher_menu() {

	add_menu_page( 'Image Trasher', 'iTrasher', 'manage_options', 'itrasher', 'itrasher_scan', plugins_url( 'itrasher/icon.png' ));

}

function itrasher_scan() {

    include( 'itrasher-admin.php' );

}

add_action('admin_enqueue_scripts', 'ajax_itrasher_enqueue_scripts');
function ajax_itrasher_enqueue_scripts() {
    wp_enqueue_script( 'itrasher', plugins_url( 'itrasher/js/itrasher.js' ), array('jquery'), '1.0', true );
    wp_enqueue_style( 'itrasher', plugins_url( 'itrasher/css/itrasher.css' ) );
}

// handles ajax request
function itrasher_trash() {

    require_once('config.php');
    require_once('itrasher_scanner.php');

    $itrasher = new ITrasher_Scanner($config);

   if($images = $_REQUEST['data'])
   {
       $itrasher->trash($images);
       echo json_encode('success');
   }
}

add_action('wp_ajax_itrasher_trash', 'itrasher_trash'); 
add_action('wp_ajax_nopriv_itrasher_trash', 'itrasher_trash'); 