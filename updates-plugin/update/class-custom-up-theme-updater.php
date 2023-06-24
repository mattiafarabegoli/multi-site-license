<?php

class Custom_UP_Theme_Updater implements UP_Updater {
	private $jsonCreator;
	private $themeSlug;
	private $upgrader;
	
	public function __construct(){
		$this->jsonCreator = new jsonCreator();
		$this->themeSlug = 'generatepress';
		$this->upgrader = new Theme_Upgrader(new Quiet_Skin());
	}
	
	public function check(){
		$updates = get_site_transient( 'update_themes' );
		if (isset($updates->response[get_template()])) {
			return $updates->response[get_template()]['new_version'];
		} else {
			return null;
		}	
	}
	
	public function update(){	
		$result = $this->upgrader->upgrade($this->themeSlug);
	
		//ob_clean();
		
		if(is_wp_error($result)){
			echo $this->jsonCreator->createErrorJson("Errore nell'aggiornamento del tema");
			exit();
		} else{
			echo $this->jsonCreator->createSuccessJson("Aggiornamento del tema completato correttamente");
			exit();
		}
	}
}