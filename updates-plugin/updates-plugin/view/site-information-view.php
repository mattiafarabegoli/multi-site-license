<?php
function display_site_info(){ ?>
		
	<p class="site-name"><span class="bold-text">Nome del sito: </span> <?php echo get_bloginfo('name'); ?></p>
	<p class="site-version"><span class="bold-text">Versione del sito: </span> <?php echo SITE_VERSION; ?></p>
	
<?php
}
add_action( 'show_site_info', 'display_site_info' );
function show_site_updates($message){ ?>

	<div class="container_updates">
		<p class="up_notice_updates"><span class="bold-text">Stato aggiornamenti: </span> Nuovo aggiornamento disponibile. Aggiorna subito alla versione <?php echo $message; ?></p>
	</div>
	<a href="#" class="download-site-updates-button animation-button">
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		Aggiorna il sito</a>
	<p class="update-site-status"></p>

<?php
}
add_action( 'up_site_updates_found', 'show_site_updates', 1 );

function not_show_site_updates($message){ ?>

	<div class="container_updates"><p class="up_notice_updates"><span class="bold-text">Stato aggiornamenti: </span> <?php echo $message; ?></p></div>

<?php
}
add_action( 'up_site_updates_not_found', 'not_show_site_updates' );