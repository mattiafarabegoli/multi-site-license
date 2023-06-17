<?php
class HTTPRequests {
	
	private $referer;
	
	function __construct(){
		$this->referer = parse_url(get_home_url(), PHP_URL_HOST);
	}
	
    public function postRequest($url, $data) {
        $this->referer = parse_url(get_home_url(), PHP_URL_HOST);
        $args = array(
            'headers' => array(
                'Referer' => $this->referer,
            ),
            'body' => $data,
        );
        $response = wp_remote_post($url, $args);
        
        return $response;
    }
	
	public function getResponseType($response){
		return wp_remote_retrieve_header($response, 'content-type');
	}
	
	public function getResponseCode($response){
		return wp_remote_retrieve_response_code($response);
	}

    // Metodo per verificare il tipo di risposta HTTP
    public function checkResponseType($response, $expectedType) {
        $contentType = wp_remote_retrieve_header($response, 'content-type');
        return $this->check_response_type($contentType, $expectedType);
    }

    // Metodo per verificare il codice di risposta HTTP
    public function checkResponseCode($response) {
        $responseCode = wp_remote_retrieve_response_code($response);
        return $this->check_response_code($responseCode);
    }
	
	private function check_response_type($content_type, $response_content_type){
		if ( $content_type && strpos( $content_type, $response_content_type ) !== false ) {
			return true;
		} else {
			return false;
		}
	}
	
	private function check_response_code( $response_code ){
		if ( $response_code && $response_code === 200 ) {
			return true;
		} else {
			return false;
		}
	}	
}
