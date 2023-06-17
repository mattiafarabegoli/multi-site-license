/**
 * Class to manage button handlers.
 */
class ButtonHandler {
	
	/**
	 * Constructor to declare all handlers.
	 */
  	constructor($) {
    	this.setupClickHandlers($);
  	}

	/**
	 * Function to declare all handlers.
	 */
  	setupClickHandlers($) {
    var self = this;

	/**
	 * Insert license key button.
	 */
    $('.insert-license-key-button').on('click', function(e) {
      self.handleInsertLicenseKeyClick(e, $);
    });

    /**
	 * Download site updates button.
	 */
    $('.download-site-updates-button').on('click', function(e) {
      self.handleDownloadSiteUpdatesClick(e, $);
    });

    /**
	 * Download UP Plugin updates.
	 */
    $('.download-up-updates-button').on('click', function(e) {
      self.handleDownloadUpUpdatesClick(e, $);
    });

    /**
	 * Update all plugins button.
	 */
    $('#update-all-plugins').submit(function(e) {
      self.handleUpdateAllPluginsSubmit(e, $);
    });

    /**
	 * Update theme button.
	 */
    $('.update-theme-button').click(function(e) {
      self.handleUpdateThemeClick(e, $);
    });

    /**
	 * Update wordpress button.
	 */
    $('.update-wordpress-button').click(function(e) {
      self.handleUpdateWordpressClick(e, $);
    });

    /**
	 * Download suggested plugins button.
	 */
    $('.download-suggested-plugins-button').click(function(e) {
      self.handleDownloadSuggestedPluginsClick(e, $);
    });
  }

  	/**
	 * Insert license key handler.
	 */
  	handleInsertLicenseKeyClick(event, $) {
		var license = $('.license-key-form').val();
		
		this.displayStartMessage("Verifica della chiave di licenza in corso...", event, $);
		
		if (!license) {
			this.displayErrorMessage("Inserisci una chiave di licenza per utilizzare questo plugin", $);
		} else {
			var requestData = {
				action: 'check_license_input',
				license: license
			};

			this.makeAjaxRequest(requestData, function(response) {
				var result = JSON.parse(response);
				console.log(result.success);
				if (result.success == true) {
					this.displaySuccessMessage("La chiave di licenza inserita è corretta, aggiorna la pagina per visualizzare la dashboard", $);
				} else {
					this.displayErrorMessage("La chiave di licenza inserita non è valida", $);
				}
			}, $);
		}
  	}

  	/**
	 * Download site updates handler.
	 */
  	handleDownloadSiteUpdatesClick(event, $) {
		this.displayStartMessage("Download degli aggiornamenti del sito in corso, perfavore attendere senza ricarica la pagina...", event,  $);
		
    	var requestData = {
      		action: 'download_site_updates'
    	};

    	this.makeAjaxRequest(requestData, function(response) {
      		var result = JSON.parse(response);

      		if (result.success) {
				this.displaySuccessMessage(result.message, $);
			} else {
				this.displayErrorMessage(result.message, $);
			}
    	}, $);
  	}

  	/**
	 * Download UP Plugin updates handler.
	 */
  	handleDownloadUpUpdatesClick(event, $) {
		this.displayStartMessage("Download degli aggiornamenti del plugin Updates Plugin in corso, perfavore attendere senza ricarica la pagina...", event,  $);
    var requestData = {
      action: 'download_and_update_updates_plugin'
    };

    this.makeAjaxRequest(requestData, function(response) {
      var result = JSON.parse(response);

      if (result.success) {
        this.displaySuccessMessage(result.message, $);
      } else {
        this.displayErrorMessage(result.message, $);
      }
    }, $);
  }

