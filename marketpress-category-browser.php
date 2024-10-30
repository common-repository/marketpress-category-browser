<?php
/**
 * Plugin Name: Marketpress Category Browser
 * Plugin URI: http://thoughtengineer.com/
 * Description:  Displays list of categories in a widget or a shortcode
 * Version: 1.1.2
 * Author: Samer Bechara
 * Author URI: http://thoughtengineer.com/
 * Text Domain: marketpress-category-browser
 * Domain Path: /languages
 * Network: true 
 * License: GPL2
 */

/*  Copyright 2014  Samer Bechara  (email : sam@thoughtengineer.com)

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

// Define plugin path and URL constant - makes it easier to include files 
define( 'MCB_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCB_URL', plugin_dir_url(__FILE__));


// Require Widget class
require_once (MCB_PATH.'/lib/MarketpressCategoryWidget.php'); 

// Require main class
require_once (MCB_PATH.'/lib/MarketpressCategoryBrowser.php'); 

// Initialize marketpress category browser object
$category_browser = new MarketpressCategoryBrowser();