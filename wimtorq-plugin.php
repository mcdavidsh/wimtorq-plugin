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


include plugin_dir_path( __FILE__ ) . 'includes/menu.php';
include plugin_dir_path( __FILE__ ) . 'includes/page-settings.php';
include plugin_dir_path( __FILE__ ) . 'modules/stripe-php/init.php';


function add_mystyle_script() {
	wp_enqueue_script( 'wimtorq_stripe', plugin_dir_url(__FILE__). 'js/script.js', array(), '1.0', true );
	wp_enqueue_style( 'mystyle', plugin_dir_url(__FILE__). 'css/style.css', array(), '1.0', true);
}

function add_datatables_scripts() {
	wp_register_script('datatables', 'https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array('jquery'), true);
	wp_enqueue_script('datatables');

	wp_register_script('datatables_bootstrap', 'https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js', array('jquery'), true);
	wp_enqueue_script('datatables_bootstrap');
}

function add_datatables_style() {
	wp_register_style('bootstrap_style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css');
	wp_enqueue_style('bootstrap_style');

	wp_register_style('datatables_style', 'https://cdn.datatables.net/v/bs5/jq-3.6.0/datatables.min.css');
	wp_enqueue_style('datatables_style');
}

add_action('wp_enqueue_scripts', 'add_datatables_scripts');
add_action('wp_enqueue_scripts', 'add_datatables_style');
add_action( 'wp_enqueue_scripts', 'add_mystyle_script' );

add_shortcode( "wimtorq-stripe", "wimtorq_stripe_api" );
function wimtorq_stripe_api() {


	$stripe = new \Stripe\StripeClient(
		get_option('stripe_api_secret')
	);
    if ($stripe !== null){
	$str_inf = $stripe->prices->all();
    $i=1;
    ob_start();

    ?>

	<table id="movietable" class="table table-striped table-hover">
		<thead>
		<tr>
			<th>S/N</th>
			<th>Price Name</th>
			<th>Currency</th>
			<th>Price</th>
		</tr>
		</thead>

        <tbody>
        <?php
		foreach ( $str_inf  as $datum ) {
            $name = ($datum['nickname'] !== null)?$datum['nickname'] :$datum['product'];
 ?>    <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $name ?></td>
            <td><?php echo strtoupper($datum['currency']) ?></td>
            <td><?php echo $datum['unit_amount']/100 ?></td>
        </tr>

        <?php    $i++; } ?>
        </tbody>
	</table>
	<?php
	return ob_get_clean();
    }else {
        throw new Exception('Invalid Stripe Secret Key');
    }
}


