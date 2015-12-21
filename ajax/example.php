<?php

/**
 * this is an example AJAX call handler
 * it returns a slice of the dokuwiki page
 * the AJAX call must pass in two parameters: 
 * id (a string) and slice (a stringified JSON object).
 * note that a sectok must also be passed in for logged in users.
 * the class name can be arbitrary, but the name of this script
 * must be the same as the function call, i.e., example.php 
 * respond to the AJAX call componnets.example.
 */

class Components_AJAX_Example extends Doku_AJAX {
	public function __construct() {
		parent::__construct(
			// required parameters
			array(
				'id' => 'string',
				'range' => 'array'
			)
			// no optional parameters
		);
	}

	// the name of the function call
	public function name() { return 'example'; }

	// check if the call is authorized
	// here we require the user to have edit right
	// for these people can see the raw wiki text anyway,
	// so there is no information leak for us.
	protected function auth($params) {
		$id = cleanID($params['id']);
		return auth_quickaclcheck($id) >= AUTH_EDIT;
	}

	// make the actuall call, and return the result
	protected function call($params) {
		$id = cleanID($params['id']);
		if (!file_exists(wikiFN($id)))
			// return an error and exit;
			$this->error(404);
		$range = $params['range'];
		if (!isset($range['from']) || !isset($range['to'])) 
			// an error with a specific message, and exit
			$this->error(400, "range must have a from proterty and a to property");
		// return the slice
		return rawWikiSlices($range['from'] . '-' . $range['to'], $id)[1];
	}
}