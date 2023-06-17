<?php
class Custom_UP_WOrdpress_Updater implements UP_Updater {
	private $jsonCreator;
	private $upgrader;
	private $locale;
	
	public function __construct(){
		$this->jsonCreator = new jsonCreator();
		$this->upgrader = new Core_Upgrader(new Quiet_Skin());
		$this->locale = ITALIAN_PACK;
	}
	
	public function check(){
		$updates = get_core_updates();
		if ( isset( $updates[0]->version ) && version_compare( $updates[0]->version, get_bloginfo( 'version' ), '>' ) ) {
			return $updates[0]->version;
		}
		else{
			return null;
		}
	}
	
	public function update(){
		$updates = get_core_updates();
		$version = isset( $updates[0]->version ) ? $updates[0]->version : false;
		$update  = find_core_update( $version, $this->locale );
		
		$options = array(
			'sslverify' => true, 
			'timeout'   => 60,  
		);
		
		$result = $this->upgrader->upgrade($update, $options);
	
		if(is_wp_error($result)){
			echo $this->jsonCreator->createErrorJson($result->get_error_message().' - '. $version);
			exit();
		} else{
			echo $this->jsonCreator->createSuccessJson("Aggiornamento di Wordpress completato correttamente");
			exit();
		}
	}
}