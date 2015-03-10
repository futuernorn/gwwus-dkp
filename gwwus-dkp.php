<?php
/**
 * Plugin Name: GWWUS DKP
 * Plugin URI: https://github.com/futuernorn/gwwus-dkp
 * Description: Plugin for managing DKP tracking with vanilla WoW (1.12.1)
 * Version: 0.0.1
 * Author: Jeffrey Hogan
 * Author URI: http://jeffhogan.me
 * License: GPL2
 */

/*  Copyright 2015  Jeffrey Hogan  (email : jeff@jeffhogan.me)

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
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('gw-bank.php');
require('gw-database.php');

register_activation_hook( __FILE__, 'gwwus_install' );
register_activation_hook( __FILE__, 'gwwus_install_data' );


add_action( 'admin_menu', 'my_plugin_menu' );


function my_plugin_menu() {
	add_options_page( 'GWWUS DKP Options', 'GWWUS DKP', 'manage_options', 'gwwus-dkp', 'my_plugin_options' );
}


function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';
}


// from: http://wordpress.stackexchange.com/questions/127818/how-to-make-a-plugin-require-another-plugin
add_action( 'admin_init', 'gwwus_plugin_has_twig_plugin' );
function gwwus_plugin_has_twig_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'timber-library/timber.php' ) ) {
        add_action( 'admin_notices', 'gwwus_twig_required_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function gwwus_twig_required_notice(){
    ?><div class="error"><p>Sorry, but GWWUS DKP requires the <a href='https://wordpress.org/plugins/timber-library/' alt='twig-library download page' target='_blank'>Twig</a> plugin to be installed and active.</p></div><?php
}

?>
