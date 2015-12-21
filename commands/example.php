<?php

// this exampel shows how to handle an action, and render the results.
class Components_Action_Example extends Doku_Action {
	/** action() should return the name of the action that this handler
     *  can handle, e.g., 'edit', 'show', etc.
     * note that the actual command is some_plugin.some_command, for example,
     * this action is components.example
     */
    public function action() { return 'example'; }

    /** permission_required() should return the permission level that
     *  this action needs, e.g., 'AUTH_NONE', 'AUTH_READ', etc.
     */
    public function permission_required() { return AUTH_READ; }

    /** handle() method perform the action, 
     *  and return a command to be passed to
     *  the main template to display the result.
     *  If there should be no change in action name, 
     *  the return value can be omitted.
     */
    public function handle() {
    	global $EXAMPLE_TAG;
    	global $INPUT;
    	$EXAMPLE_TAG = $INPUT->str('tag', 'pre');
    }
}

// an example post processor, which alters the renderer
// here the 'pre' and 'div' tags are switched.
class Component_Action_PostProcessor extends Doku_Action_Postprocessor {
    /**
     * Specifies the action name that this process responds to
     *
     * @return string the action name
     */
    public function action() { return 'example'; }

    /**
     * process the global data that has been handled by the action handler
     */
    public function process() {
    	global $EXAMPLE_TAG;
    	if ($EXAMPLE_TAG == 'pre') $EXAMPLE_TAG = 'div';
    	else if ($EXAMPLE_TAG == 'div') $EXAMPLE_TAG = 'pre';
    }
}

class Components_Action_Renderer_Example extends Doku_Action_Renderer {
	/** action() should return the name of the action that this handler
     *  can handle, e.g., 'edit', 'show', etc.
     * note that the actual command is some_plugin.some_command, for example,
     * this action is components.example
     */
    public function action() { return 'example'; }

    /**
     * renders the xhtml output of an action
     * note that you can define Doku_Action_Postprocessor subclasses to change
     * the global data that is used here, e.g., $ID and $EXAMPLE_TAG,
     */
    public function xhtml() {
    	global $ID;
    	global $EXAMPLE_TAG;
    	$text = htmlspecialchars(rawWiki($ID));
    	echo '<' . $EXAMPLE_TAG . '>' . $text . '</' . $EXAMPLE_TAG . '>';
    }
}
