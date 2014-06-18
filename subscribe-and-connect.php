<?php
/**
 * Plugin Name: Subscribe & Connect
 * Plugin URI: http://woothemes.com/
 * Description: Hi, I'm here to help your visitors subscribe to your content, as well as share it across various social networks.
 * Version: 1.1.0
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2012  WooThemes  (email : info@woothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	require_once( 'classes/class-subscribe-and-connect.php' );
    require_once( 'classes/class-subscribe-and-connect-widget.php' );

	global $subscribe_and_connect;
	$subscribe_and_connect = new Subscribe_And_Connect( __FILE__ );
	$subscribe_and_connect->version = '1.1.0';

    if ( ! is_admin() ) {

    require_once( 'subscribe-and-connect-functions.php' );
    require_once( 'subscribe-and-connect-template.php' );

    }