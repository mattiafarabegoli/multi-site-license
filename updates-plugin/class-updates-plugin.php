<?php
defined( 'ABSPATH' ) || exit;

! defined(' ACTIVE_THEME_FOLDER' ) && define( 'ACTIVE_THEME_FOLDER', get_option('stylesheet') );

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/constants.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/license.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/view/site-information-view.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/view/plugins-information-view.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/view/updates-plugin-information-view.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/view/theme-information-view.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/view/wordpress-view.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/send-informations.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-quiet-skin.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/interface-up-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-json-creator.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-http-request.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/class-site-information.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-site-up-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-plugin-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-plugins-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-theme-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-up-wordpress-updater.php';
require_once ABSPATH . 'wp-content/plugins/updates-plugin/update/class-custom-suggested-plugins-updater.php';

/**
 * Class that manage Plugin Menu
 * 
 */
if( ! class_exists( 'UpdatesPluginMenu') ) {
	class UpdatesPluginMenu {
		
		/**
		 * Constructor
		 * 
		 */
    	function __construct() {
        	add_action( 'admin_menu', [$this, 'admin_menu' ] );
			
			$site_updater = new Custom_Site_UP_Updater();
			$up_updater = new Custom_UP_Plugin_Updater();
			$theme_updater = new Custom_UP_Theme_Updater();
			$wordpress_updater = new Custom_UP_Wordpress_Updater();
			$plugins_updater = new Custom_Plugins_Updater();
			$custom_suggested_plugins_updater = new Custom_Suggested_Plugins_Updater();
			
			add_action( 'wp_ajax_download_site_updates', [$site_updater, 'update']);
			add_action( 'wp_ajax_download_and_update_updates_plugin', [$up_updater, 'update']);			
			add_action( 'wp_ajax_custom_update_theme', [$theme_updater, 'update']);
			add_action( 'wp_ajax_custom_update_wordpress', [$wordpress_updater, 'update']);
			add_action( 'wp_ajax_update_all_plugins', [$plugins_updater, 'update']);
			add_action( 'wp_ajax_custom_download_plugins', [$custom_suggested_plugins_updater, 'update']);
        }

		/**
		 * Creation of menu with notifications number
		 * 
		 */
        function admin_menu() {
			$notifications = get_transient( 'up_plugin_notifications' );
			
            add_menu_page(
                'UpdatesPlugin',
				$notifications ? sprintf('Updates <span class="update-plugins up-update-count"><span class="update-count">%d</span></span>', $notifications) : 'Updates Plugin',
                'manage_options',
                'updatesplugin',
                [ $this, 'updates_plugin_content' ],
                'dashicons-admin-generic',
                80
            );
        }

		/**
		 * Create structure of dashboard and call Hook based on License check
		 * 
		 */
        function updates_plugin_content() { ?>

			<div class="container-plugin-dashboard">
				<p class="result-message-dashboard"></p>
				<div class="container-dashboard">
			
					<?php
					$result_check = json_decode(check_license());
			
					if( $result_check->success == true ){
						do_action( 'site_informations_updates_plugin' );
					}
					else {
						do_action( 'site_informations_updates_plugin_no_license', $result_check->message );
					} ?>
				
				</div>
			</div>
<?php  	}
    }
}
new UpdatesPluginMenu();
