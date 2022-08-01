<?php
/**
 * Plugin Name: Wimtorq Plugin
 * Plugin URI: https://github.com/mcdavidsh/wimtorg-plugin
 * Description: A test plugin that displays stripe client secret and client id in a wordpress post/page as a shortcode rendered in a table.
 * Version: 1.0
 * Author: Mcdavid Obioha
 * Author URI: https://github.com/mcdavidsh
 * Text Domain: wimtorq-plugin
 **/


use Stripe\StripeClient;

include plugin_dir_path( __FILE__ ) . 'includes/menu.php';
include plugin_dir_path( __FILE__ ) . 'includes/page-settings.php';
include plugin_dir_path( __FILE__ ) . 'modules/stripe-php/init.php';

add_action('admin_enqueue_scripts','mycustome_admin_style');
function mycustome_admin_style (){
	wp_enqueue_style( 'mystyle', plugin_dir_url(__FILE__). 'css/style.css');
}
function add_datatables_scripts() {
	wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array('jquery'), true);
    wp_enqueue_script('datatables_bootstrap', 'https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js', array('jquery'), true);
	wp_enqueue_script( 'wimtorq_stripe', plugin_dir_url(__FILE__). 'js/main.js', array(), '1.0', true );
	wp_enqueue_style('bootstrap_style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css');
	wp_enqueue_style('datatables_style', 'https://cdn.datatables.net/v/bs5/jq-3.6.0/datatables.min.css');
}

add_action('wp_enqueue_scripts', 'add_datatables_scripts');





add_shortcode( "wimtorq_stripe", "wimtorq_stripe_api" );
function wimtorq_stripe_api() {

	ob_start();
    ?>

	<table id="wimtorq-table" class="table table-striped table-hover">
		<thead>
		<tr>
			<th>S/N</th>
			<th>Price Name</th>
			<th>Currency</th>
			<th>Price</th>
		</tr>
		</thead>
	</table>
<?php
	return ob_get_clean();
}

function filter($input, $array) {
	$input = preg_quote($input, '~'); // don't forget to quote input string!

	return preg_grep('~' . $input . '~', $array);
}

add_action('wp_ajax_datatables_endpoint', 'wimtorq_fetch_stripe_data'); //logged in
add_action('wp_ajax_no_priv_datatables_endpoint', 'wimtorq_fetch_stripe_data'); //not logged in
function wimtorq_fetch_stripe_data(){
	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$rowperpage = $_POST['length'];
	$columnIndex = $_POST['order']["0"]["column"];
	$columnName = $_POST['columns'][$columnIndex]["data"];
	$columnOrder = $_POST['order']["0"]["dir"];
	$searchValue = $_POST['search']['value']; // Search value
	$columns = $_POST['columns'];

	$data = [];
	$init = new StripeClient(get_option('stripe_api_secret'));
	$stripe = $init->prices->all();
	$i = 1;
	$recordTotal = count($stripe);


	//$request['search']['value'] <= Value from search


	if (empty($searchValue)) {

		foreach ($stripe as $value):

			$data[] = array(
				"id" => $i,
				"price_name" => $value["product"],
				"price_currency" => $value["currency"],
				"price_amount" => $value["unit_amount"] / 100
			);
			$i++;
		endforeach;
	}
    elseif (!empty($searchValue)){


		$ssearch = filter($searchValue, $stripe["data"]);
		foreach ($ssearch as $value):

			$data[] = array(
				"id" => $i,
				"price_name" => $value["product"],
				"price_currency" => $value["currency"],
				"price_amount" => $value["unit_amount"] / 100
			);
			$i++;
		endforeach;

	}
	else {
		$data[] = array();
	}


	$return = array(
		"draw" => !empty ($draw) ? intval($draw) : 0,
		'recordsTotal' =>!empty( $recordTotal)?$recordTotal:0,
		'recordsFiltered' => !empty( $recordTotal)?$recordTotal:0,
		'data' => !empty($data)?$data:"",
	);

	wp_send_json($return);
}


