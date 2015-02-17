<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function gwwus_generate_inventory_table() {
$data = Timber::get_context();
Timber::render('templates/bank-table.twig', $data);
}
add_shortcode( 'gwwus_bank_table', 'gwwus_generate_inventroy_table' );
?>
