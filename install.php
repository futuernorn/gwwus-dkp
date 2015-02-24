<?php
function gwwus_install () {
   global $wpdb;

   $table_name = $wpdb->prefix . "gwwus-dkp_bankitems"; 

$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  banker_id int(11) NOT NULL,
  item_name text NOT NULL,
  game_id int(11) NOT NULL,
  item_count int(11) DEFAULT '0' NOT NULL,
  color varchat(10) NOT NULL,
  icon text NOT NULL,
  location text NOT NULL,
  UNIQUE KEY id (item_id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

add_option( "gwwus-dkp_db_version", "0.0.1" );
}


?>
