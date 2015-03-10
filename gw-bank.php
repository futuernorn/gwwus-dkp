<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function gwwus_generate_inventory_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "gwdkp_bankitems"; 
    $bankItems = $wpdb->get_results( 
        "
	SELECT * 
	FROM $table_name
	"
    );

    foreach ( $bankItems as $bankItem ) {
        //echo $fivesdraft->post_title;
    }
    $data = Timber::get_context();
    $data['bank_items'] = $bankItems;
    Timber::render('templates/bank-table.twig', $data);
}
add_shortcode( 'gwwus_bank_table', 'gwwus_generate_inventory_table' );
?>
