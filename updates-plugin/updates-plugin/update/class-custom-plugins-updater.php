<?php
class Custom_Plugins_Updater implements UP_Updater {
	private $request;
	private $jsonCreator;
	private $url;
	private $nonce;
	private $upgrader;
	
	public function __construct(){
		$this->request = new HTTPRequests();
		$this->jsonCreator = new jsonCreator();
		$this->url = PLUGIN_PAGE_URL;
		$this->nonce = "bulk-update-plugins";
		$this->upgrader = new Plugin_Upgrader( new Quiet_Skin() );
	}
	
	public function check(){
		$updates = get_plugin_updates();
		if(!empty($updates)){
			return $this->jsonCreator->createSuccessJson($updates);
		}
		else{
			return $this->jsonCreator->createErrorJson("Nessun plugin da aggiornare"); 
		}
	}
	
	public function update(){
		check_ajax_referer( 'update_all_plugins', 'update_all_plugins_nonce' );
		$plugin_updates = get_plugin_updates();
		$plugins = array();
		
		if(!empty($plugin_updates)) {
			foreach ($plugin_updates as $plugin_update) {
				if(isset($plugin_update->update->package) && $plugin_update->update->package != ''){
					array_push($plugins, $plugin_update->update->plugin);
				}	
			}
		}
		
		$result = $this->upgrader->bulk_upgrade( $plugins );
		
		if(!is_wp_error($result)){
			echo $this->jsonCreator->createSuccessJson("Tutti i plugin sono stati aggiornati correttamente"); 
			exit;
		}
		else{
			echo $this->jsonCreator->createErrorJson("Errore durante il download dei plugin, perfavore riprova"); 	
			exit;
		}
	}
}

function get_custom_plugins_install(){
	$request = new HTTPRequests();
	$jsonCreator = new jsonCreator();
	
	$data = array(
		'license_key' 	=> get_license_key(),
		'customer' 		=> CUSTOMER,
		'update' 		=> 'custom-plugins',
		'action'		=> 'custom-install'
	);
	
	$response = $request->postRequest(ENDPOINT_SERVER, $data);
	
	if ( is_wp_error( $response ) ) {
		return $Jsoncreator->createErrorJson($response->get_error_message()); 
	} else {
		$response_body = wp_remote_retrieve_body( $response );
		$data = json_decode($response_body);
		
		return $data;
		
	}
}

function check_suggested_plugins(){
	$jsonCreator = new jsonCreator();
	$plugins = get_info_plugins_install();
	json_decode($plugins);
	if(json_last_error === 0){
		return $Jsoncreator->createErrorJson($response->get_error_message()); 
	}
	$plugin_slugs = array(); 
	$plugins_installed = get_plugins();
	foreach ($plugins as $plugin) {
		$isPresent = false;
		foreach($plugins_installed as $plugin_path => $plugin_installed){
			if ($plugin === dirname($plugin_path)){ 
				$isPresent = true;
			}
		}
		if(!$isPresent){
			array_push($plugin_slugs, $plugin);
		}
	}
	return $plugin_slugs;
}

function custom_download_plugins(){
	$jsonCreator = new jsonCreator();
	header('Content-Type: application/json');

	$plugins = get_info_plugins_install();
	
	class Quiet_Skin extends WP_Upgrader_Skin {
		public function feedback($string) {}
		public function header() {}
		public function footer() {}
	}

	$upgrader = new Plugin_Upgrader(new Quiet_Skin());
    foreach ($plugins as $slug) {
		$plugin_info = plugins_api('plugin_information', array('slug' => $slug));
		if(!is_wp_error( $plugin_info )){
			$result = $upgrader->install($plugin_info->download_link);
			if (!is_wp_error($result)) {
				$plugins_paths = get_plugins(); // recupera l'array di tutti i plugin installati
				foreach ($plugins_paths as $plugin_path => $plugin) {
					if (strpos($plugin_path, $slug . '/') !== false) {
						activate_plugin($plugin_path);
						break;
					}
				}
			} else {
				echo $Jsoncreator->createErrorJson('Errore nel download dei plugin, perfavore prova a riaggiornare la pagina e riprova' );

				die;
			}
		}
		else{
			if( download_plugin($slug)){
				if(confirm_installation_plugin( $slug )){
					$jsonCreator->createSuccessJson('La richiesta per il file ' . $slug . ' è stata effettuata correttamente' );
					die;	
				}
				else{
					$Jsoncreator->createErrorJson('La conferma non è stata effettuata correttamente' );
					die;
				}
			}
			else{
				echo json_encode( array( 'success' => false, 'message' => 'La richiesta non è stata effettuata correttamente' ));
				die;	
			}
		}
    }
	echo $jsonCreator->createSuccessJson('I plugin suggeriti sono stati correttamente scaricati e installati');
		
	die;
}
//add_action('wp_ajax_custom_download_plugins', 'custom_download_plugins');

function download_plugin($file_name){
	
	$request = new HTTPRequests();
	$jsonCreator = new jsonCreator();
	
	$data = array(
		'license_key' => LICENSE_KEY,
		'file_name' => $file_name,
		'customer' => CUSTOMER,
		'update' => 'custom-plugin',
		'action' => 'custom-install'
	);
	
	$response = $request->postRequest(ENDPOINT_SERVER, $data);
	
	if ( is_wp_error( $response ) ) {
		return false;
	} else {
		if($request->checkResponseType( $response, 'application/zip') && $request->checkResponseCode( $response )){
			$file_content = $response['body'];
			$destination = DESTINATION_URL_PLUGIN;

			$temp_file = tmpfile();
			fwrite($temp_file, $file_content);

			$package_uri = stream_get_meta_data($temp_file)['uri'];
			$upgrader = new Plugin_Upgrader(new Quiet_Skin());
			$result = $upgrader->install($package_uri, array('overwrite_package' => 'true'));
			
			if ( !is_wp_error( $result ) ) {
				return true;
			}
			else {
				return false;
			}
		}
		else{
			return false;
		}
	}
}

function confirm_installation_plugin($file_name){
	$request = new HTTPRequests();
	$jsonCreator = new jsonCreator();
	
	$data_confirm = array(
		'license_key' => LICENSE_KEY,
		'plugins' => $file_name,
		'customer' => CUSTOMER,
		'action' => 'confirm_installation'
	);
	
	$response_confirmation = $request->postRequest(ENDPOINT_SERVER, $data_confirm);

	if ( !is_wp_error( $response_confirmation ) ) {
		return true;
	}
	else{
		return false;
	}
}