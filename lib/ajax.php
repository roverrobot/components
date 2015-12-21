<?php

class DokuAJAXColidingParamException extends Exception {
	private $param = '';
	public function param() { return $this->param; }
	public function __construct($param) { $this->param = $param; }
}


/**
 * The base class for all AJAX handlers
 * @author Junling Ma <junlingm@gmail.com>
 */
abstract class Doku_AJAX {
	private $required = array();
	private $optional = array();

	/**
	 * returns the name of the component
	 * Note different types of components can have identical names,
	 * but components of the same type cannot.
	 * @return string the name of the component.
	 */
	abstract public function name();

	/**
	 * return an error mesage to the client
	 * @param numeric $code the error code;
	 * @param string msg (optional) the error message
	 */ 
	protected function error($code, $msg='') {
		if (function_exists('http_response_code') && !$msg) {
			http_response_code($code);
			exit;
		}
		if (!$msg) {
	        switch ($code) {
	            case 200: $msg = 'OK'; break;
	            case 400: $msg = 'Bad request'; break;
	            case 401: $msg = 'Unauthorized'; break;
	            case 403: $msg = 'Forbidden'; break;
	            case 404: $msg = 'Not Found'; break;
	            case 416: $msg = 'Requested Range Not Satisfiable'; break;
	            default: $code = 200; $msg = "OK"; break;
        	}
        }
        header("HTTP/1.0 $code $msg");
        exit;
	}

	/**
	 * send the result back to the client and exit
	 * @param mixed $result the result to send back to the client
	 */
	protected function respond($result) {
		header('Content-Type: application/json');
		$json = new JSON();
		echo $json->encode($result);
		exit;
	}

	/**
	 * check whether the caller has enough auth level
	 * @param array params the array of aprameters for this call,
	 * with the keys as the parameter names and the values as the parameter values
	 * @return bool whether the call is authorized
	 */
	abstract protected function auth($params);

	/**
	 * call the ajax function
	 * @param array $params the array of parameters passed in,
	 * with the keys as parameter names and the value as their values;
	 * @return mixed the value to be returned to the client;
	 */
	abstract protected function call($params);

	/**
	 * the default constructor
	 * @param array $required (optional) array of required parameters with 
	 * the variable name as the key and its type as the value.
	 * @param array $optional (optional) array of optional parameters with
	 * the variable name as the key and its type as the value.
	 */
	public function __construct($required=array(), $optional=array()) {
		$this->required = $required;
		$this->optional = $optional;
	}

	/**
	 * handles the call
	 */
	public function handle() {
		$params = array();
		$types = array();
		foreach ($this->required as $var => $type) {
			$params[$var] = TRUE;
			$types[$var] = $type;
		}
		foreach ($this->optional as $var => $type) {
			if (isset($vars[$var])) throw new DokuAJAXColidingParamException($var);
			$params[$var] = FALSE;
			$types[$var] = $type;
		}
		$vars = array();

		global $INPUT;
		// check security token
		$sectok = $INPUT->str('sectok');
		if (!checkSecurityToken($sectok))
			$this->error(403);
		// check the presence of parameters
		$json = new JSON;
		foreach ($params as $var => $req) {
			if ($INPUT->has($var)) {
				$value = $INPUT->param($var);
				if (is_array($value)) $type = 'array';
				else if (is_string($value)) {
					if ($types[$var] == 'array') {
						$value = $json->decode($value);
						$type = is_array($value) ? 'array' : NULL;
					} else $type = 'string';
				}
				else if (is_integer($value)) $type = 'int';
				else if (is_float($value)) $type = 'float';
				else if (is_bool($value)) $type = 'bool';
				else $type = NULL;
				if ($type !== $types[$var]) {
					$this->error(400, $var . ' is expected to be ' . $types[$var] . ' : '.$type);
				}
				$vars[$var] = $value;
			} else if ($req) $this->error(400);
		}

		if (!$this->auth($vars)) $this->error(403);
		$this->respond($this->call($vars));
	}
}