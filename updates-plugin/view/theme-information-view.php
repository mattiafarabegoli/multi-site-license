<?php
function display_theme_informations(){ ?>
	<p class="child-theme-name"><span class="bold-text">Tema attivo: </span> <?php echo CHILD_THEME; ?></p>
	<p class="parent-theme-name"><span class="bold-text">Tema genitore: </span> <?php echo PARENT_THEME; ?></p>
	<p class="parent-theme-version"><span class="bold-text">Versione tema genitore: </span> <?php echo PARENT_THEME_VERSION; ?></p>
<?php
}
add_action( 'up_theme_informations', 'display_theme_informations' );

function display_theme_updates($latest_version){ ?>
	<p class="parent-theme-updates"><span class="bold-text">Stato aggiornamento tema genitore: </span>Nuovo aggiornamento disponibile. Aggiorna subito alla versione <?php echo $latest_version; ?></p>
	<a href="#" class="update-theme-button animation-button">
	  <span></span>
	  <span></span>
	  <span></span>
	  <span></span>
	  Aggiorna il tema
	</a>
<?php
}
add_action( 'up_theme_updates', 'display_theme_updates' );

function not_display_theme_updates(){ ?>
	<p class="parent-theme-updates"><span class="bold-text">Stato aggiornamento tema genitore: </span>Aggiornato all'ultima versione</p>
<?php
}
add_action( 'up_not_theme_updates', 'not_display_theme_updates' );