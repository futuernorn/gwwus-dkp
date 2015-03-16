<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function gwwus_generate_inventory_table() {
    global $wpdb;
    $bank_table_name = $wpdb->prefix . "gwdkp_bankitems";
     
    $itemtemplate_table_name = $wpdb->prefix . "gwdkp_iteminfo";
    $sql = "SELECT * FROM $bank_table_name LEFT JOIN $itemtemplate_table_name ON (entry = game_id);";
         error_log($sql);
    $bankItems = $wpdb->get_results( $sql  );

    foreach ( $bankItems as $bankItem ) {
        //echo $fivesdraft->post_title;
    }
    $data = Timber::get_context();
    $data['bank_items'] = $bankItems;
    Timber::render('templates/bank-table.twig', $data);
}
add_shortcode( 'gwwus_bank_table', 'gwwus_generate_inventory_table' );
?>
