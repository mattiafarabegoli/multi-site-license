<?php
/********************************************************************************/
/* 		Invia dati del sito per tenere traccia degli aggiornamenti effettuati 	*/
/********************************************************************************/
function send_site_information(){
	global $wpdb;
	$request = new HTTPRequests();
	$jsonCreator = new jsonCreator();
	
	$data = array(
		'license_key' 			=> LICENSE_KEY,
		'customer' 				=> CUSTOMER,
		'action'				=> 'track', 
		'site_name'				=> get_bloginfo('name'),
		'site_version'			=> SITE_VERSION, 
		'up_plugin_version'		=> UPDATESPLUGIN_VERSION,
		'parent_theme_name'		=> PARENT_THEME,
		'parent_theme_version'	=> PARENT_THEME_VERSION, 
		'wp_version'			=> get_bloginfo( 'version' ),
		'php_version'			=> PHP_VERSION, 
		'database_version'		=> $wpdb->db_version(), 
		'timestamp'				=> time()
	);
	$response = $request->postRequest(ENDPOINT_SERVER, $data);
	
	if ( is_wp_error( $response ) ) {
		return $jsonCreator->createErrorJson($response->get_error_message());
	} else {
		if($request->checkResponseType( $response, 'application/json') && $request->checkResponseCode( $response )){
			return $jsonCreator->createSuccessJson("Invio dati completato");
		}
		else{
			return $jsonCreator->createErrorJson("La risposta alla richiesta HTTP non è quella attesa (Contenuto: ".$request->getResponseType( $response ).", Codice risposta: ".$request->getResponseCode( $response ).")");
		}
	}
}
add_action( 'information_sender', 'send_site_information' );

/********************************************************************************/
/* 		Registra l'evento di invio dei dati per eseguirlo una volta all'ora 	*/
/********************************************************************************/
function register_my_scheduled_event() {
    if ( ! wp_next_scheduled( 'information_sender' ) ) {
    	wp_schedule_event( time(), 'hourly', 'information_sender' );
	}
}
add_action( 'init', 'register_my_scheduled_event' );