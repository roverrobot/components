<?php

require_once dirname(__FILE__) . '/../lib/action_manager.php';

class action_plugin_components_action extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this,
                                   'act');
    }

    function act(&$event, $param) {
        $action_manager = Doku_Action_Manager::manager();
        if ($action_manager && $action_manager->act($event->data))
            $event->preventDefault();
    }
}
