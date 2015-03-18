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
  entry mediumint(8) unsigned NOT NULL DEFAULT '0',
  class tinyint(3) unsigned NOT NULL DEFAULT '0',
  subclass tinyint(3) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  Quality tinyint(3) unsigned NOT NULL DEFAULT '0',
  InventoryType tinyint(3) unsigned NOT NULL DEFAULT '0',
  AllowableClass mediumint(9) NOT NULL DEFAULT '-1',
  ItemLevel tinyint(3) unsigned NOT NULL DEFAULT '0',
  RequiredLevel tinyint(3) unsigned NOT NULL DEFAULT '0',
  RequiredSkill smallint(5) unsigned NOT NULL DEFAULT '0',
  RequiredSkillRank smallint(5) unsigned NOT NULL DEFAULT '0',
  maxcount smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (entry),
  KEY items_index (class)
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
INSERT INTO $table_name (item_id, banker_id, item_name, game_id, item_count, color, icon, location) VALUES(139786, 1, 'Earthroot', 2449, 82, 'ffffff', 'Interface\\Icons\\INV_Misc_Herb_07', 'Char:Bag4');
SQL;
    $wpdb->query($sql);

    $itemtemplate_table_name = $wpdb->prefix . "gwdkp_iteminfo"; 

    $dir = plugin_dir_path( __FILE__ );
    $item_template_csv_file = fopen($dir.'item_template_clean.sql', 'r');
    $item_template_csv = fgetcsv($item_template_csv_file);
    foreach($current_row as $item_template_csv) {
        $wpdb->insert( 
            $itemtemplate_table_name, 
            array(
                'entry' => $current_row[0],
                'class' => $current_row[1],
                'subclass' => $current_row[2],
                'name' => $current_row[3],
                'Quality' => $current_row[4],
                'InventoryType' => $current_row[5],
                'AllowableClass' => $current_row[6],
                'ItemLevel' => $current_row[7],
                'RequiredLevel' => $current_row[8],
                'RequiredSkill' => $current_row[9],
                'RequiredSkillRank' => $current_row[10],
                'maxcount' => $current_row[11]
            ) 
        );
    }

    fclose($item_template_csv_file);

    

    
}


?>
