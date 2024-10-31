<?php
/*
 * Plugin Name: Projector
 * Version: 0.1
 * Plugin URI: http://mosaika.fr
 * Description: Easily present your <em>work in progress</em> projects to your clients.
 * Author: Pierre Saikali
 * Author URI: http://saika.li
 * Requires at least: 3.0
 * Tested up to: 3.6
 *
 * @package WordPress
 * @author Pierre Saikali
 * @since 0.1
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !defined( 'MSK_PROJECTOR_PATH' ) )      define( 'MSK_PROJECTOR_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined( 'MSK_PROJECTOR_URL' ) )       define( 'MSK_PROJECTOR_URL', plugin_dir_url( __FILE__ ) );
if ( !defined( 'MSK_PROJECTOR_VERSION' ) )   define( 'MSK_PROJECTOR_VERSION', '0.1' );

if ( !class_exists('Cuztom') ) require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'inc/cuztom/cuztom.php' );

require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'classes/class-msk-projector.php' );
require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'classes/class-msk-projector-settings.php' );
require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'classes/post-types/class-msk-projector-post_type.php' );

require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'msk-projector-filters.php' );
require_once( trailingslashit( MSK_PROJECTOR_PATH ) . 'msk-projector-functions.php' );

global $MSK_PROJECTOR;
$MSK_PROJECTOR           = new MSK_Projector( __FILE__ );
$MSK_PROJECTOR_SETTINGS  = new MSK_Projector_Settings( __FILE__ );
$MSK_PROJECTOR_POST_TYPE = new MSK_Projector_Post_Type( __FILE__ );