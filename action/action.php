<?php

require_once dirname(__FILE__) . '/../lib/action_manager.php';
require_once DOKU_PLUGIN . 'action.php';

class action_plugin_projects_action extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this,
                                   'act');
    }

    function act(&$event, $param) {
        global $ACTION_MANAGER;
        if (!$ACTION_MANAGER) $ACTION_MANAGER = new Doku_Action_Manager();
        if ($ACTION_MANAGER && $ACTION_MANAGER->act($event->data))
            $event->preventDefault();
    }
}
