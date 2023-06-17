<?php
/**
 * Verify license input.
 */
function check_license_input(){
	global $wpdb;
	$http_request = new HTTPRequests();
	$jsonCreator = new jsonCreator();
	
	$license_key = $_POST['license'];
	$data = array(
		'license_key' => $license_key,
		'customer' => CUSTOMER,
		'action' => 'check-license',
	);
	
	$response = $http_request->postRequest(ENDPOINT_SERVER, $data);
	
	if ( is_wp_error( $response ) ) {
		echo $jsonCreator->createErrorJson($response->get_error_message());
		exit;
	} else {
		if($http_request->checkResponseType( $response, 'application/json') && $http_request->checkResponseCode( $response )){
			$response_body = wp_remote_retrieve_body( $response );
			
			$data = json_decode($response_body);
			if( $data->result == 'success' && $data->verified == 'yes' ){		
				$table_name = $wpdb->prefix . 'my_plugin_licenses';
				$wpdb->query($wpdb->query("TRUNCATE TABLE $table_name"));
				$sql_license_database = "INSERT INTO $table_name (license_key, license_owner) VALUES (%s, %s)";
				$wpdb->query($wpdb->prepare($sql_license_database, $license_key, CUSTOMER));
			}
		}
		echo $jsonCreator->createSuccessJson("La chiave di licenza è valida");
		exit;
	}
}
add_action( 'wp_ajax_check_license_input', 'check_license_input' );

/**
 * Verify license from database
 */
function check_license(){
	$jsonCreator = new jsonCreator();
	$http_request = new HTTPRequests();
	global $wpdb;
	$license_key = get_license_key();

	if(empty($license_key)){
		return $jsonCreator->createErrorJson("La chiave inserita non è valida");
	}

	$data = array(
		'license_key' => $license_key,
		'customer' => CUSTOMER,
		'action' => 'check-license',
	);
	
	$response = $http_request->postRequest( ENDPOINT_SERVER, $data );
	if ( is_wp_error( $response ) ) {
		return $jsonCreator->createErrorJson("La chiave inserita non è valida");
	} else {
		if($http_request->checkResponseType( $response, 'application/json') && $http_request->checkResponseCode( $response )){
			$response_body = wp_remote_retrieve_body( $response );

			$data = json_decode($response_body);

			if( $data->result == 'success' && $data->verified == 'yes' ){		
				return $jsonCreator->createSuccessJson("La chiave di licenza è valida");
			}
			else { 
				$sql_delete_license = "DELETE FROM $table_name WHERE license_owner = %s";
				$delete_results = $wpdb->get_results($wpdb->prepare($sql_delete_license, CUSTOMER));

				return $jsonCreator->createErrorJson("La chiave di licenza non è più valida");
			}
		}
	}
}

function get_license_key(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'my_plugin_licenses';
	$sql_get_license = "SELECT license_key FROM $table_name WHERE license_owner = %s";
	$license_key_results = $wpdb->get_results($wpdb->prepare($sql_get_license, CUSTOMER));
	return $license_key_results[0]->license_key;
}