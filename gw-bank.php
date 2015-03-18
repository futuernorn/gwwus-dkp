<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('gw-constants.php');

function gwwus_generate_inventory_table() {
    global $wpdb, $subClass, $inventoryType;
    $bank_table_name = $wpdb->prefix . "gwdkp_bankitems";
     
    $itemtemplate_table_name = $wpdb->prefix . "gwdkp_iteminfo";
    $sql = "SELECT * FROM $bank_table_name LEFT JOIN $itemtemplate_table_name ON (entry = game_id);";
         // error_log($sql);
    $bankItems = $wpdb->get_results( $sql  );
    
    foreach ( $bankItems as $bankItem ) {
        
        
        $inventoryTypeNum = $bankItem->InventoryType;
        $bankItem->inventoryTypeTxt = $inventoryType[$inventoryTypeNum];
        $bankItem->subClassTxt = $subClass[$bankItem->subclass][$bankItem->class];
        $bankItem->qualityTxt = gwwus_get_quality_text($bankItem->color);
    }
    $data = Timber::get_context();
    $data['bank_items'] = $bankItems;
    
    Timber::render('templates/bank-table.twig', $data);
}
add_shortcode( 'gwwus_bank_table', 'gwwus_generate_inventory_table' );

function gwwus_get_quality_text($color) {
  $quality = "";
  switch ($color) {
    case "1eff00":
      $quality = "Uncommon";
      break;
    case "ffffff":
      $quality = "Common";
      break;
    case "0070dd":
      $quality = "Rare";
      break;
    case "a335ee":
      $quality = "Epic";
      break;
    default:
      $quality = "It'sAMystery";
  }
  return $quality;
}
?>
