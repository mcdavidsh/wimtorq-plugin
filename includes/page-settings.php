<?php





if( !function_exists("wimtorq_settings") ) {

	function wimtorq_settings(){
		?>
<h1>Wimtorq Plugin Settings</h1>
		<div class="mycontainer">

	<form method="post">
        <label >stripe API Secret</label>
        <br>
        <br>
        <input type="text" name="stripe-secret" value="<?php echo get_option("stripe_api_secret");?>" class="regular-text" placeholder="Enter Stripe Api Secret">
        <br>
        <br>
        <br>
        <label >stripe API Client</label>
        <br>
        <br>
        <input type="text" name="stripe-client" value="<?php echo get_option("stripe_api_client");?>" class="regular-text" placeholder="Enter Stripe Client ID">
        <br>
        <br>
        <br>
        <input class="button-primary" type="submit" name="submit" value="<?php esc_attr_e( 'Submit' ); ?>" />
    </form>
		</div>
<?php
	}
	if (isset($_POST["submit"])){

		$secret = $_POST["stripe-secret"];
		$client = $_POST["stripe-client"];

        if (!get_option("stripe_api_stripe") && !get_option("stripe_api_client")){
	        add_option( 'stripe_api_client' , $client , '' , 'no');
	        add_option( 'stripe_api_secret' , $secret , '' , 'no');
	        add_action('admin_notices', 'insert_notice');
        }else {
	        update_option( 'stripe_api_client' , $client );
	        update_option( 'stripe_api_secret' , $secret );
	        add_action('admin_notices', 'update_notice');
        }

	}


}
function insert_notice(){

	?>
    <div class="notice notice-success is-dismissible">
        <p>API Keys Saved successfully!</p>
    </div>
<?php }
function update_notice(){

	?>
    <div class="notice notice-success is-dismissible">
        <p>API Keys Updated successfully!</p>
    </div>
<?php }
function failure_notice(){

	?>
    <div class="notice notice-error is-dismissible">
        <p>API Keys failed successfully!</p>
    </div>
<?php }


