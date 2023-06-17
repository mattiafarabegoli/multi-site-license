<?php
/**
 * Method to display a table of all plugins to be updated.
 */
function get_info_plugins_updates($plugin_updates){

	if ( ! empty( $plugin_updates ) ) { ?>
    
		<table class="widefat">
		<thead><tr><th>Plugin</th><th>Versione corrente</th><th>Versione disponibile</th></tr></thead>
		<tbody>

		<?php foreach ( $plugin_updates as $plugin_update ) {
        	$plugin_name = $plugin_update->Name;
        	$current_version = $plugin_update->Version;
        	$new_version = $plugin_update->update->new_version;

        	echo '<tr class="plugin-table-headers"><td>' . esc_html( $plugin_name ) . '</td><td>' . esc_html( $current_version ) . '</td><td>' . esc_html( $new_version ) . '</td></tr>';
    	} ?>
    
		</tbody>
		</table>

    
		<form id="update-all-plugins" method="post">
			<input type="hidden" name="action" value="update_all_plugins">
			<?php wp_nonce_field( 'update_all_plugins', 'update_all_plugins_nonce' ); ?>
			<input type="submit" value="Aggiorna tutti i plugin" class="update-plugins-button">
			<span class="first-span"></span>
			<span class="second-span"></span>
			<span class="third-span"></span>
			<span class="fourth-span"></span>
		</form>
		<div class="update-process"></div>

<?php
	} else { ?>

    	<p class="no-plugin-updates"><span class="bold-text">Aggiornamenti: </span>Tutti i plugin sono aggiornati</p>

	<?php }
}
add_action( 'up_plugins_info', 'get_info_plugins_updates', 1 );

/**
 * Method to display a table of all plugins to be installed.
 */
function display_suggested_plugins($plugin_slugs){
		?>
	
			<table class="widefat">
			<thead><tr><th>Plugin</th><th>Versione</th><th>Autore</th></tr></thead>
			<tbody>
				
			<?php foreach ($plugin_slugs as $slug) { 
				$args = array('slug' => $slug);
				$plugin_info = plugins_api('plugin_information', $args ); 
				if(!is_wp_error($plugin_info)) { ?>
				
				<tr class="plugin-table-headers"><td><?php echo $plugin_info->name; ?></td><td><?php echo $plugin_info->version; ?></td><td><?php echo $plugin_info->author; ?></td></tr>
				
				<?php } else { ?>
				
					<tr class="plugin-table-headers"><td><?php echo $slug; ?></td><td>-</td><td>Custom</td></tr>
				
				<?php }
				
			} ?>
			</tbody>
			</table>
			<a href="#" class="download-suggested-plugins-button animation-button">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			Scarica plugin suggeriti</a>

<?php }
add_action( 'up_suggested_plugins', 'display_suggested_plugins' );

/**
 * Method to display the message 'No plugin to be updated'.
 */
function not_display_suggested_plugins(){ ?>
			
			<p class="no-plugin-suggested"><span class="bold-text">Nuovi plugin: </span>Nessun nuovo plugin da installare al momento</p>

		<?php }	
add_action( 'up_not_suggested_plugins', 'not_display_suggested_plugins' );