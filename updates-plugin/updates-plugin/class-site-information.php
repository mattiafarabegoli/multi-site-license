<?php

require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-site-up-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-plugin-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-plugins-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-theme-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-wordpress-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-suggested-plugins-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-json-creator.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-http-request.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-quiet-skin.php';

/**
 * Class SiteInformation manage dashboard view.
 * 
 */
class SiteInformation {
	private $notifications;
	private $site_updater;
	private $up_updater;
	private $theme_updater;
	private $wordpress_updater;
	private $plugins_updater;
	private $suggested_plugins_updater;
	/**
	 * Display site status.
	 * 
	 */
	public function __construct(){
		$this->site_updater = new Custom_Site_UP_Updater();
		$this->up_updater = new Custom_UP_Plugin_Updater();
		$this->theme_updater = new Custom_UP_Theme_Updater();
		$this->wordpress_updater = new Custom_UP_Wordpress_Updater();
		$this->plugins_updater = new Custom_Plugins_Updater;
		$this->suggested_plugins_updater = new Custom_Suggested_Plugins_Updater();
	}
	
	private function showSiteStatus(){
		echo '<h2 class="site-updates-title">Stato del sito</h2>';
		do_action( 'show_site_info' );
		$toUpdate = json_decode($this->site_updater->check());
		if($toUpdate->success == true){
			do_action( 'up_site_updates_found', $toUpdate->message);
			$this->increaseNotifications();
		} else{
			do_action( 'up_site_updates_not_found', $toUpdate->message);
		}
	}
	
	/**
	 * Display UP plugin status.
	 * 
	 */ 
	private function showUPPLuginStatus(){
		echo '<h2 class="site-licence-title">Stato Updates Plugin</h2>';
		do_action( 'up_plugin_info' );
		$up_updates = json_decode($this->up_updater->check());
								  
		if($up_updates->success == true){
			do_action( 'up_updates_found', $up_updates->message );
			$this->increaseNotifications();
		}
		else{
			do_action( 'up_updates_not_found', $up_updates->message );
		}
	}
	
	/**
	 * Display theme status.
	 * 
	 */
	private function showThemeStatus(){
		echo '<h2 class="theme-updates-title">Aggiornamenti del tema</h2>';
		do_action( 'up_theme_informations' ); 

		$theme_updates = $this->theme_updater->check();
		if($theme_updates != null){
			do_action( 'up_theme_updates', $theme_updates ); 
			$this->increaseNotifications();
		}
		else{
			do_action( 'up_not_theme_updates' );
		}
	}
	
	/**
	 * Display Wordpress status.
	 * 
	 */
	private function showWordpressStatus(){
		echo '<h2 class="wordpress-updates-title">Stato Wordpress</h2>';
		do_action( 'up_wordpress_informations' ); 
		$wordpress_updates = $this->wordpress_updater->check();
		echo $wordpress_updates;
		if($wordpress_updates != null){
			do_action( 'up_wordpress_updates', $wordpress_updates ); 
			$this->increaseNotifications();
		}
		else{
			do_action( 'up_not_wordpress_updates' );
		}
	}
	
	/**
	 * Display installed plugins status.
	 * 
	 */
	private function showInstalledPluginStatus(){
		echo '<h2 class="plugin-updates-title">Aggiornamenti dei plugin</h2>';
		$check = json_decode($this->plugins_updater->check());
		if($check->success == true){
			$this->increaseNotifications();
		}
		do_action( 'up_plugins_info', $check->message );
	}
	
	/**
	 * Display suggested plugins status.
	 * 
	 */
	private function showSuggestedPluginStatus(){
		echo '<h2 class="suggested-plugins-title">Plugin suggeriti</h2>';
			
		$suggested_plugins = json_decode($this->suggested_plugins_updater->check());
		if($suggested_plugins->success === true){
			do_action( 'up_suggested_plugins' , $suggested_plugins->message);
		}
		else {
			do_action( 'up_not_suggested_plugins' );
		}
	}
	
	/**
	 * Display content.
	 * 
	 */
	public function renderDashboard(){ ?>
		
		<div class="container-all-updates">
			<div class="container-site-status">
				
				<?php $this->showSiteStatus(); ?>
				
			</div>
			<div class="container-up-status">
				
				<?php $this->showUPPLuginStatus(); ?>
				
			</div>
			<div class="container-theme-status">
				
				<?php $this->showThemeStatus(); ?>
				
			</div>
			<div class="container-wordpress-status">
				
				<?php $this->showWordpressStatus(); ?>
				
			</div>
			<div class="container-plugin-status">
			
				<?php $this->showInstalledPluginStatus(); ?>
				
			</div>
			<div class="container-suggested-plugins-status">
				
				<?php $this->showSuggestedPluginStatus(); ?>
				
			</div>
		</div>

<?php }
	
	/**
	 * Display content if license not owned.
	 * 
	 */
	public function renderNoLicenseDashboard($message){ ?>
	
		<div class="container-no-license">
			<input type="password" class="license-key-form" />
			<a href="#" class="insert-license-key-button animation-button">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			Conferma</a>
		</div>
		<p class="license-incorrect-message"><?php echo $message; ?></p>

<?php }
	
	/**
	 * Set notifications transient.
	 * 
	 */
	public function setTransient(){
		if ( $this->notifications > 0){
			set_transient( 'up_plugin_notifications', $this->notifications );
		}
	}
	
	/**
	 * increase notifications.
	 * 
	 */
	private function increaseNotifications(){
		$this->notifications++;
	}
}

$siteInformation = new SiteInformation();

/**
 * add actions.
 * 
 */
add_action( 'site_informations_updates_plugin', [$siteInformation, 'renderDashboard']);
add_action( 'site_informations_updates_plugin', [$siteInformation, 'setTransient']);
add_action( 'site_informations_updates_plugin_no_license', [$siteInformation, 'renderNoLicenseDashboard']);
