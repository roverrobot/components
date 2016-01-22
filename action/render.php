<?php

require_once dirname(__FILE__) . '/../lib/action_manager.php';
require_once DOKU_PLUGIN . 'action.php';


class action_plugin_components_render extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this,
                                   'render');
    }

    function render(&$event, $param) {
        $action_manager = Doku_Action_Manager::manager();
        if ($action_manager && $action_manager->render($event->data))
            $event->preventDefault();
    }
}
