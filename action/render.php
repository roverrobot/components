<?php

require_once dirname(__FILE__) . '/../lib/action_manager.php';
require_once DOKU_PLUGIN . 'action.php';


class action_plugin_projects_render extends DokuWiki_Action_Plugin
{
    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this,
                                   'render');
    }

    function render(&$event, $param) {
        global $ACTION_MANAGER;
        if (!$ACTION_MANAGER) return;
        if ($ACTION_MANAGER->render($event->data))
            $event->preventDefault();
    }
}
