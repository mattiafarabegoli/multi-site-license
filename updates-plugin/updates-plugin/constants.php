<?php

! defined( 'UPDATESPLUGIN_DIR_URL' ) && define( 'UPDATESPLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
! defined( 'UPDATESPLUGIN_VERSION' ) && define( 'UPDATESPLUGIN_VERSION', '1.1.1' );
! defined( 'UPDATESPLUGIN_NAME' ) && define ( 'UPDATESPLUGIN_NAME', 'Updates Plugin' );
! defined( 'ENDPOINT_SERVER' ) && define( 'ENDPOINT_SERVER', 'https://tirocini.kobold.studio/wp-json/up-updates/download' );
! defined( 'CUSTOMER' ) && define( 'CUSTOMER' , parse_url(get_site_url(), PHP_URL_HOST));
! defined( 'DESTINATION_URL_THEME' ) && define( 'DESTINATION_URL_THEME' , ABSPATH . 'wp-content/themes');
! defined( 'DESTINATION_URL_PLUGIN' ) && define( 'DESTINATION_URL_PLUGIN' , ABSPATH . 'wp-content/plugins');
! defined( 'PLUGIN_PAGE_URL' ) && define( 'PLUGIN_PAGE_URL' , ABSPATH . 'admin.php?page=updatesplugin');
$theme = wp_get_theme();
$parent_theme = wp_get_theme( get_template( ) );
! defined( 'CHILD_THEME' ) && define( 'CHILD_THEME' , $theme->get( 'Name' ));
! defined( 'PARENT_THEME' ) && define( 'PARENT_THEME' , $parent_theme->get( 'Name' ));
! defined( 'PARENT_THEME_VERSION' ) && define( 'PARENT_THEME_VERSION' , $parent_theme->get( 'Version' ));
! defined( 'ITALIAN_PACK' ) && define( 'ITALIAN_PACK' , 'it_IT');
! defined( 'ENGLISH_PACK' ) && define( 'ENGLISH_PACK' , 'en_US');



