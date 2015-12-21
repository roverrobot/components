<?php

require_once dirname(__FILE__) . '/component_manager.php';
require_once dirname(__FILE__) . '/ajax.php';

// the dir holding ajax handlers under the plugin dir structure.
// can be in its subdirs
define(DOKU_AJAX_ROOT, '/ajax');

/**
 * The manager for AJAX handler components
 * @author Junling Ma <junlingm@gmail.com>
 */
class Doku_AJAX_Manager extends Doku_Component_Manager {
	private $handlers = array();

    /**
     * handles a new class that is loaded in
     * @param string $class the name of the new class.
     */
	protected function handle($class) {
		if (is_subclass_of($class, 'Doku_AJAX')) {
			$handler = new $class;
			$this->handlers[$handler->name()] = $handler;
		}
	}

	/**
	 * call the given ajax function
	 * the ajax function function name must be plugin_name.function_name.
	 * @param string $call the name fo the AJAX call;
	 * @return bool whether the call has been make (regardless of being successful).
	 */
	public function call($call) {
		$components = explode('.', $call);
		if (count($components) <= 1) return FALSE;
		$plugin = array_shift($components);
		$call = implode('.', $components);
		$path = DOKU_PLUGIN  . $plugin . DOKU_AJAX_ROOT;
		$this->load($path, $call);
		foreach($this->handlers as $handler)
			if ($handler->name() == $call) {
				$handler->handle();
				// if we get to here, the hander should have exited.
				// but just in case
				return TRUE;
			}
		return FALSE;
	}
}