<?php
class Custom_UP_Plugin_Updater implements UP_Updater {
	private $request;
	private $jsonCreator;
	private $destination;
	private $upgrader;
	
	public function __construct(){
		$this->request = new HTTPRequests();
		$this->jsonCreator = new jsonCreator();
		$this->upgrader = new Plugin_Upgrader(new Quiet_Skin());
	}
	
	public function check(){
		$data = array(
			'license_key' => get_license_key(),
			'file_name' => 'updates-plugin.json',
			'customer' => CUSTOMER,
			'update' => 'plugin',
			'action' => 'check'
		);
		$response = $this->request->postRequest(ENDPOINT_SERVER, $data);
		
		if ( is_wp_error( $response ) ) {
			return $this->jsonCreator->createErrorJson($response->get_error_message());
		} else {
			if($this->request->checkResponseType( $response, 'application/json') && $this->request->checkResponseCode( $response )){
				$response_body = wp_remote_retrieve_body( $response );
				$data = json_decode($response_body);

				if( $data->latest_version && version_compare($data->latest_version, UPDATESPLUGIN_VERSION, '>') ){	
					return $this->jsonCreator->createSuccessJson($data->latest_version);	
				}
				else{ 
					return $this->jsonCreator->createErrorJson("Nessun aggiornamento disponibile");
				}	
			}
			else{
				return $this->jsonCreator->createErrorJson("La risposta alla richiesta HTTP non è quella attesa (Contenuto: ".$this->request->getResponseType( $response ).", Codice risposta: ".$this->request->getResponseCode( $response ).")");
			}
		}
	}
	
	public function update(){
		$data = array(
			'license_key' => LICENSE_KEY,
			'file_name' => 'updates-plugin.zip',
			'customer' => CUSTOMER,
			'update' => 'plugin',
			'action' => 'update'
		);

		$response = $this->request->postRequest(ENDPOINT_SERVER, $data);
		
		if ( is_wp_error( $response ) ) {
			echo $this->jsonCreator->createErrorJson($response->get_error_message());
			exit;
		} else {
			if($this->request->checkResponseType( $response, 'application/zip') && $this->request->checkResponseCode( $response ) ){
				$file_content = $response['body'];
				
				$temp_file = tmpfile();
				fwrite($temp_file, $file_content);
				$package_uri = stream_get_meta_data($temp_file)['uri'];
				
				$result = $this->upgrader->install($package_uri, array('overwrite_package' => 'true'));

				if ( !is_wp_error( $result ) ) {
					echo $this->jsonCreator->createSuccessJson("Aggiornamenti di Updates plugin scaricati ed installati correttamente.");
					exit;
				}
				else {
					echo $this->jsonCreator->createErrorJson("Errore nello scampattamento del file ZIP.");
					exit;
				}
			}
			else{
				echo $this->jsonCreator->createErrorJson("La risposta alla richiesta HTTP non è quella attesa (Contenuto: ".$this->request->getResponseType().", Codice risposta: ".$this->request->getResponseCode().")");
				exit;
			}
		}
	}
}
