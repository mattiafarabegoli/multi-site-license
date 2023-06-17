<?php
function display_wordpress_informations(){ 
	global $wpdb; ?>
	<p class="wordpress-version"><span class="bold-text">Versione di Wordpress: </span> <?php echo get_bloginfo( 'version' ); ?></p>
	<p class="php-version"><span class="bold-text">Versione di PHP: </span> <?php echo PHP_VERSION; ?></p>
	<p class="database-version"><span class="bold-text">Versione del database MySQL: </span> <?php echo $wpdb->db_version(); ?></p>
<?php
}
add_action( 'up_wordpress_informations', 'display_wordpress_informations' );

function display_wordpress_updates($latest_version){ ?>
	<p class="wordpress-updates"><span class="bold-text">Stato aggiornamento: </span>Nuovo aggiornamento disponibile. Aggiorna subito alla versione <?php echo $latest_version; ?></p>
	<a href="#" class="update-wordpress-button animation-button">
	  <span></span>
	  <span></span>
	  <span></span>
	  <span></span>
	  Aggiorna Wordpress
	</a>
<?php
}
add_action( 'up_wordpress_updates', 'display_wordpress_updates', 1 );

function not_display_wordpress_updates(){ ?>
	<p class="wordpress-updates"><span class="bold-text">Stato aggiornamento: </span>Aggiornato</p>
<?php
}
add_action( 'up_not_wordpress_updates', 'not_display_wordpress_updates' );