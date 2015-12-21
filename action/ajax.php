<?php
/**
 * projects Action Plugin: hajacking the modification of metadata
 * it cleears the persistent metadata 'projectfile' if the file in the page was removed.
 *
 * @author     Junling Ma <junlingm@gmail.com>
 */
 
require_once DOKU_PLUGIN . 'action.php';
require_once dirname(__FILE__) . '/../lib/ajax_manager.php';

class action_plugin_components_ajax extends DokuWiki_Action_Plugin { 
    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,
                                   'call');
    }

	// the matedata has been rendered 
    function call(&$event, $param) {
        $manager = new Doku_AJAX_Manager();
        if ($manager && $manager->call($event->data))
            $event->preventDefault();
	}
		
}
