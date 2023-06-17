<?php

class jsonCreator {
	
	public static function createSuccessJson($message) {
        $response = array(
            'success' => true,
            'message' => $message
        );

        return json_encode($response);
    }
	
    public static function createErrorJson($message) {
        $response = array(
            'success' => false,
            'message' => $message
        );

        return json_encode($response);
    }
}