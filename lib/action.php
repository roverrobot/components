<?php

define(DOKU_ACTIONS_ROOT, dirname(__FILE__) . '/../commands');

/**
 * These handlers are called right before an action is handled, so that
 * plugins have a change to change the data that is used by an action handler
 * 
 * A preprocessor that inherits from a parent preprocessor will replace the
 * parent.
 * 
 * Multiple preprocessers can be defined, the order that these processors
 * are called us unpredictable. So, to ensure that a preprocessor A should
 * be called before another one B, A should inherit from B, and then calls
 * B's process().
 * 
 * @author Junling Ma <junlingm@gmail.com>
 */
abstract class Doku_Action_Preprocessor {
    /**
     * Specifies the action name that this process responds to
     *
     * @return string the action name
     */
    abstract public function action();

    /**
     * process the global data that will be passed to the action handler
     */
    abstract public function process();
}

/**
 * These handlers are called after an action is handled, but before an action
 * is rendered, so that the data that an action renders
 * 
 * A postprocessor that inherits from a parent preprocessor will replace the
 * parent. So, to ensure that a postprocessor A should
 * be called before another one B, A should inherit from B, and then calls
 * B's process().
 * 
 * Multiple postprocessers can be defined, the order that these processors
 * are called us unpredictable. 
 *
 * @author Junling Ma <junlingm@gmail.com>
 */
abstract class Doku_Action_Postprocessor {
    /**
     * Specifies the action name that this process responds to
     *
     * @return string the action name
     */
    abstract public function action();

    /**
     * process the global data that has been handled by the action handler
     */
    abstract public function process();
}

/**
 * These renderers renders the output of an action.
 * If a renderer class is extended, then the subclass replaces the parent
 * as the renderer. Two subclasses of a the same parent renderer will cause a
 * conflict, and which renderer wins out is not unpredictable.
 * 
 * @author Junling Ma <junlingm@gmail.com>
 */
abstract class Doku_Action_Renderer {
    /**
     * Specifies the action name that this process responds to
     *
     * @return string the action name
     */
    abstract public function action();

    /**
     * renders the xhtml output of an action
     */
    abstract public function xhtml();
}

/**
 * Doku_Action class is the parent class of all actions. 
 * It has two interfaces: 
 *   - a static one that acts as action handler managers
 *     * act($action_name) to handle an action;
 *     * render($action_name) to render the output of an action.
 *   - an interface that specifies what each action should implement, namely
 *     * action() returning the action name;
 *     * permission_required() returning the permission level for the action;
 *     * handle() as the action handler;
 *
 * We require that actions are defined as subclasses of Doku_Action, and if
 * a class is extended, then the subclass replaces the parent as a handler.
 * Two subclasses of a the same parent handler will cause a conflict, and
 * which handler wins out is not unpredictable.
 * 
 * The action definitions are put in a file with the same name as the action
 * in the inc/commands folder, and a plugin's commands folder (to avoid
 * conflicts with the action (event_handler) plugins
 *
 * @author Junling Ma <junglingm@gmail.com> 
 */
abstract class Doku_Action
{
    /** action() should return the name of the action that this handler
     *  can handle, e.g., 'edit', 'show', etc.
     */
    abstract public function action();

    /** permission_required() should return the permission level that
     *  this action needs, e.g., 'AUTH_NONE', 'AUTH_READ', etc.
     */
    abstract public function permission_required();

    /** handle() method perform the action, 
     *  and return a command to be passed to
     *  the main template to display the result.
     *  If there should be no change in action name, 
     *  the return value can be omitted.
     */
    abstract public function handle();
}
