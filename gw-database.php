<?php
function gwwus_install () {
   global $wpdb;

   $bankitems_table_name = $wpdb->prefix . "gwdkp_bankitems";
   $itemtemplate_table_name = $wpdb->prefix . "gwdkp_iteminfo"; 

   $wpdb->query("DROP TABLE IF EXISTS $bankitems_table_name");
   $wpdb->query("DROP TABLE IF EXISTS $itemtemplate_table_name");   

   $charset_collate = $wpdb->get_charset_collate();

   $sql = <<<SQL
       CREATE TABLE $bankitems_table_name (
           item_id int(11) NOT NULL AUTO_INCREMENT,
           banker_id int(11) NOT NULL,
           item_name text NOT NULL,
           game_id int(11) NOT NULL,
           item_count int(11) DEFAULT '0' NOT NULL,
           color varchar(10) NOT NULL,
           icon text NOT NULL,
           location text NOT NULL,
           UNIQUE KEY id (item_id)
       ) $charset_collate;

CREATE TABLE $itemtemplate_table_name (
  `entry` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `class` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `subclass` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `InventoryType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ItemLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `RequiredLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`entry`),
  KEY `items_index` (`class`)
) $charset_collate

SQL;

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );

   
   add_option( "gwwus-dkp_db_version", "0.0.1" );
}

function gwwus_install_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . "gwdkp_bankitems";
    $sql = <<<SQL
INSERT INTO $table_name (`item_id`, `banker_id`, `item_name`, `game_id`, `item_count`, `color`, `icon`, `location`) VALUES(139786, 1, 'Earthroot', 2449, 82, 'ffffff', 'Interface\\Icons\\INV_Misc_Herb_07', 'Char:Bag4');
SQL;
    $wpdb->query($sql);

    

    
}


?>