  	/**
   	 *  Update all plugins handler.
   	 */
  	handleUpdateAllPluginsSubmit(event, $) {
		event.preventDefault();

    	var requestData = {
		  	action: 'update_all_plugins',
		  	update_all_plugins_nonce: $('#update_all_plugins_nonce').val()
		};
		this.displayStartMessage("Download degli aggiornamenti dei plugin in corso, perfavore attendere senza ricarica la pagina...", event,  $);

    	this.makeAjaxRequest(requestData, function(response) {
      		var result = JSON.parse(response);
			$( '#update-all-plugins' ).hide();
      		if (result.success) {
        		this.displaySuccessMessage(result.message, $);
      		} else {
        		this.displayErrorMessage(result.message, $, $);
      		}
    	}, $);
  	}

  	/**
  	 * Update theme handler
  	 */
  	handleUpdateThemeClick(event, $) {
    	event.preventDefault();
		this.displayStartMessage("Download degli aggiornamenti del tema in corso, perfavore attendere senza ricarica la pagina...", event,  $);

		var requestData = {
		  	action: 'custom_update_theme'
		};

    	this.makeAjaxRequest(requestData, function(response) {
      		var result = JSON.parse(response);

			  $('.update-theme-button.loader').remove();
			  $('.update-theme-button').text('Completato');

      	if (result.success) {
        	this.displaySuccessMessage(result.message, $);
        	$('.update-theme-button').off('click');
      	} else {
        	this.displayErrorMessage(result.message, $);
      	}
    }, $);
  }

  	/**
	 * Update Wordpress handler.
	 */
  	handleUpdateWordpressClick(event, $) {
    	event.preventDefault();
		this.displayStartMessage("Download degli aggiornamenti di Wordpress in corso, perfavore attendere senza ricarica la pagina...", event,  $);

    	var requestData = {
      		action: 'custom_update_wordpress'
    	};

    	this.makeAjaxRequest(requestData, function(response) {
      		var result = JSON.parse(response);

			$('.update-wordpress-button.loader').remove();
			$('.update-wordpress-button').text('Completato');

		  	if (result.success) {
				this.displaySuccessMessage(result.message, $);
				$('.update-wordpress-button').off('click');
		  	} else {
				this.displayErrorMessage(result.message, $);
		  	}
    	}, $);
  	}

  	/**
  	 * Download suggested plugins handler.
  	 */
  	handleDownloadSuggestedPluginsClick(event, $) {
    	event.preventDefault();
		this.displayStartMessage("Download dei plugin suggeriti in corso, perfavore attendere senza ricarica la pagina...", event,  $);
    	$(this).html('<div class="loader"></div>');

    	var requestData = {
      		action: 'custom_download_plugins'
    	};

    	this.makeAjaxRequest(requestData, function(response) {
      		var result = JSON.parse(response);

      		if (result.success) {
        		this.displaySuccessMessage(result.message, $);
      		} else {
        		this.displayErrorMessage(result.message, $);
      		}
    	}, $);
  	}

  	/**
  	 * Method to make an AJAX request.
  	 */
  	makeAjaxRequest(requestData, successCallback, $) {
    	$.ajax({
      		url: up_ajax.ajax_url,
      		method: 'POST',
      		data: requestData,
      		success: successCallback.bind(this),
      		error: function(xhr, status, error) {
        		this.displayErrorMessage("Si è verificato un errore nella richiesta! Riprova o contatta l'assistenza per una rilevazione", $);
      		}.bind(this)
    	});
  	}

	/**
	 * Method to display success message.
	 */
  	displayStartMessage(message, event, $) {
    	$('.result-message-dashboard').css('color', '#3c434a');
    	$('.result-message-dashboard').text(message);
		if (!$(event.target).hasClass('insert-license-key-button')) {
			$(event.target).html('<div class="loader"></div>');
		}
  	}
	/**
	 * Method to display success message.
	 */
  	displaySuccessMessage(message, $) {
    	$('.result-message-dashboard').css('color', 'green');
    	$('.result-message-dashboard').text(message);
		$('.loader').hide();
  	}
	
	/**
	 * Method to display error message.
	 */
  	displayErrorMessage(message, $) {
		$('.result-message-dashboard').css('color', 'red');
		$('.result-message-dashboard').text(message);
		$('.loader').hide();
  	}
}

// Inizializzazione della classe al caricamento del documento
jQuery(document).ready(function($) {
  	new ButtonHandler($);
});