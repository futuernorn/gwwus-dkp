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
register_activation_hook( __FILE__, 'gwwus_activation' );
register_deactivation_hook( __FILE__, 'gwwus_deactivation' );

/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
function gwwus_activation() {
	wp_schedule_event( time(), 'hourly', 'gwwus_hourly_event_hook' );
}


/**
 * On the scheduled action hook, run the function.
 */
function gwwus_do_this_hourly() {
	// do something every hour
    error_log("GWWUS hourly cron");
}



/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function gwwus_deactivation() {
	wp_clear_scheduled_hook( 'gwwus_hourly_event_hook' );
    delete_option( "gwwus-dkp_current-import-row");    
}


add_action( 'admin_notices', 'gwwus_admin_import_notice' );
add_action( 'admin_footer', 'gwwus_action_bulk_import' ); 
add_action( 'gwwus_hourly_event_hook', 'gwwus_do_this_hourly' );
add_action( 'admin_menu', 'gwwus_plugin_menu' );


function gwwus_plugin_menu() {
	add_options_page( 'GWWUS DKP Options', 'GWWUS DKP', 'manage_options', 'gwwus-dkp', 'gwwus_plugin_options' );
}


function gwwus_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
        
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    $data = Timber::get_context();
    switch ($_GET['action']) {
    case "post-install":
        Timber::render('templates/post-install.twig', $data);
        break;
    default:
        echo '<div class="wrap">';
        echo '<p>Here is where the form would go if I actually had options.</p>';
        //echo '<p>Doing sleep test....</p>';
        echo '</div>';
    }
    //sleep(121);
	
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


function gwwus_action_bulk_import() {
    if (!get_option( "gwwus-dkp_current-import-row", false )) {
        add_option( "gwwus-dkp_current-import-row", 0 );
        
    }
    if (get_option('gwwus-dkp_current-import-row') < 14401) {
        
    ?>
    	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var data = {
			'action': 'gwwus_action_bulk_import',
			'current_row': <?php echo get_option('gwwus-dkp_current-import-row');?>
		};

        $('#gwwus_admin_import_notice').html("Import beginning...<div class='spinner'></div>");
		$.post(ajaxurl, data, function(response) {
			console.log('Got this from the server: ' + response);
            eval(response);
		});
	});
	</script> <?php
          }
    
}
function gwwus_admin_import_notice() {
    ?>
    <div id="gwwus_admin_import_notice" class='updated'>
<p>test</p>        
    </div>
    <?php
}


add_action( 'wp_ajax_gwwus_action_bulk_import', 'gwwus_bulk_import_callback' );

function gwwus_bulk_import_callback() {
    //    echo "callback";
    
    global $wpdb; // this is how you get access to the database

	$current_row = intval( $_POST['current_row'] );

    if ($current_row < 14401) {
        $next_row = $current_row +  1000;
        if ($next_row > 14401)
            $next_row = 14401;

        $dir = plugin_dir_path( __FILE__ );
        $data = file($dir.'item_template_clean.sql');
        //echo $dir.'item_template_clean.sql';
        

        $table_name = $wpdb->prefix . "gwdkp_iteminfo"; 
        $sql = "";

        for ($i = $current_row; $i <= $next_row; $i++) {
            $current_stmt = $data[$i];
            
            $sql = "INSERT INTO $table_name ".$current_stmt;
                    $wpdb->query($sql);
        }
        //echo $sql;       
        
        
        

        update_option("gwwus-dkp_current-import-row", $next_row);
        //        echo "formatted_sql: $formatted_sql";
?>
        
        var data = {
            'action': 'gwwus_action_bulk_import',
            'current_row': <?php echo $next_row;?>
        };
        console.log("Importing rows from <?php echo"$current_row to $next_row";?>...");
        $('#gwwus_admin_import_notice').html("Importing rows from <?php echo"$current_row to $next_row";?>...<div class='spinner'></div>");
        setTimeout(function(){$.post(ajaxurl, data, function(response) {
		
            eval(response);
		})},2000);
        
        <?php
        //*/
    } else {
        ?>
        $('#gwwus_admin_import_notice').html("Importing completed!");
        <?php
    }	
        
	wp_die();
    exit();
}
?>
