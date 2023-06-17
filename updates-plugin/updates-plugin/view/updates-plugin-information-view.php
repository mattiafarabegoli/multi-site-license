<?php
function display_up_plugin_info(){ ?>
	<p class="up-plugin-name"><span class="bold-text">Nome plugin: </span> <?php echo UPDATESPLUGIN_NAME; ?></p>
	<p class="up-plugin-name"><span class="bold-text">Versione plugin: </span> <?php echo UPDATESPLUGIN_VERSION; ?></p>
<?php
}
add_action( 'up_plugin_info', 'display_up_plugin_info' );

function show_up_updates($latest_version){ ?>

	<div class="container_updates">
		<p class="up_notice_updates"><span class="bold-text">Stato aggiornamenti: </span> Nuovo aggiornamento disponibile. Aggiorna subito alla versione <?php echo $latest_version; ?></p>
	</div>
	<a href="#" class="download-up-updates-button animation-button">
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		Aggiorna Updates Plugin</a>
	<p class="update-site-status"></p>

<?php
}
add_action( 'up_updates_found', 'show_up_updates', 1 );

function not_show_up_updates($message){ ?>

	<div class="container_updates"><p class="up_notice_updates"><span class="bold-text">Stato aggiornamenti: </span> <?php echo $message; ?></p></div>

<?php
}
add_action( 'up_updates_not_found', 'not_show_up_updates', 1 );