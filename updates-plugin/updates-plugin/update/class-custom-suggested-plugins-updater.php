<?php
class Custom_Suggested_Plugins_Updater implements UP_Updater {
	private $request;
	private $jsonCreator;
	private $upgrader;
	private $destination;
	
	public function __construct() {
		$this->request = new HTTPRequests();
		$this->jsonCreator = new jsonCreator();
		$this->upgrader = new Plugin_Upgrader(new Quiet_Skin());
		$this->destination = DESTINATION_URL_PLUGIN;
	}
	
	public function check(){
		$data = array(
			'license_key' 	=> get_license_key(),
			'customer' 		=> CUSTOMER,
			'update' 		=> 'plugins',
			'action'		=> 'install'
		);
		
		$response = $this->request->postRequest(ENDPOINT_SERVER, $data);
		
		if ( is_wp_error( $response ) ) {
			return $this->jsonCreator->createErrorJson($response->get_error_message());
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			$plugins = json_decode($response_body);

			$plugin_slugs = $this->get_plugin_slugs($plugins);
			if(!empty($plugin_slugs)){
				return $this->jsonCreator->createSuccessJson($plugin_slugs);
			}
			return $this->jsonCreator->createErrorJson("Nessun plugin da installare al momento");
		}
	}
	
	public function update() {
		$plugins_data = json_decode($this->check());
		
		$plugins = $plugins_data->message;
		
		foreach ($plugins as $slug) {
			$plugin_info = plugins_api('plugin_information', array('slug' => $slug));
			if(!is_wp_error( $plugin_info )){
				$this->download_plugin_from_wordpress($plugin_info);
			}
			else{
				if( !$this->download_plugin_from_web($slug)){
					echo $this->jsonCreator->createErrorJson('La richiesta non è stata effettuata correttamente' );
					exit;	
				}
			}
		}
		echo $this->jsonCreator->createSuccessJson( 'Tutti i plugin sono stati correttamente installati' );
		exit;
	}
	
	private function get_plugin_slugs($plugins){
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
	
	private function download_plugin_from_wordpress($plugin_info){
		$result = $this->upgrader->install($plugin_info->download_link);
		if (!is_wp_error($result)) {
			$plugins_paths = get_plugins(); // recupera l'array di tutti i plugin installati
			foreach ($plugins_paths as $plugin_path => $plugin) {
				if (strpos($plugin_path, $plugin->slug . '/') !== false) {
					activate_plugin($plugin_path);
					break;
				}
			}
		} else {
			echo $this->jsonCreator->createErrorJson('Errore nel download dei plugin, perfavore prova a riaggiornare la pagina e riprova' );
			exit;
		}
	}
	
	private function download_plugin_from_web($file_slug){
		$data = array(
			'license_key' => LICENSE_KEY,
			'file_name' => $file_slug,
			'customer' => CUSTOMER,
			'update' => 'custom-plugin',
			'action' => 'custom-install'
		);
		
		$response = $this->request->postRequest(ENDPOINT_SERVER, $data);
		
		if ( !is_wp_error( $response ) ) {
			if($this->request->checkResponseType( $response, 'application/zip') && $this->request->checkResponseCode( $response )){
				$file_content = $response['body'];

				$temp_file = tmpfile();
				fwrite($temp_file, $file_content);
				$package_uri = stream_get_meta_data($temp_file)['uri'];
				
				$result = $this->upgrader->install($package_uri, array('overwrite_package' => 'true'));

				if ( !is_wp_error( $result ) ) {
					if($this->confirm_plugin_installation($file_slug)){
						return true;	
					}
				}
			} 
		}
		return false;
	}
	
	private function confirm_plugin_installation($file_name){
		$data_confirm = array(
			'license_key' => LICENSE_KEY,
			'plugins' => $file_name,
			'customer' => CUSTOMER,
			'action' => 'confirm_installation'
		);
		$response_confirmation = $this->request->postRequest(ENDPOINT_SERVER, $data_confirm);

		if ( !is_wp_error( $response_confirmation ) ) {
			return true;
		}
		else{
			return false;
		}
	}
}